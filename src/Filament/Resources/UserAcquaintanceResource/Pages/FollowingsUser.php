<?php

namespace Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages;

use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource;

class FollowingsUser extends InteractionsUser implements HasTable
{
    use HasPageSidebar;
    use InteractsWithFormActions;
    use InteractsWithTable;

    public static string $relationQueryName = 'follow';

    public static string $relationName = 'followings';

    public static ?string $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

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

    public static function getSidebarNavigationItem(Model $record): array
    {
        return parent::getSidebarNavigationItem($record);
    }
}
