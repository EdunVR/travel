<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format tanggal ke DD/MM/YYYY
     */
    public static function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) return '-';
        
        try {
            if (is_string($date)) {
                $date = Carbon::parse($date);
            }
            
            return $date->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Format tanggal dan waktu ke DD/MM/YYYY HH:mm
     */
    public static function formatDateTime($date, $format = 'd/m/Y H:i')
    {
        if (!$date) return '-';
        
        try {
            if (is_string($date)) {
                $date = Carbon::parse($date);
            }
            
            return $date->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Format tanggal untuk input HTML (YYYY-MM-DD)
     */
    public static function formatForInput($date)
    {
        if (!$date) return '';
        
        try {
            if (is_string($date)) {
                $date = Carbon::parse($date);
            }
            
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Parse tanggal dari format DD/MM/YYYY ke Carbon
     */
    public static function parseFromDDMMYYYY($dateString)
    {
        if (!$dateString) return null;
        
        try {
            return Carbon::createFromFormat('d/m/Y', $dateString);
        } catch (\Exception $e) {
            // Fallback ke parsing normal
            try {
                return Carbon::parse($dateString);
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    /**
     * Get current date in DD/MM/YYYY format
     */
    public static function today($format = 'd/m/Y')
    {
        return Carbon::now('Asia/Jakarta')->format($format);
    }

    /**
     * Get current datetime in DD/MM/YYYY HH:mm format
     */
    public static function now($format = 'd/m/Y H:i')
    {
        return Carbon::now('Asia/Jakarta')->format($format);
    }

    /**
     * Convert date to Indonesian format with day name
     */
    public static function toIndonesian($date, $includeTime = false)
    {
        if (!$date) return '-';
        
        try {
            if (is_string($date)) {
                $date = Carbon::parse($date);
            }
            
            $date->locale('id');
            
            if ($includeTime) {
                return $date->format('l, d F Y H:i');
            } else {
                return $date->format('l, d F Y');
            }
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Get date range for filters (start and end of month)
     */
    public static function getMonthRange($year = null, $month = null)
    {
        $date = Carbon::now('Asia/Jakarta');
        
        if ($year) $date->year = $year;
        if ($month) $date->month = $month;
        
        return [
            'start' => $date->copy()->startOfMonth()->format('Y-m-d'),
            'end' => $date->copy()->endOfMonth()->format('Y-m-d')
        ];
    }
}