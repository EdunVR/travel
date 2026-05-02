<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'telepon' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'id_outlet' => Outlet::factory(),
            'kode_member' => $this->faker->unique()->numerify('M####'),
            'ktp_nik' => $this->faker->numerify('################'),
            'ktp_nama' => $this->faker->name(),
            'ktp_tempat_lahir' => $this->faker->city(),
            'ktp_tanggal_lahir' => $this->faker->date(),
            'ktp_alamat' => $this->faker->address(),
            'is_jamaah' => false
        ];
    }

    public function jamaah()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_jamaah' => true,
                'jamaah_type' => $this->faker->randomElement(['hajj', 'umrah']),
                'passport_nomor' => $this->faker->numerify('P########'),
                'passport_nama' => $attributes['nama'],
                'passport_tanggal_lahir' => $attributes['ktp_tanggal_lahir'],
                'passport_tanggal_kadaluarsa' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
                'passport_kewarganegaraan' => 'Indonesia',
                'gender' => $this->faker->randomElement(['male', 'female']),
                'health_conditions' => $this->faker->optional()->sentence(),
                'emergency_contact_name' => $this->faker->name(),
                'emergency_contact_phone' => $this->faker->phoneNumber(),
                'emergency_contact_relationship' => $this->faker->randomElement(['spouse', 'parent', 'sibling', 'child']),
                'room_preference' => $this->faker->randomElement(['single', 'double', 'triple', 'quad'])
            ];
        });
    }
}
