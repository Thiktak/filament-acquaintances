<?php

namespace Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages;

use App\Filament\Matrix\Resources\Matrix\PersonResource;
use App\Jobs\Matrix\ProcessUpdateScores;
use App\Models\Matrix\Item;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
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
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Multicaret\Acquaintances\Models\InteractionRelation;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource;

class FavoritesUser extends InteractionsUser implements HasTable
{
    use InteractsWithTable, InteractsWithFormActions;
    use HasPageSidebar;

    public static string $relationQueryName = 'favorite';
    public static string $relationName      = 'favorites';
    public static ?string $navigationIcon    = 'heroicon-o-star';

    protected static string $view = 'thiktak-filament-acquaintances::filament.resources.user-acquaintances.index';

    protected static string $resource = UserAcquaintanceResource::class;

    public User $record;


    public function getTableQuery()
    {
        return parent::getTableQuery();
    }

    public function table(Table $table): Table
    {
        return parent::table($table);
    }

    public function getTableColumns(): array
    {
        return parent::getTableColumns();
    }

    static public function getSidebarNavigationItem(Model $record): array
    {
        return parent::getSidebarNavigationItem($record);
    }
}
