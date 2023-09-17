<?php

namespace Thiktak\FilamentAcquaintances;

use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Thiktak\FilamentAcquaintances\Filament\Pages\UserAcquaintances;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource;

class FilamentAcquaintancesPlugin implements Plugin
{
    public array $configureUserProfileTrends = [
        'userPage' => true,
        'showActivitiesGraph' => true,
        'showTimeline' => true,
        'showPopular' => true,
        'popularAllowAllModels' => true,
        'popularAllowModels' => [],
    ];

    public array $configureUserProfileFavorites = [
        'userPage' => true,
    ];

    public array $configureUserProfileFollowings = [
        'userPage' => true,
    ];

    public array $configureUserProfileSubscriptions = [
        'userPage' => true,
    ];

    public array $configureUserProfileFriends = [
        'userPage' => true,
    ];

    public function getId(): string
    {
        return 'filament-acquaintances';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                //UserAcquaintances::class, // This one works fine with UserAcquaintances::getUrl()
            ])
            ->resources([
                UserAcquaintanceResource::class,
            ])
            /*->discoverResources(
                in: app_path('../vendor/thiktak/filament-acquaintances/src/Filament/Resources/'),
                for: 'Thiktak\\FilamentAcquaintances\\Resources'
            )
            //*/;
        //dd(glob(app_path('../vendor/thiktak/filament-acquaintances/src/Filament/Resources/*')));
    }

    public function boot(Panel $panel): void
    {
        $panel
            ->userMenuItems([
                MenuItem::make('acquaintances')
                    ->label('User profile')
                    ->url(fn () => UserAcquaintanceResource::getUrl('view', ['record' => auth()->id()])) //UserAcquaintanceResource\Pages\ViewUser::getRouteName())
                    ->icon('heroicon-o-user'),
            ]);

        //->url(UserAcquaintances::getUrl())

        //dd(UserAcquaintanceResource::getRouteBaseName(), UserAcquaintanceResource::getUrl());
        /*dd(
            UserAcquaintanceResource::getRouteBaseName(),
            UserAcquaintances::getRouteName()
        ); //*/
        //dd(UserAcquaintanceResource::getUrl());
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

    public function config(string $dot = null): mixed
    {
        return $dot ? data_get($this, $dot) : $this;
    }

    public function configureUserProfileTrends(bool $userPage = true, $popularAllowAllModels = true, array $popularAllowModels = []): static
    {
        $this->configureUserProfileTrends['userPage'] = $userPage;
        $this->configureUserProfileTrends['popularAllowAllModels'] = (bool) $popularAllowAllModels;

        $this->configureUserProfileTrends['popularAllowModels'] = (array) $popularAllowModels;

        if (count($this->configureUserProfileTrends['popularAllowModels'])) {
            $this->configureUserProfileTrends['popularAllowAllModels'] = false;
        }

        return $this;
    }
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
