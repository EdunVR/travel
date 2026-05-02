<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'company_name',
        'company_code',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'company_logo',
        'company_favicon',
        'npwp',
        'nib',
        'siup',
        'tdp',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'currency',
        'timezone',
        'date_format',
        'time_format',
        'tax_rate',
        'is_active',
        'additional_settings'
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'additional_settings' => 'array'
    ];

    /**
     * Get the outlet that owns the company setting.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the company logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->company_logo) {
            return null;
        }

        if (filter_var($this->company_logo, FILTER_VALIDATE_URL)) {
            return $this->company_logo;
        }

        // Generate full URL with base URL
        $storageUrl = Storage::url($this->company_logo);
        
        // If the URL doesn't start with http, prepend the base URL
        if (!str_starts_with($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }

    /**
     * Get the company favicon URL.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if (!$this->company_favicon) {
            return null;
        }

        if (filter_var($this->company_favicon, FILTER_VALIDATE_URL)) {
            return $this->company_favicon;
        }

        // Generate full URL with base URL
        $storageUrl = Storage::url($this->company_favicon);
        
        // If the URL doesn't start with http, prepend the base URL
        if (!str_starts_with($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }

    /**
     * Get formatted company address.
     */
    public function getFormattedAddressAttribute(): string
    {
        return nl2br(e($this->company_address));
    }

    /**
     * Get formatted phone number.
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->company_phone;
        if (!$phone) return '';

        // Format Indonesian phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '+62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '+62' . $phone;
        } else {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    /**
     * Get company setting by outlet ID.
     */
    public static function getByOutlet(int $outletId): ?self
    {
        return static::where('outlet_id', $outletId)->first();
    }

    /**
     * Get or create company setting for outlet.
     */
    public static function getOrCreateForOutlet(int $outletId): self
    {
        return static::firstOrCreate(
            ['outlet_id' => $outletId],
            [
                'company_name' => 'Nama Perusahaan',
                'currency' => 'IDR',
                'timezone' => 'Asia/Jakarta',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'tax_rate' => 11.00,
                'is_active' => true
            ]
        );
    }

    /**
     * Scope for active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available currencies.
     */
    public static function getCurrencies(): array
    {
        return [
            'IDR' => 'Indonesian Rupiah (IDR)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)',
            'SGD' => 'Singapore Dollar (SGD)',
            'MYR' => 'Malaysian Ringgit (MYR)'
        ];
    }

    /**
     * Get available timezones.
     */
    public static function getTimezones(): array
    {
        return [
            'Asia/Jakarta' => 'WIB (Jakarta)',
            'Asia/Makassar' => 'WITA (Makassar)',
            'Asia/Jayapura' => 'WIT (Jayapura)',
            'Asia/Singapore' => 'Singapore',
            'UTC' => 'UTC'
        ];
    }

    /**
     * Get available date formats.
     */
    public static function getDateFormats(): array
    {
        return [
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'Y-m-d' => 'YYYY-MM-DD',
            'd-m-Y' => 'DD-MM-YYYY',
            'd.m.Y' => 'DD.MM.YYYY'
        ];
    }

    /**
     * Get available time formats.
     */
    public static function getTimeFormats(): array
    {
        return [
            'H:i' => '24 Hour (HH:MM)',
            'h:i A' => '12 Hour (HH:MM AM/PM)',
            'H:i:s' => '24 Hour with seconds (HH:MM:SS)'
        ];
    }

    /**
     * Get setting value by key with default fallback.
     */
    public static function getValue(string $key, $default = null, int $outletId = null)
    {
        $query = static::query();
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        
        $setting = $query->first();
        
        if (!$setting) {
            return $default;
        }
        
        // Handle special cases for URL attributes
        if ($key === 'logo_url') {
            return $setting->logo_url;
        }
        
        if ($key === 'favicon_url') {
            return $setting->favicon_url;
        }
        
        // Return the attribute value or default
        return $setting->getAttribute($key) ?? $default;
    }
}