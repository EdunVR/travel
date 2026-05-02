<?php

function format_uang($angka){

    $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
    return $hasil_rupiah;
}

function format_nomor($angka){

    $hasil_rupiah = number_format($angka,0,',','.');
    return $hasil_rupiah;
}

function tanggal_indonesia($tgl, $tampil_hari = true) {
    $nama_hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $nama_bulan = array(1 =>
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );

    if (empty($tgl)) {
        $tgl = '2024-11-11';
    }

    $tahun = substr($tgl, 0, 4);
    $bulan = (int)substr($tgl, 5, 2); // Ambil bulan sebagai integer
    $tanggal = (int)substr($tgl, 8, 2); // Ambil tanggal sebagai integer
    $text = '';

    if ($tampil_hari) {
        $urutan_hari = date('w', mktime(0, 0, 0, $bulan, $tanggal, $tahun));
        $hari = $nama_hari[$urutan_hari];
        $text .= "$hari, $tanggal {$nama_bulan[$bulan]} $tahun";
    } else {
        $text .= "$tanggal {$nama_bulan[$bulan]} $tahun";
    }

    return $text;
}


function terbilang($angka){

    $angka = abs($angka);
    $baca = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan');
    $temp = "";
    if ($angka < 10) {
        $temp = " " . $baca[$angka];
    } else if ($angka < 20) {
        $temp = terbilang($angka - 10) . " Belas";
    } else if ($angka < 100) {
        $temp = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $temp = "Seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $temp = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $temp = "Seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $temp = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $temp = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
    }
    return $temp;
}

function tambah_nol_didepan($value, $threshold = null)
{
    return sprintf("%0". $threshold . "s", $value);
}

if (!function_exists('hasAnyAccess')) {
    function hasAnyAccess(array $permissions): bool
    {
        $userAccess = Auth::user()->akses ?? [];
        
        foreach ($permissions as $permission) {
            if (in_array($permission, $userAccess)) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Use the User model's hasPermission method which is more reliable
        return $user->hasPermission($permission);
    }
}