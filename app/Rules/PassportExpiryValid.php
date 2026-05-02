<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class PassportExpiryValid implements Rule
{
    protected $departureDate;
    protected $expiryDate;
    protected $requiredDate;

    /**
     * Create a new rule instance.
     *
     * @param  string  $departureDate
     * @return void
     */
    public function __construct($departureDate)
    {
        $this->departureDate = $departureDate;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->expiryDate = Carbon::parse($value);
        $this->requiredDate = Carbon::parse($this->departureDate)->addMonths(6);
        
        return $this->expiryDate->gte($this->requiredDate);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('travel-validation.passport.expiry_too_soon', [
            'expiry_date' => $this->expiryDate ? $this->expiryDate->format('d/m/Y') : '',
            'required_date' => $this->requiredDate ? $this->requiredDate->format('d/m/Y') : ''
        ]);
    }
}
