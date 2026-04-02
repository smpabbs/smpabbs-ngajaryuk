<?php

namespace App\Helpers;

class GeoHelper
{
    public static function getAddressFromLatLon($lat, $lon)
    {
        // Konversi ke decimal (kalau masih format DMS)
        $lat = self::convertToDecimal($lat);
        $lon = self::convertToDecimal($lon);

        // Kalau gagal konversi, return pesan error
        if (!is_numeric($lat) || !is_numeric($lon)) {
            return "Format koordinat tidak valid";
        }

        $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}&format=json&accept-language=id";

        $response = self::fetchUrl($url);

        if (!$response) {
            return "Alamat tidak ditemukan (timeout / gagal koneksi)";
        }

        $data = json_decode($response, true);

        return $data['display_name'] ?? "Alamat tidak ditemukan";
    }

    private static function fetchUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // waktu maksimal buat connect
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);        // waktu maksimal total request
        curl_setopt($ch, CURLOPT_USERAGENT, "LaravelApp/1.0"); // wajib User-Agent

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            // bisa log error kalau mau
            // \Log::error('GeoHelper cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $response;
    }

    private static function convertToDecimal($coord)
    {
        // Kalau sudah angka desimal → langsung return
        if (is_numeric($coord)) {
            return (float) $coord;
        }

        $coord = trim($coord);

        // Regex format DMS: 7°32'35.5"S
        if (preg_match('/(\d+)°(\d+)\'([\d\.]+)"?([NSEW])/', $coord, $m)) {
            $deg = (float) $m[1];
            $min = (float) $m[2];
            $sec = (float) $m[3];
            $dir = $m[4];

            $decimal = $deg + ($min / 60) + ($sec / 3600);

            if ($dir === 'S' || $dir === 'W') {
                $decimal *= -1;
            }

            return $decimal;
        }

        // Fallback: return string asli
        return $coord;
    }
}
