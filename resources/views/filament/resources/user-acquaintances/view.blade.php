@php $colors = array_reverse(['inherit', '#161b22', '#0e4429', '#006d32', '#26a641', '#39d353']); @endphp
@php
    $colors = [];
    $colors[] = 'rgba(var(--primary-50),  .05)';
    $colors[] = 'rgba(var(--primary-50),  .5)';
    $colors[] = 'rgba(var(--primary-100), .75)';
    $colors[] = 'rgba(var(--primary-200), .75)';
    $colors[] = 'rgba(var(--primary-300), .75)';
    $colors[] = 'rgba(var(--primary-400), .75)';
    $colors[] = 'rgba(var(--primary-500), .80)';
    $colors[] = 'rgba(var(--primary-600), .85)';
    $colors[] = 'rgba(var(--primary-700), .90)';
    $colors[] = 'rgba(var(--primary-800), .95)';
    $colors[] = 'rgba(var(--primary-900),   1)';
@endphp

<x-filament-panels::page>

    @if ($showActivitiesGraph)
        <x-filament::section>
            <x-slot name="heading">
                User activities
            </x-slot>

            <!-- @source https://codepen.io/ire/pen/Legmwo/ -->
            <div class="flex justify-center">
                <div class="graph block" style="overflow-y: hidden; overflow-x: auto">
                    <ul class="months">
                        <li>Jan</li>
                        <li>Feb</li>
                        <li>Mar</li>
                        <li>Apr</li>
                        <li>May</li>
                        <li>Jun</li>
                        <li>Jul</li>
                        <li>Aug</li>
                        <li>Sep</li>
                        <li>Oct</li>
                        <li>Nov</li>
                        <li>Dec</li>
                    </ul>
                    <ul class="days">
                        <li>Sun</li>
                        <li>Mon</li>
                        <li>Tue</li>
                        <li>Wed</li>
                        <li>Thu</li>
                        <li>Fri</li>
                        <li>Sat</li>
                    </ul>
                    <ul class="squares">
                        @foreach ($interactionsDates as $date)
                            <li class="td" data-level="{{ round($date->get('prc') * (count($colors) - 1), 0) }}"
                                title="{{ $date->get('nb') }}">
                            </li>
                        @endforeach
                    </ul>
                    <div class="squares-legend flex justify-end">
                        <span class="grow"></span>
                        <ul>
                            @foreach ($colors as $i => $color)
                                <li class="td" data-level="{{ $i }}"
                                    title="{{ round(($i / count($colors)) * 100) }}% - {{ round((($i + 1) / count($colors)) * 100) }}%">
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </x-filament::section>
    @endif

    @if ($showTimeline)
        <x-filament::section>
            <x-slot name="heading">
                User's subscriptions Timeline
            </x-slot>

            <!-- @source https://cruip.com/3-examples-of-brilliant-vertical-timelines-with-tailwind-css/ -->
            <div
                class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:ml-[8.75rem] md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">

                @foreach ($timeline as $timelineDate => $timelineData)
                    <!-- Item #1 -->
                    <div class="relative">
                        <div class="md:flex items-center md:space-x-4 mb-3">
                            <div class="flex items-center space-x-4 md:space-x-2 md:space-x-reverse">
                                <!-- Icon -->
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full bg-white shadow md:order-1">
                                    <svg class="fill-emerald-500" xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16">
                                        <path
                                            d="M8 0a8 8 0 1 0 8 8 8.009 8.009 0 0 0-8-8Zm0 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z" />
                                    </svg>
                                </div>
                                <!-- Date -->
                                <time class="font-caveat font-medium text- text-indigo-500 md:w-28">
                                    {{ $timelineDate }}
                                </time>
                            </div>
                            <!-- Title -->
                            <div class="text-slate-500 ml-14">
                                <span class="text-slate-900 font-bold">Mark Mikrol</span>
                                opened
                                the
                                request
                            </div>
                        </div>
                        <!-- Card -->
                        <div class="bg-white p-4 rounded border border-slate-200 text-slate-500 shadow ml-14 md:ml-44">
                            @foreach ($timelineData as $item)
                                <div>
                                    <div>{{ $item['object'] }} (#{{ $item['id'] }})
                                        {{ $item['what'] }}
                                        <span title="{{ $item['date'] }}">{{ $item['date']->diffForHumans() }}</span>
                                    </div>
                                    <div class="ps-3">
                                        <div class="truncate w-full">
                                            <small>{{ $item['title'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
        </x-filament::section>
    @endif


    @if ($showPopular)
        <x-filament::section>
            <x-slot name="heading">
                Popular
            </x-slot>

            <div class="grid gap-3 grid-cols-3">
                @foreach ($popular as $item)
                    <div
                        class="bg-white shadow-md border border-gray-200 rounded-lg max-w-sm dark:bg-gray-800 dark:border-gray-700 relative">
                        <a href="#">
                            {{-- <img class="rounded-t-lg" src="https://flowbite.com/docs/images/blog/image-1.jpg"
                            alt=""> --}}
                        </a>
                        <div class="p-5">
                            <a href="#">
                                <h5
                                    class="text-gray-900 font-bold text-2xl tracking-tight mb-2 dark:text-white truncate">
                                    {{ $item->subject }}
                                </h5>
                            </a>
                            <p class="font-normal text-gray-700 mb-3 dark:text-gray-400"
                                style="height: 5rem; overflow: hidden">
                                {{ $item->subject }}
                            </p>

                            <div class="flex justify-between items-center">
                                <x-filament::button size="xs">
                                    Read more
                                </x-filament::button>
                                <span>
                                    <small>{{ $item->count }} pts</small>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif





    <style>
        /* Article - https://bitsofco.de/github-contribution-graph-css-grid/ */

        /* Grid-related CSS */

        :root {
            --square-size: 13px;
            --square-gap: 2px;
            --week-width: calc(var(--square-size) + var(--square-gap));
        }

        .months {
            grid-area: months;
        }

        .days {
            grid-area: days;
        }

        .squares {
            grid-area: squares;
            padding: 3px;
        }

        .squares-legend {
            grid-area: legend;
            padding: 3px;
        }

        .graph {
            display: inline-grid;
            grid-template-areas:
                "empty  months"
                "days   squares"
                "legend legend";
            grid-template-columns: auto 1fr;
            grid-gap: 5px;
        }

        .months {
            display: grid;
            grid-template-columns: calc(var(--week-width) * 4)
                /* Jan */
                calc(var(--week-width) * 4)
                /* Feb */
                calc(var(--week-width) * 4)
                /* Mar */
                calc(var(--week-width) * 5)
                /* Apr */
                calc(var(--week-width) * 4)
                /* May */
                calc(var(--week-width) * 4)
                /* Jun */
                calc(var(--week-width) * 5)
                /* Jul */
                calc(var(--week-width) * 4)
                /* Aug */
                calc(var(--week-width) * 4)
                /* Sep */
                calc(var(--week-width) * 5)
                /* Oct */
                calc(var(--week-width) * 4)
                /* Nov */
                calc(var(--week-width) * 5)
                /* Dec */
            ;
        }

        .days,
        .squares,
        .squares-legend ul {
            display: grid;
            grid-gap: var(--square-gap);
            grid-template-rows: repeat(7, var(--square-size));
        }

        .squares {
            grid-auto-flow: column;
            grid-auto-columns: var(--square-size);
        }

        .squares-legend ul {
            grid-auto-flow: column;
            grid-auto-columns: repeat(7, var(--square-size));
            grid-template-rows: var(--square-size);
        }


        /* Other styling */

        .graph {}

        .days li:nth-child(odd) {
            visibility: hidden;
        }

        .squares-legend li.td,
        .squares li.td {
            background-color: rgba(250, 250, 250, .25);
            border-radius: 2px;
        }

        .squares-legend li.td {
            width: var(--square-size);
        }

        /* #c6e48b; #7bc96f #0e4429 #006d32 #196127*/
        /* #161b22 */
    </style>
    @foreach ($colors as $i => $color)
        <style>
            .graph li.td[data-level="{{ $i }}"] {
                background-color: {{ $color }};
            }
        </style>
    @endforeach
</x-filament-panels::page>
