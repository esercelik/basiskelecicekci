<?php

namespace Database\Factories;

use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreSetting>
 */
class StoreSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Başiskele Çiçek',
            'phone' => '+90 262 000 00 00',
            'whatsapp_number' => '905550000000',
            'address' => 'Barbaros Mahallesi, Pınar Caddesi, Başiskele/Kocaeli',
            'instagram_url' => 'https://www.instagram.com/basiskelecicek',
            'map_url' => 'https://maps.google.com/?q=Barbaros+Mahallesi,+Pınar+Caddesi,+Başiskele,+Kocaeli',
        ];
    }
}
