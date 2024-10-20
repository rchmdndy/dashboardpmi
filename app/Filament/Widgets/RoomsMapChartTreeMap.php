<?php

namespace App\Filament\Widgets;

use DB;
use App\Models\Room;
use Filament\Support\RawJs;
use App\Models\UserTransaction;
use function PHPSTORM_META\map;
use Illuminate\Support\Facades\Gate;
use App\Filament\Resources\BookingResource;

use App\Filament\Resources\UserTransactionResource;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RoomsMapChartTreeMap extends ApexChartWidget
{
    protected static ?string $chartId = 'roomsMapChartTreeMap';
    protected static ?string $heading = 'Detail Ruangan';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Gate::allows('admin') || Gate::allows('pimpinan') || Gate::allows('customerService');
    }

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

            // Ambil booking dan transaksi
            $booking = $room->booking->first(function ($booking) use ($today) {
                return $booking->start_date <= $today && $booking->end_date >= $today;
            });

            if ($booking) {
                $booking_transaction_id = $booking->user_transaction_id;
                $usertransaction = UserTransaction::where('id', $booking_transaction_id)->first();
                // dd($usertransaction->id);

                // Tentukan warna berdasarkan status transaksi
                if ($usertransaction->transaction_status == 'pending') {
                    $fillColor = '#00ff00'; // Hijau
                } elseif ($usertransaction->transaction_status == 'success') {
                    if ($usertransaction->verifyCheckin == 1) {
                        $fillColor = '#ffa500'; // Orange untuk success dan verifyCheckin 1
                        if ($usertransaction->verifyCheckout == 1) {
                            $fillColor = '#ff0000'; // Merah untuk success dan verifyCheckout 1
                        }
                    } elseif ($usertransaction->verifyCheckout == 1) {
                        $fillColor = '#ff0000'; // Merah untuk success dan verifyCheckout 1
                    } else {
                        $fillColor = '#0000ff'; // Biru untuk success tanpa verifikasi
                    }
                }elseif ($usertransaction->transaction_status == 'failed'){
                    $fillColor = '#3d3c3b';
                }
            } else {
                $fillColor = '#3d3c3b';
            }

            return [
                'x' => $room->room_name,
                'y' => $capacity,
                'room_type' => $room->roomType->room_type,
                'capacity' => $capacity_pure,
                'checkin' => $booking ? $booking->start_date : "Kosong",
                'checkout' => $booking ? $booking->end_date : "Kosong",
                'booking_id' => $booking ? $booking->id : null,
                'fillColor' => $fillColor, // Set warna berdasarkan kondisi di atas
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
                    'colorScale' => [
                        'ranges' => [
                            ['color' => '#00ff00', 'name' => 'Order'],
                            ['color' => '#0000ff', 'name' => 'Paid'],
                            ['color' => '#ffa500', 'name' => 'Check-In'],
                            ['color' => '#ff0000', 'name' => 'Check-Out'],
                            ['color' => '#3d3c3b', 'name' => 'Available'],
                        ],
                    ],
                ]
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        $baseUrl = BookingResource::getUrl();
        return RawJs::make(<<<JS
        {
            chart: {
                events: {
                    mounted: function() {
                        // Menambahkan elemen HTML di bawah chart untuk penjelasan warna
                        var chartEl = document.querySelector('#roomsMapChartTreeMap');
                        var legend = document.createElement('div');
                        legend.innerHTML = '<div class="color-legend">' +
                            '<span style="display:inline-block;width:20px;height:20px;background-color:#00ff00;"></span> ' +
                            'Order ' +
                            '<span style="display:inline-block;width:20px;height:20px;background-color:#0000ff;"></span> ' +
                            'Paid ' +
                            '<span style="display:inline-block;width:20px;height:20px;background-color:#ffa500;"></span> ' +
                            'Check-In ' +
                            '<span style="display:inline-block;width:20px;height:20px;background-color:#ff0000;"></span> ' +
                            'Check-Out ' +
                            '<span style="display:inline-block;width:20px;height:20px;background-color:#3d3c3b;margin-left:10px;"></span> ' +
                            'Available' +
                            '</div>';
                        chartEl.parentNode.insertBefore(legend, chartEl.nextSibling);
                    },
                    dataPointSelection: function(event, chartContext, config) {
                        var booking_id = config.w.config.series[config.seriesIndex].data[config.dataPointIndex].booking_id;
                        if (booking_id != null && booking_id != undefined) {
                            window.location.href = '{$baseUrl}/' + booking_id + '/view';
                        }
                    }
                }
            },
            tooltip: {
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    var data = w.globals.initialSeries[seriesIndex].data[dataPointIndex];
                    return '<div class="custom-tooltip">' +
                        '<span><strong>Room:</strong> ' + data.x + '</span><br>' +
                        '<span><strong>Tipe Ruangan:</strong> ' + data.room_type + '</span><br>' +
                        '<span><strong>Kapasitas:</strong> ' + data.capacity + '</span><br>' +
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
