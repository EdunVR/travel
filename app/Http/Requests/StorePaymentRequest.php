<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_jamaah_booking' => 'required|exists:jamaah_bookings,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,transfer,credit_card,debit_card,e_wallet',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id_jamaah_booking.required' => __('travel-validation.required', ['attribute' => 'booking']),
            'id_jamaah_booking.exists' => __('travel-validation.exists', ['attribute' => 'booking']),
            
            'payment_date.required' => __('travel-validation.payment.date_required'),
            'payment_date.date' => __('travel-validation.date', ['attribute' => 'tanggal pembayaran']),
            'payment_date.before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
            
            'amount.required' => __('travel-validation.payment.amount_required'),
            'amount.numeric' => __('travel-validation.numeric', ['attribute' => 'jumlah pembayaran']),
            'amount.min' => __('travel-validation.payment.amount_positive'),
            
            'payment_method.required' => __('travel-validation.payment.invalid_method'),
            'payment_method.in' => __('travel-validation.payment.invalid_method'),
            
            'reference_number.max' => __('travel-validation.max.string', ['attribute' => 'nomor referensi', 'max' => 100]),
            'notes.max' => __('travel-validation.max.string', ['attribute' => 'catatan', 'max' => 500]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return __('travel-validation.attributes');
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $booking = \App\Models\JamaahBooking::find($this->id_jamaah_booking);
            
            if ($booking && $this->amount) {
                $validationService = app(\App\Services\TravelValidationService::class);
                
                // Validate payment amount doesn't exceed remaining balance
                $paymentValidation = $validationService->validatePaymentAmount($booking, $this->amount);
                
                if (!$paymentValidation['valid']) {
                    $validator->errors()->add('amount', $paymentValidation['message']);
                }
            }
        });
    }
}
