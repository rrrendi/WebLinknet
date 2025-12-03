<?php

namespace App\Helpers;

use Carbon\Carbon;

class Helper
{
    /**
     * Format tanggal ke format Indonesia: dd-mm-yyyy HH:mm:ss
     */
    public static function formatTanggal($date)
    {
        if (!$date) return '-';
        
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $carbon->format('d-m-Y H:i:s');
    }

    /**
     * Format tanggal ke format Indonesia (tanpa jam): dd-mm-yyyy
     */
    public static function formatTanggalSaja($date)
    {
        if (!$date) return '-';
        
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $carbon->format('d-m-Y');
    }

    /**
     * Get badge class based on status
     */
    public static function getStatusBadgeClass($status)
    {
        return match(strtoupper($status)) {
            'OK' => 'badge-ok bg-success',
            'NOK' => 'badge-nok bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Generate unique serial number
     */
    public static function generateSerialNumber($prefix = '')
    {
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . $year . $month . $random;
    }

    /**
     * Validate serial number format
     */
    public static function validateSerialNumber($serialNumber)
    {
        // Remove whitespace
        $serialNumber = trim($serialNumber);
        
        // Check if not empty
        if (empty($serialNumber)) {
            return [
                'valid' => false,
                'message' => 'Serial number tidak boleh kosong'
            ];
        }

        // Check minimum length
        if (strlen($serialNumber) < 5) {
            return [
                'valid' => false,
                'message' => 'Serial number minimal 5 karakter'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Serial number valid'
        ];
    }

    /**
     * Format number with thousand separator
     */
    public static function formatNumber($number)
    {
        return number_format($number, 0, ',', '.');
    }

    /**
     * Get current timestamp in Indonesian format
     */
    public static function getCurrentTimestamp()
    {
        return Carbon::now()->format('d-m-Y H:i:s');
    }

    /**
     * Parse Indonesian date format to Carbon
     */
    public static function parseIndonesianDate($date)
    {
        // Format: dd-mm-yyyy HH:mm:ss atau dd-mm-yyyy
        try {
            return Carbon::createFromFormat('d-m-Y H:i:s', $date);
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d-m-Y', $date);
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * Get kategori barang options
     */
    public static function getKategoriBarang()
    {
        return ['ONT', 'STB', 'ROUTER'];
    }

    /**
     * Get jenis kerusakan options
     */
    public static function getJenisKerusakan()
    {
        return [
            'Konektor LAN rusak',
            'Konektor Optic rusak',
            'Adapter rusak',
            'Port mati',
            'LED mati',
            'Board rusak',
            'Tidak bisa nyala',
            'Restart terus',
            'Lainnya'
        ];
    }

    /**
     * Check if serial number sudah melewati Repair OK atau Uji Fungsi OK
     */
    public static function isEligibleForRekondisi($serialNumber)
    {
        $repairOk = \App\Models\Repair::where('serial_number', $serialNumber)
            ->where('status', 'OK')
            ->exists();
        
        $ujiFungsiOk = \App\Models\UjiFungsi::where('serial_number', $serialNumber)
            ->where('status', 'OK')
            ->exists();

        return $repairOk || $ujiFungsiOk;
    }

    /**
     * Check if serial number berstatus NOK di Uji Fungsi atau Repair
     */
    public static function isEligibleForServiceHandling($serialNumber)
    {
        $ujiFungsiNok = \App\Models\UjiFungsi::where('serial_number', $serialNumber)
            ->where('status', 'NOK')
            ->exists();
        
        $repairNok = \App\Models\Repair::where('serial_number', $serialNumber)
            ->where('status', 'NOK')
            ->exists();

        return $ujiFungsiNok || $repairNok;
    }

    /**
     * Check if serial number sudah melewati Rekondisi
     */
    public static function isEligibleForPacking($serialNumber)
    {
        return \App\Models\Rekondisi::where('serial_number', $serialNumber)->exists();
    }

    /**
     * Get progress percentage
     */
    public static function getProgressPercentage($current, $total)
    {
        if ($total == 0) return 0;
        return round(($current / $total) * 100, 2);
    }

    /**
     * Log activity (for future audit trail)
     */
    public static function logActivity($action, $module, $data = [])
    {
        // You can implement activity logging here
        // For now, just return true
        return true;
    }

    /**
     * Sanitize input
     */
    public static function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate No DO
     */
    public static function generateNoDO()
    {
        $date = date('Ymd');
        $lastDO = \App\Models\Igi::whereDate('created_at', today())
            ->count();
        
        $number = str_pad($lastDO + 1, 4, '0', STR_PAD_LEFT);
        
        return "DO-{$date}-{$number}";
    }
}

// Register helper functions globally (add this to composer.json autoload files)
if (!function_exists('formatTanggal')) {
    function formatTanggal($date) {
        return \App\Helpers\Helper::formatTanggal($date);
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return \App\Helpers\Helper::formatNumber($number);
    }
}

if (!function_exists('getCurrentTimestamp')) {
    function getCurrentTimestamp() {
        return \App\Helpers\Helper::getCurrentTimestamp();
    }
}