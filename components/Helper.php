<?php

namespace app\components;

use yii\base\Component;

class Helper extends Component
{
    public static function formatDateIndonesia($date, $time = false, $unix = true)
    {
        $result = '';
        if ($date) {
            $date = $unix ? date('Y-m-d H:i:s', $date) : date('Y-m-d H:i:s', strtotime($date));
            $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
            $hariIndo = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
            $tahun = substr($date, 0, 4);
            $bulan = substr($date, 5, 2);
            $tgl = substr($date, 8, 2);
            $mingguKe = date('w', strtotime($date));
            if ($time) $jam = '<br> ' . substr($date, 11, 8);
            else $jam = '';

            $result = $hariIndo[$mingguKe] . ", " . $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun . $jam;

        }
        return $result;
    }
}