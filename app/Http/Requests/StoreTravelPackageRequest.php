<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FutureDepartureDate;

class StoreTravelPackageRequest extends FormRequest
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
        $packageId = $this->route('package') ?? $this->id;
        
        return [
            'package_code' => 'required|string|max:50|unique:travel_packages,package_code,' . $packageId,
            'package_name' => 'required|string|max:255',
            'package_type' => 'required|in:hajj,umrah',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:365',
            'departure_date' => ['required', 'date', new FutureDepartureDate()],
            'return_date' => 'required|date|after:departure_date',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'hpp' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric',
            'status' => 'nullable|in:draft,active,full,completed,cancelled',
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
            'package_code.required' => __('travel-validation.required', ['attribute' => 'kode paket']),
            'package_code.unique' => __('travel-validation.unique', ['attribute' => 'kode paket']),
            'package_code.max' => __('travel-validation.max.string', ['attribute' => 'kode paket', 'max' => 50]),
            
            'package_name.required' => __('travel-validation.required', ['attribute' => 'nama paket']),
            'package_name.max' => __('travel-validation.max.string', ['attribute' => 'nama paket', 'max' => 255]),
            
            'package_type.required' => __('travel-validation.required', ['attribute' => 'jenis paket']),
            'package_type.in' => __('travel-validation.in', ['attribute' => 'jenis paket']),
            
            'duration_days.required' => __('travel-validation.required', ['attribute' => 'durasi']),
            'duration_days.integer' => __('travel-validation.integer', ['attribute' => 'durasi']),
            'duration_days.min' => __('travel-validation.min.numeric', ['attribute' => 'durasi', 'min' => 1]),
            'duration_days.max' => __('travel-validation.max.numeric', ['attribute' => 'durasi', 'max' => 365]),
            
            'departure_date.required' => __('travel-validation.departure_date.required'),
            'departure_date.date' => __('travel-validation.date', ['attribute' => 'tanggal keberangkatan']),
            
            'return_date.required' => __('travel-validation.return_date.required'),
            'return_date.date' => __('travel-validation.date', ['attribute' => 'tanggal kepulangan']),
            'return_date.after' => __('travel-validation.return_date.after_departure'),
            
            'capacity.required' => __('travel-validation.required', ['attribute' => 'kapasitas']),
            'capacity.integer' => __('travel-validation.integer', ['attribute' => 'kapasitas']),
            'capacity.min' => __('travel-validation.min.numeric', ['attribute' => 'kapasitas', 'min' => 1]),
            
            'price.required' => __('travel-validation.required', ['attribute' => 'harga']),
            'price.numeric' => __('travel-validation.numeric', ['attribute' => 'harga']),
            'price.min' => __('travel-validation.positive', ['attribute' => 'harga']),
            
            'hpp.numeric' => __('travel-validation.numeric', ['attribute' => 'HPP']),
            'hpp.min' => __('travel-validation.positive', ['attribute' => 'HPP']),
            
            'profit_margin.numeric' => __('travel-validation.numeric', ['attribute' => 'margin keuntungan']),
            
            'status.in' => __('travel-validation.in', ['attribute' => 'status']),
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
            // Check if price is below HPP (warning, not error)
            if ($this->hpp && $this->price && $this->price <= $this->hpp) {
                $margin = $this->hpp > 0 ? (($this->price - $this->hpp) / $this->hpp * 100) : 0;
                
                // Add as warning in session instead of validation error
                session()->flash('warning', __('travel-validation.pricing.below_hpp', [
                    'price' => number_format($this->price, 0, ',', '.'),
                    'hpp' => number_format($this->hpp, 0, ',', '.'),
                    'margin' => number_format($margin, 2, ',', '.')
                ]));
            }
        });
    }
}
