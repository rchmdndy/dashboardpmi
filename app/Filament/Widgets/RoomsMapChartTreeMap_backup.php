<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use DB;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

use function PHPSTORM_META\map;

class RoomsMapChartTreeMap_backup extends ApexChartWidget
{
    protected static ?string $chartId = 'roomsMapChartTreeMap';
    protected static ?string $heading = 'Detail Ruangan';

    protected int|string|array $columnSpan = 'full';
    
    protected function getOptions(): array
    {
        $rooms = Room::with('roomType', 'booking')->get();
        $today = now()->toDateString();
        $data = $rooms->map(function ($room) use ($today) {
            $capacity = $room->roomType->capacity;
            $capacity_pure = $room->roomType->capacity;
            if (in_array($room->roomType->id, [1, 2])) {
                $capacity *= 4;
            }

            $booking = $room->booking->first(function ($booking) use ($today) {
                return $booking->start_date <= $today && $booking->end_date >= $today;
            });

            return [
                'x' => $room->room_name,
                'y' => $capacity,
                'capacity' => $capacity_pure,
                'checkin' => $booking ? $booking->start_date : "Kosong",
                'checkout' => $booking ? $booking->end_date : "Kosong",
                'fillColor' => $booking ? '#ce0000' : '#3d3c3b', 
            ];
        })->toArray();

        return [
            'chart' => [
                'type' => 'treemap',
                'height' => 300,
            ],
            'series' => [
                [
                    'data' => $data,
                ],
            ],
            'colors' => ['#ce0000', '#3d3c3b'],
            'legend' => [
                'show' => true,
            ],
            'plotOptions' => [
                'treemap' => [
                    'distributed' => true,
                    'enableShades' => false,
                ]
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
{
    return RawJs::make(<<<'JS'
    {
        chart: {
            events: {
                mounted: function() {
                    // Menambahkan elemen HTML di bawah chart untuk penjelasan warna
                    var chartEl = document.querySelector('#roomsMapChartTreeMap');
                    var legend = document.createElement('div');
                    legend.innerHTML = '<div class="color-legend">' +
                        '<span style="display:inline-block;width:20px;height:20px;background-color:#ce0000;"></span> ' +
                        'TIdak Tersedia Hari Ini ' +
                        '<span style="display:inline-block;width:20px;height:20px;background-color:#3d3c3b;margin-left:10px;"></span> ' +
                        'Tersedia Hari Ini' +
                        '</div>';
                    chartEl.parentNode.insertBefore(legend, chartEl.nextSibling);
                }
            }
        },
        tooltip: {
            custom: function({ series, seriesIndex, dataPointIndex, w }) {
                var data = w.globals.initialSeries[seriesIndex].data[dataPointIndex];
                return '<div class="custom-tooltip">' +
                    '<span><strong>Room:</strong> ' + data.x + '</span><br>' +
                    '<span><strong>Capacity:</strong> ' + data.capacity + '</span><br>' +
                    '<span><strong>Check-in:</strong> ' + data.checkin + '</span><br>' +
                    '<span><strong>Check-out:</strong> ' + data.checkout + '</span>' +
                    '</div>';
            }
        },
        dataLabels: {
            enabled: true,
        }
    }
    JS);
}

}