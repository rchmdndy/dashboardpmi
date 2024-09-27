<?php

namespace App\Services;

use CURLFile;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WhatsappNotificationService
{

    protected static string $api_token;
    protected static string $base_url = 'https://api.fonnte.com/send';


    public function __construct()
    {
        self::$api_token = env("FONNTE_API_TOKEN");
    }

    public static function sendMessage($phoneNumber, $transactionLink, $data){
        $curl = curl_init();
        $fonnte_api_token = env('FONNTE_API_TOKEN');
        Log::info("curl iniatied");
        $message =

"Hallo, terima kasih telah melakukan transaksi di PUSDIKLAT PMI JATENG. Silahkan lakukan pembayaran sesuai dengan nominal yang tertera pada invoice yang kami kirimkan.
Berikut adalah detail transaksi anda
```
Order-ID = {$data["order_id"]},
Nama = {$data["name"]},
Tanggal Check-In = {$data["start_date"]},
Tanggal Check-Out = {$data["end_date"]},
Tipe Ruangan = {$data["room_type"]},
Total Harga = {$data["total_price"]}
```

Berikut adalah link invoice transaksi Anda
$transactionLink
";

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => (string) $phoneNumber,
                'message' => $message,
                'url' => 'https://dashboard.palmerinjateng.id/storage/logo_asli.png',
                'filename' => 'PMI.png',
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $fonnte_api_token"//change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);
        Log::info("curl executed");
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            Log::error($error_msg);
        }
        Log::info($response);
    }

    public static function sendSuccessMessage($phoneNumber, $transactionLink, $data){
        $curl = curl_init();
        $fonnte_api_token = env('FONNTE_API_TOKEN');
        Log::info("curl iniatied");
        $message =

"Terima kasih transaksi anda telah berhasil diverifikasi.
Berikut adalah detail transaksi anda
```
Order-ID = {$data["order_id"]},
Nama = {$data["name"]},
Tanggal Check-In = {$data["start_date"]},
Tanggal Check-Out = {$data["end_date"]},
Tipe Ruangan = {$data["room_type"]},
Daftar Ruangan = {$data["rooms"]},
Total Harga = {$data["total_price"]}
```
*Waktu Check-In dan Check-Out di pukul 14:00 WIB*

Berikut adalah link invoice transaksi Anda
$transactionLink
";

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => (string) $phoneNumber,
                'message' => $message,
                'url' => 'https://dashboard.palmerinjateng.id/storage/logo_asli.png',
                'filename' => 'PMI.png',
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $fonnte_api_token"//change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);
        Log::info("curl executed");
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            Log::error($error_msg);
        }
        Log::info($response);
    }

    public static function sendFailedMessage($phoneNumber, $transactionLink, $data){
        $curl = curl_init();
        $fonnte_api_token = env('FONNTE_API_TOKEN');
        Log::info("curl iniatied");
        $message =

"Transaksi telah batal dikarenakan tidak melakukan pembayaran atau pembayaran tidak berhasil.
Berikut adalah detail transaksi anda
```
Order-ID = {$data["order_id"]},
Nama = {$data["name"]},
Tanggal Check-In = {$data["start_date"]},
Tanggal Check-Out = {$data["end_date"]},
Tipe Ruangan = {$data["room_type"]},
Total Harga = {$data["total_price"]}
```
Berikut adalah link invoice transaksi Anda
$transactionLink
";

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => (string) $phoneNumber,
                'message' => $message,
                'url' => 'https://dashboard.palmerinjateng.id/storage/logo_asli.png',
                'filename' => 'PMI.png',
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $fonnte_api_token"//change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);
        Log::info("curl executed");
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            Log::error($error_msg);
        }
        Log::info($response);
    }


}
