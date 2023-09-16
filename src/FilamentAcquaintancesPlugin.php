<?php

namespace Thiktak\FilamentAcquaintances;

use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Thiktak\FilamentAcquaintances\Filament\Pages\UserAcquaintances;

class FilamentAcquaintancesPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-acquaintances';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                UserAcquaintances::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        $panel
            ->userMenuItems([
                MenuItem::make('acquaintances')
                    ->label('User profile')
                    ->url(UserAcquaintances::getUrl())
                    ->icon('heroicon-o-user')
            ]);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    //public function activate
}


/*
        ->activateFriendship(userPage: true, blocable: true, requestable: true)
        ->activateUserProfileSubscriptions(userPage: true)
        ->activateUserProfileSubscribers(userPage: true)
        ->activateUserProfileFavorites(userPage: true)
        ->activateUserProfileHistory(userPage: true, delete)
        ->activateUserProfileTrends(userPage: true) // list of popular objets
        ->trends([
            Item::class,
            Person::class => fn(Model $record) : View => view('...'),
        ])
        ->toStringModel(fn($model) => $model)
        ->toUrlModel(fn($model) => '#' . $model->id)

*/