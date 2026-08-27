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
            'phone' => null,
            'whatsapp_number' => null,
            'address' => 'Barbaros Mahallesi, Pınar Caddesi, Başiskele/Kocaeli',
            'instagram_url' => null,
            'map_url' => null,
        ];
    }
}
