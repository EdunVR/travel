<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidKtpNik implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // NIK must be exactly 16 digits
        return preg_match('/^\d{16}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('travel-validation.ktp.nik_format', ['length' => strlen(request()->input('ktp_nik') ?? '')]);
    }
}
