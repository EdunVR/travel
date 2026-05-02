<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FutureDepartureDate;
use App\Rules\ValidKtpNik;
use App\Rules\PassportExpiryValid;

class StoreJamaahBookingRequest extends FormRequest
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
        $package = \App\Models\TravelPackage::find($this->id_travel_package);
        
        return [
            'id_travel_package' => 'required|exists:travel_packages,id',
            'id_member' => 'required|exists:member,id',
            'id_keberangkatan' => 'nullable|exists:keberangkatan,id',
            'booking_date' => 'required|date',
            'total_price' => 'required|numeric|min:0',
            
            // Jamaah validation
            'ktp_nik' => ['required', new ValidKtpNik()],
            'ktp_tanggal_lahir' => 'required|date|before:today',
            'passport_nomor' => 'required|string|max:50',
            'passport_tanggal_kadaluarsa' => [
                'required',
                'date',
                'after:today',
                $package ? new PassportExpiryValid($package->departure_date) : 'after:today'
            ],
            
            // Mahram validation for female under 45
            'mahram_name' => 'required_if:gender,female',
            'mahram_relationship' => 'required_if:gender,female',
            'mahram_phone' => 'required_if:gender,female',
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
            'id_travel_package.required' => __('travel-validation.booking.package_required'),
            'id_travel_package.exists' => __('travel-validation.exists', ['attribute' => 'paket']),
            'id_member.required' => __('travel-validation.booking.jamaah_required'),
            'id_member.exists' => __('travel-validation.exists', ['attribute' => 'jamaah']),
            'id_keberangkatan.exists' => __('travel-validation.exists', ['attribute' => 'keberangkatan']),
            
            'booking_date.required' => __('travel-validation.required', ['attribute' => 'tanggal booking']),
            'booking_date.date' => __('travel-validation.date', ['attribute' => 'tanggal booking']),
            
            'total_price.required' => __('travel-validation.required', ['attribute' => 'total harga']),
            'total_price.numeric' => __('travel-validation.numeric', ['attribute' => 'total harga']),
            'total_price.min' => __('travel-validation.positive', ['attribute' => 'total harga']),
            
            'ktp_nik.required' => __('travel-validation.ktp.nik_required'),
            'ktp_tanggal_lahir.required' => __('travel-validation.jamaah_age.birth_date_required'),
            'ktp_tanggal_lahir.date' => __('travel-validation.date', ['attribute' => 'tanggal lahir']),
            'ktp_tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            
            'passport_nomor.required' => __('travel-validation.passport.number_required'),
            'passport_nomor.max' => __('travel-validation.max.string', ['attribute' => 'nomor passport', 'max' => 50]),
            
            'passport_tanggal_kadaluarsa.required' => __('travel-validation.passport.expiry_required'),
            'passport_tanggal_kadaluarsa.date' => __('travel-validation.date', ['attribute' => 'tanggal kadaluarsa passport']),
            'passport_tanggal_kadaluarsa.after' => 'Passport sudah kadaluarsa.',
            
            'mahram_name.required_if' => __('travel-validation.mahram.name_required'),
            'mahram_relationship.required_if' => __('travel-validation.mahram.relationship_required'),
            'mahram_phone.required_if' => __('travel-validation.mahram.phone_required'),
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
            // Additional custom validation logic
            $member = \App\Models\Member::find($this->id_member);
            $package = \App\Models\TravelPackage::find($this->id_travel_package);
            
            if ($member && $package) {
                $validationService = app(\App\Services\TravelValidationService::class);
                
                // Age validation
                $ageValidation = $validationService->validateJamaahAge($member, $package->package_type);
                if (!$ageValidation['valid']) {
                    $validator->errors()->add('ktp_tanggal_lahir', $ageValidation['message']);
                }
                
                // Mahram validation
                $mahramValidation = $validationService->validateMahramRequirement($member);
                if (!$mahramValidation['valid']) {
                    $validator->errors()->add('mahram_name', $mahramValidation['message']);
                }
                
                // Package capacity validation
                $availableSeats = $package->getAvailableSeats();
                if ($availableSeats <= 0) {
                    $validator->errors()->add('id_travel_package', __('travel-validation.capacity.package_full', [
                        'capacity' => $package->capacity,
                        'booked' => $package->capacity - $availableSeats
                    ]));
                }
            }
        });
    }
}
