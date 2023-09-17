<?php

namespace Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages;

use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource;

class FriendsUser extends Page implements HasTable
{
    use HasPageSidebar;
    use InteractsWithFormActions;
    use InteractsWithTable;

    protected static string $view = 'thiktak-filament-acquaintances::filament.resources.user-acquaintances.index';

    protected static string $resource = UserAcquaintanceResource::class;

    public User $record;

    public array $requestStatus = [
        'pending' => 'info',
        'accepted' => 'success',
        'denied' => 'warning',
        'blocked' => 'danger',
    ];

    public static function getSidebarNavigationItem(Model $record): array
    {
        $groups = collect(config('acquaintances.friendships_groups'))
            ->map(function ($order, $group) {
                return collect([
                    'key' => $group,
                    'label' => $group,
                    'order' => $order,
                ]);
            })
            ->add(collect([
                'key' => '',
                'label' => 'All',
                'order' => -1,
            ]))
            ->sortBy('order');

        $items[] = NavigationItem::make('friends')
            ->label(fn () => 'Friends')
            ->url(fn () => static::getResource()::getUrl('friends', ['record' => $record->id]))
            ->icon('heroicon-o-user-group')
            ->group('friends')
            ->isActiveWhen(fn () => request()->routeIs(static::getResource()::getRouteBaseName() . '.friends*'));

        if (request()->routeIs(static::getResource()::getRouteBaseName() . '.friends*')) {
            foreach ($groups as $groupKey => $group) {
                $items[] = NavigationItem::make('friends_' . $groupKey)
                    ->label(fn () => '=> ' . $group->get('label'))
                    ->badge(fn () => $record->getFriendsCount($group->get('key')))
                    ->url(fn () => static::getResource()::getUrl('friends', ['record' => $record->id, 'group' => $group->get('key')]))
                    ->group('friends')
                    ->isActiveWhen(fn () => request()->get('group') == $group->get('key'));
            }
        }

        return $items;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->model(\Multicaret\Acquaintances\Models\Friendship::class)
                ->form([
                    Select::make('user_id')
                        ->searchable()
                        ->multiple()
                        ->getSearchResultsUsing(fn (string $search): array => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                        ->getOptionLabelUsing(fn (array $values): ?string => User::whereIn('id', $values)?->name)
                        ->required(),
                    // ...
                ])
                ->using(function (array $data, string $model) {
                    foreach ((array) ($data['user_id'] ?? []) as $userId) {
                        $user = User::find($userId);
                        if ($user) {
                            if (! $user->hasFriendRequestFrom($this->record)) {
                                $this->record->befriend($user);

                                Notification::make()
                                    ->success()
                                    ->title('Request sent')
                                    ->body(sprintf('We sent a friendship request to %s', $user))
                                    ->send();
                            } else {
                                Notification::make()
                                    ->warning()
                                    ->title('Ask again ?')
                                    ->body(sprintf('You already sent a friendship request to %s', $user))
                                    ->send();
                            }
                        }
                    }
                })
                ->successNotification(null),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }

    protected function getTableQuery()
    {
        //dd($this->item, $this->item->ancestors);
        return $this->record
            ->findFriendships(
                groupSlug: request()->get('group') ?? ''
            )
            ->where('sender_type', User::class)
            ->where('recipient_type', User::class)
            ->with('groups');
    }

    public function getTableColumns(): array
    {
        $user = auth()->id();

        return [
            TextColumn::make('user')
                ->getStateUsing(function (Model $record) use ($user) {
                    return match ($record->sender_id == $user) {
                        true => $record->recipient,
                        false => $record->sender
                    };
                })
                ->description(function (Model $record) use ($user) {
                    return match ($record->sender_id == $user) {
                        true => 'You requested',
                        false => 'you have been requested'
                    };
                }),

            TextColumn::make('groups')
                ->getStateUsing(function (Model $record) {
                    $groups = array_flip(config('acquaintances.friendships_groups'));

                    return $record->groups
                        ->pluck('group_id')
                        ->map(fn ($id) => $groups[$id] ?? $id);
                }),

            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'info',
                    'accepted' => 'success',
                    'denied' => 'warning',
                    'blocked' => 'danger',
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->groups([
                Group::make('status')
                    ->groupQueryUsing(fn (Builder $query) => $query->groupBy('status'))
                    ->getTitleFromRecordUsing(fn (Model $record): string => ucfirst($record->status))
                    ->getDescriptionFromRecordUsing(fn (Model $record): string => $record->status),
            ])
            ->pushActions([
                Action::make('accept')
                    ->label('Accept')
                    //->visible(fn (Model $record): bool => in_array($record->status, ['pending', 'denied']))
                    ->visible(fn (Model $record) => $this->actionIsVisibleProxy($record, ['pending', 'denied'], isRecipient: true))
                    ->action(function (Model $record) {
                        $record->recipient->acceptFriendRequest($record->sender);
                    }),

                DeleteAction::make('cancel')
                    ->label('cancel')
                    ->visible(fn (Model $record) => $this->actionIsVisibleProxy($record, ['pending'], isSender: true, isRecipient: false)),

                Action::make('deny')
                    ->label('Deny')
                    ->requiresConfirmation()
                    ->visible(fn (Model $record) => $this->actionIsVisibleProxy($record, ['pending'], isSender: false, isRecipient: true))
                    ->action(function (Model $record) {
                        $record->recipient->denyFriendRequest($record->sender);
                    }),

                Action::make('unfriend')
                    ->label('Unfriend')
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => in_array($record->status, ['accepted']))
                    ->action(fn (Model $record) => $record->recipient->unfriend($record->sender)),

                Action::make('unblock')
                    ->label('unblock')
                    ->requiresConfirmation()
                    ->visible(fn (Model $record) => $this->actionIsVisibleProxy($record, ['blocked'], isRecipient: false))
                    ->action(fn (Model $record) => $this->actionProxy($record, 'unblock')),

                Action::make('block')
                    ->label('Block')
                    ->requiresConfirmation()
                    ->visible(fn (Model $record) => $this->actionIsVisibleProxy($record, ['pending', 'accepted', 'denied'], isRecipient: false))
                    ->action(fn (Model $record) => $this->actionProxy($record, 'block')),

            ])
            ->defaultGroup('status');
    }

    public function actionProxy(Model $record, $action)
    {
        $user = auth()->id();
        $other = ($record->sender_id == $user) ? $record->recipient : $record->sender;

        $requestor = ($record->sender_id == $user) ? $record->recipient : $record->sender;
        $requested = ($record->sender_id == $user) ? $record->sender : $record->recipient;

        switch ($action) {
            case 'unblock':
                auth()->user()->unblockFriend($other);

                break;

            case 'block':
                auth()->user()->blockFriend($other);

                break;
        }
    }

    public function actionIsVisibleProxy(Model $record, array $status = [], $isRecipient = true, $isSender = false)
    {
        $user = auth()->id();
        $requestor = ($record->sender_id == $user) ? $record->recipient : $record->sender;
        $requested = ($record->sender_id == $user) ? $record->sender : $record->recipient;

        if ($isRecipient) {
            if ($user != $record->recipient_id) {
                return false;
            }
        }

        if ($isSender) {
            if ($user != $record->sender_id) {
                return false;
            }
        }

        return in_array($record->status, $status);
    }

    /*function (Model $record) {
                    })*/

    /*public function getTabs(): array
    {
        return array_merge(
            [
                'all' => Tab::make(),
            ],
            collect($this->requestStatus)
                ->map(function ($color, $status) {
                    return Tab::make()
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'));
                })
                ->toArray()
        );
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }*/

    public function getTitle(): string | Htmlable
    {
        return self::$resource::getHelperTitle($this->record, 'friends');
    }
}
