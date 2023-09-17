<?php

namespace Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource\Pages;

use App\Filament\Matrix\Resources\Matrix\PersonResource;
use App\Jobs\Matrix\ProcessUpdateScores;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Multicaret\Acquaintances\Models\InteractionRelation;
use Thiktak\FilamentAcquaintances\Filament\Resources\UserAcquaintanceResource;
use Thiktak\FilamentAcquaintances\FilamentAcquaintancesPlugin;

class ViewUser extends \Filament\Resources\Pages\Page
{
    //use \Thiktak\FilamentBookmarks\Traits\HasPageBookmarks;
    //use \Thiktak\FilamentBookmarks\Traits\HasPageHistory;

    use HasPageSidebar;

    //protected static string $viewSidebar = 'filament.matrix.resources.matrix.person-resource.pages.view-person';
    //protected static string $view = 'filament-page-with-sidebar::proxy'; //filament.matrix.resources.matrix.person-resource.pages.view-person';

    protected static string $view = 'thiktak-filament-acquaintances::filament.resources.user-acquaintances.view';

    protected static string $resource = UserAcquaintanceResource::class;

    public User $record;

    protected Collection $interactionsDates;

    /**************************************************************************
     * Main functions
     *************************************************************************/

    protected function getViewData(): array
    {

        return ([
            ...$this->getDataForAcitivitiesGraph(),
            ...$this->getDataForTimeline(),
            ...$this->getDataForPopular(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }


    /**************************************************************************
     * Support functions
     *************************************************************************/

    static public function getConfig($dot = ''): mixed
    {
        return FilamentAcquaintancesPlugin::get()->config('configureUserProfileTrends.' . $dot);
    }


    /**************************************************************************
     * Logic functions
     *************************************************************************/

    public function getDataForAcitivitiesGraph(): array
    {
        if (!static::getConfig('showActivitiesGraph')) {
            return [
                'showActivitiesGraph' => false
            ];
        }

        $interactionsDates = InteractionRelation::query()
            ->selectRaw('DATE_FORMAT(created_at, \'%Y-%m-%d\') as d, count(*) as nb')
            ->where('user_id', $this->record->id)
            ->whereYear('created_at', date('Y'))
            ->groupByRaw('DATE_FORMAT(created_at, \'%Y-%m-%d\')')
            //->getQuery()->toRawSQl() //
            ->get()
            ->pluck('nb', 'd');

        $max = $interactionsDates->max() ?: 1;

        $begin = new \DateTime(date('Y') . '-01-01');
        $end = new \DateTime(date('Y') . '-12-31');
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        foreach ($period as $date) {
            if (!isset($interactionsDates[$date->format('Y-m-d')])) {
                $interactionsDates[$date->format('Y-m-d')] = 0;
            }
        }


        $interactionsDates = collect($interactionsDates)
            ->sortKeysDesc()
            ->map(fn ($nb) => collect([
                'nb' => $nb,
                'prc' => $nb / $max
            ]));

        return [
            'showActivitiesGraph' => true,
            'datePeriod' => $period,
            'interactionsDates' => $interactionsDates
        ];
    }



    public function getDataForTimeline(): array
    {

        if (!static::getConfig('showTimeline')) {
            return [
                'showTimeline' => false
            ];
        }

        $interactions = InteractionRelation::query()
            ->where('user_id', $this->record->id)
            //->whereIn('relation', ['subscribe', 'follow'])
            //->where('subject_type', 'like', '%User%')
            ->get()
            ->groupBy('subject_type');

        $timeline =
            $interactions->map(function ($objects, $subjectType) {
                return ($app = app($subjectType))::query()
                    ->whereIn($app->getKeyName(), $objects->pluck('subject_id'))
                    ->orderByDesc('updated_at')
                    ->orderByDesc('created_at')
                    ->take(20)
                    ->get()
                    ->mapWithKeys(function ($record) {
                        $key = implode(';', [
                            '', $record->created_at, $record->getKey(), get_class($record)
                        ]);
                        foreach (['created_at', 'updated_at', 'deleted_at'] as $what) {
                            if ($record->$what) {
                                $return[$record->$what . $key] = [
                                    'key'  => $record->getKeyName(),
                                    'id'   => $record->getKey(),
                                    'object' => get_class($record),
                                    'title' => (string) $record,
                                    'date' => $record->$what,
                                    'YMD' => $record->$what->format('Y-m-d'),
                                    'what' => $what,
                                ];
                            }
                        }
                        return $return;
                    });
            })
            ->collapse()
            ->sortKeysDesc()
            ->take(50)
            ->groupBy('YMD');


        return [
            'showTimeline' => true,
            'timeline' => $timeline
        ];
    }


    public function getDataForPopular(): array
    {
        if (!static::getConfig('showPopular')) {
            return [
                'showPopular' => false
            ];
        }

        return [
            'showPopular' => true,
            'popular' => InteractionRelation::popular()
                ->paginate(15),
        ];
    }
}
