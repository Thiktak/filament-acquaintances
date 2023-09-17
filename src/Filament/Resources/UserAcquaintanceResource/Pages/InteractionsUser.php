<?php

namespace Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages;

use App\Models\Matrix\Item;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Get;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Multicaret\Acquaintances\Models\InteractionRelation;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource;
use Thiktak\FilamentAcquaintances\FilamentAcquaintancesPlugin;

abstract class InteractionsUser extends Page
{
    //use InteractsWithTable, InteractsWithFormActions;
    //use HasPageSidebar;

    public static string $relationQueryName;

    public static string $relationName;

    protected static string $view; // = 'thiktak-filament-acquaintances::filament.resources.user-acquaintances.index';

    protected static string $resource; // = UserAcquaintanceResource::class;

    public User $record;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }

    public static function getRecordTableQuery(Model $record)
    {
        /*auth()->user()->favorite(User::find(rand(1, 9)));
        auth()->user()->favorite(Item::find(rand(1, 15)));*/

        return InteractionRelation::query()
            ->where('user_id', $record->id)
            ->where('relation', '=', static::$relationQueryName);
    }

    protected function getTableQuery()
    {
        return static::getRecordTableQuery($this->record);
    }

    public function getTableColumns(): array
    {
        $user = auth()->id();

        return [
            TextColumn::make('subject_type')
                ->getStateUsing(function ($record) {
                    if (method_exists($record->subject_type, 'scopeToTitle')) {
                        return $record->subject->scopeToTitle();
                    }

                    return sprintf('%s (#%s)', $record->subject_type, $record->subject_id);
                })
                ->description(function ($record) {
                    if (method_exists($record->subject_type, 'scopeToDescription')) {
                        return $record->subject->scopeToDescription();
                    } elseif (method_exists($record->subject_type, '__toString')) {
                        return $record->subject;
                    }

                    return '';
                })
            //->label(fn (MorphPivot $state) => dd(func_get_args()) . sprintf('%s (#%s)', $record->subject_type, $record->subject_id)) // @TODO: label dynamic
            ,
            TextColumn::make('relation'),
            TextColumn::make('relation_value'),
            TextColumn::make('relation_type'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->groups([])
            ->pushActions([]);
    }

    public function getTitle(): string | Htmlable
    {
        return static::$resource::getHelperTitle($this->record, static::$relationName);
    }

    public static function getConfig($dot = ''): mixed
    {
        return FilamentAcquaintancesPlugin::get()->config('configureUserProfile' . ucfirst(static::$relationName) . '.' . $dot);
    }

    public static function getSidebarNavigationItem(Model $record): array
    {
        if (! static::getConfig('userPage')) {
            return [];
        }

        return [
            NavigationItem::make(static::$relationName)
                ->label(fn () => ucfirst(static::$relationName))
                ->badge(fn () => static::getRecordTableQuery($record)->count())
                ->url(fn () => static::getResource()::getUrl(static::$relationName, ['record' => $record->id]))
                ->icon(static::$navigationIcon)
                ->isActiveWhen(fn () => request()->routeIs(static::getResource()::getRouteBaseName() . '.' . static::$relationName . '*')),
        ];
    }
}
