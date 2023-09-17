<?php

namespace Thiktak\FilamentAcquaintances;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

//use Thiktak\FilamentAcquaintances\Commands\SkeletonCommand;
//use Thiktak\FilamentAcquaintances\Testing\TestsSkeleton;

class FilamentAcquaintancesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'thiktak-filament-acquaintances';

    public static string $viewNamespace = 'thiktak-filament-acquaintances';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('thiktak/filament-acquaintances');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        /*if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/skeleton/{$file->getFilename()}"),
                ], 'skeleton-stubs');
            }
        }*/

        // Testing
        //Testable::mixin(new TestsSkeleton());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'thiktak/filament-acquaintances';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('skeleton', __DIR__ . '/../resources/dist/components/skeleton.js'),
            //Css::make('skeleton-styles', __DIR__ . '/../resources/dist/skeleton.css'),
            //Js::make('skeleton-scripts', __DIR__ . '/../resources/dist/skeleton.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            //SkeletonCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            //'create_skeleton_table',
        ];
    }

    public function boot()
    {

        /*dd(
            file_get_contents('https://api.github.com/repos/filamentphp/filamentphp.com/contents/content/plugins')
        );//*/

        \Filament\Tables\Columns\Column::macro('transformation', function ($model = true, array $only = null) {

            $only = collect($only ?: ['*']);

            if (is_subclass_of($model, \Illuminate\Database\Eloquent\Model::class) || $model === true) {

                $methods['description'] = [
                    'intersect' => ['*', 'description'],
                    'methodObject' => 'description',
                    'methodModel' => 'getFilamentDescription',
                ];

                $methods['url'] = [
                    'intersect' => ['*', 'url'],
                    'methodObject' => 'url',
                    'methodModel' => 'getFilamentUrl',
                ];

                $methods['icon'] = [
                    'intersect' => ['*', 'icon'],
                    'methodObject' => 'icon',
                    'methodModel' => 'getFilamentIcon',
                ];

                // Always last
                $methods['label'] = [
                    'intersect' => ['*', 'label'],
                    'methodObject' => 'getStateUsing',
                    'methodModel' => 'getFilamentLabel',
                ];

                /*if (method_exists($model, 'getFilamentDescription') && $only->intersect(['*', 'description'])->count()) {
                    $this->description(fn (Model $record) => $record->getFilamentDescription());
                }*/

                /*if (method_exists($model, 'getFilamentLabel') && $only->intersect(['*', 'label'])->count()) {
                    $this->getStateUsing(fn (Model $record) => $record->getFilamentDescription());
                }*/

                foreach ($methods as $methodKey => $method) {
                    if ($only->intersect($method['intersect'])->count() && method_exists($this, $method['methodObject'])) {

                        $a = clone $this;

                        $this->{$method['methodObject']}(function (Model $record) use ($method, $methodKey, $a) {
                            $a->record($this->getRecord());
                            $state = $a->getState();
                            //dd($state, $this->getStateFromRecord());
                            //$state = $this->getStateFromRecord(); //$methodKey == 'label' ? $this->getStateFromRecord() : $this->getState();
                            //dd($this, $state, $this->getState());
                            if ($state instanceof \Illuminate\Database\Eloquent\Collection) {
                                if ($methodKey == 'label') {
                                    return $state
                                        ->map(function ($state) {
                                            if ($state instanceof Model) {
                                                if (method_exists($state, 'getFilamentLabel')) {
                                                    return call_user_func([$state, 'getFilamentLabel']);
                                                }
                                            }

                                            return $state;
                                        });
                                }
                            } elseif ($state instanceof Model) {
                                if (method_exists($state, $method['methodModel'])) {
                                    return call_user_func([$state, $method['methodModel']]);
                                }
                            } elseif ($record instanceof Model) {
                                // dd($method, $record, $method['methodModel'], method_exists($record, $method['methodModel']));
                                if (method_exists($record, $method['methodModel'])) {
                                    return call_user_func([$record, $method['methodModel']]);
                                }
                            }

                            /*if (isset($record->{$this->getName()})) {
                                return $record->{$this->getName()};
                            }*/
                            return $methodKey == 'label' ? $state : null; //$state;
                            //}
                        });
                    }
                }
            }
            /*dd(
                $this,
                func_get_args(),
                is_subclass_of($model, \Illuminate\Database\Eloquent\Model::class)
            );*/

            return $this; //static::length($str) == $length;
        });

        parent::boot();
    }
}
