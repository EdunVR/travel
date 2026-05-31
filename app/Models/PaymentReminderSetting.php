<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReminderSetting extends Model
{
    protected $table = 'payment_reminder_settings';

    protected $fillable = ['key', 'value', 'description'];

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Get reminder days as array
     */
    public static function getReminderDays(): array
    {
        $days = static::getValue('reminder_days', '30,15,7');
        return array_map('intval', array_filter(explode(',', $days)));
    }

    /**
     * Get start time
     */
    public static function getStartTime(): string
    {
        return static::getValue('start_time', '09:00');
    }

    /**
     * Get interval minutes
     */
    public static function getIntervalMinutes(): int
    {
        return (int) static::getValue('interval_minutes', 15);
    }

    /**
     * Check if reminders are active
     */
    public static function isActive(): bool
    {
        return (bool) static::getValue('is_active', true);
    }

    /**
     * Get message template
     */
    public static function getMessageTemplate(): string
    {
        return static::getValue('message_template', "Assalamu'alaikum {nama},\n\nPengingat pembayaran paket {paket}.\nSisa: Rp {sisa_bayar}\nBerangkat: {tgl_berangkat} ({sisa_hari} hari lagi)\n\n— HM Tour & Travel");
    }
}
