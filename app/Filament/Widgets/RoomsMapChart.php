<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use App\Models\Inventory;
use App\Models\RoomAsset;
use Filament\Support\RawJs;
use App\Filament\Resources\RoomAssetResource;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RoomsMapChart extends ApexChartWidget
{
    protected static ?string $chartId = 'roomsMapChart';

    protected static ?string $heading = 'Rounded (Range without Shades)';

    protected int|string|array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $rooms = Room::with('roomAssets.inventory')->get();
        $items = Inventory::pluck('name')->toArray();
    
        $series = [];
    
        foreach ($items as $item) {
            $data = [];
    
            foreach ($rooms as $room) {
                $roomAsset = $room->roomAssets->first(function ($asset) use ($item) {
                    return $asset->inventory->name === $item;
                });
    
                $data[] = [
                    'x' => $room->room_name,
                    'y' => $roomAsset ? ($roomAsset->isBroken == false ? 1 : 2) : 0,
                    'room_id' => $room->id,
                    'inventory_id' => $roomAsset ? $roomAsset->inventory_id : null,
                ];

            }
    
            $series[] = [
                'name' => $item,
                'data' => $data,
            ];
        }
    
        return [
            'chart' => [
                'type' => 'heatmap',
                'height' => 350,
            ],
            'series' => $series,
            'stroke' => [
                'width' => 0,
            ],
            'plotOptions' => [
                'heatmap' => [
                    'radius' => 30,
                    'enableShades' => false,
                    'colorScale' => [
                        'ranges' => [
                            ['from' => 0, 'to' => 0, 'color' => '#CCCCCC', 'name' => 'Not Present'],
                            ['from' => 1, 'to' => 1, 'color' => '#00E396', 'name' => 'Normal'],
                            ['from' => 2, 'to' => 2, 'color' => '#008FFB', 'name' => 'Rusak'],
                        ],
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'xaxis' => [
                'type' => 'category',
            ],
            'yaxis' => [
                'labels' => [
                    'show' => true,
                ],
            ],
            'title' => [
                'text' => 'Room Assets Status',
            ],
            
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        $baseUrl = RoomAssetResource::getUrl();
        
        return RawJs::make(<<<JS
        {
            chart: {
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        var roomId = config.w.config.series[config.seriesIndex].data[config.dataPointIndex].room_id;
                        var inventoryId = config.w.config.series[config.seriesIndex].data[config.dataPointIndex].inventory_id;
                        if (roomId && inventoryId) {
                            window.location.href = '{$baseUrl}' + '?tableFilters[room_id][value]=' + roomId + '&tableFilters[inventory_id][value]=' + inventoryId;
                        }
                    }
                }
            },
        }
        JS);
    }

    
}
