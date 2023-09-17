<?php

namespace Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages;

use App\Filament\Matrix\Resources\Matrix\PersonResource;
use App\Jobs\Matrix\ProcessUpdateScores;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource;

class ListUsers extends ListRecords
{
    //use \Thiktak\FilamentBookmarks\Traits\HasPageBookmarks;
    //use \Thiktak\FilamentBookmarks\Traits\HasPageHistory;

    use HasPageSidebar;

    //protected static string $viewSidebar = 'filament.matrix.resources.matrix.person-resource.pages.view-person';
    //protected static string $view = 'filament-page-with-sidebar::proxy'; //filament.matrix.resources.matrix.person-resource.pages.view-person';

    protected static string $viewSidebar = 'thiktak-filament-acquaintances::filament.resources.user-acquaintances.index';

    protected static string $resource = UserAcquaintanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\EditAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }

    /*public function getTitle(): string | Htmlable
    {
        return self::$resource::getHelperTitle($this->record, 'dashboard');
    }*/
}
