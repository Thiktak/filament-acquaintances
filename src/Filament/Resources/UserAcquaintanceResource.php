<?php

namespace Thiktak\FilamentAcquaintances\Filament\Resources;

use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages\FavoritesUser;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages\FollowingsUser;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages\FriendsUser;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages\SubscriptionsUser;
use Thiktak\FilamentAcquaintances\FilamentAcquaintancesPlugin;

class UserAcquaintanceResource extends Resource
{
    protected static ?string $model = User::class;

    public static function sidebar(Model $record): FilamentPageSidebar
    {
        $items = [];

        if (FilamentAcquaintancesPlugin::get()->config('configureUserProfileTrends.userPage')) {
            $items[] = NavigationItem::make('trends')
                ->label(fn () => 'Trends')
                ->url(fn () => static::getUrl('view', ['record' => $record->id]))
                ->icon('heroicon-o-bookmark')
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName() . '.view'));
        }

        $items = [
            ...$items,
            ...FavoritesUser::getSidebarNavigationItem($record),
            ...SubscriptionsUser::getSidebarNavigationItem($record),
            ...FollowingsUser::getSidebarNavigationItem($record),
            ...FriendsUser::getSidebarNavigationItem($record)
        ];


        return FilamentPageSidebar::make()
            ->setTitle($record->name)
            ->setDescription($record->email)
            ->setNavigationItems($items);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
            'friends' => Pages\FriendsUser::route('/{record}/friends'),
            'favorites' => Pages\FavoritesUser::route('/{record}/favorites'),
            'subscriptions' => Pages\SubscriptionsUser::route('/{record}/subscriptions'),
            //'followers' => Pages\FollowersUser::route('/{record}/followers'),
            'followings' => Pages\FollowingsUser::route('/{record}/followings'),
        ];
    }

    static public function getHelperTitle(User $record, $page): string
    {
        return $record->name . ' (' . $page . ')';
    }
}
