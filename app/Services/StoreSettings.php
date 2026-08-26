<?php

namespace App\Services;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class StoreSettings
{
    public function get(): object
    {
        $data = Cache::get('store.settings');

        if (! is_array($data)) {
            Cache::forget('store.settings');
            $data = Cache::remember('store.settings', now()->addHour(), function (): array {
                $defaults = [
                    'name' => (string) config('store.name'),
                    'phone' => (string) config('store.phone'),
                    'whatsapp_number' => (string) config('store.whatsapp_number'),
                    'address' => (string) config('store.address'),
                    'instagram_url' => (string) config('store.instagram_url'),
                    'map_url' => (string) config('store.map_url'),
                ];

                try {
                    $settings = Schema::hasTable('store_settings') ? StoreSetting::query()->first() : null;
                } catch (Throwable) {
                    $settings = null;
                }

                $values = $settings?->only(array_keys($defaults)) ?? [];

                return array_replace($defaults, array_filter($values, fn (mixed $value): bool => filled($value)));
            });
        }

        return (object) [
            ...$data,
            'phoneUrl' => 'tel:'.preg_replace('/[^0-9+]/', '', $data['phone']),
            'whatsappNumber' => $this->normalizeWhatsappNumber($data['whatsapp_number']),
        ];
    }

    /**
     * @param  array{name: string, phone: string, whatsapp_number: string, address: string, instagram_url: string|null, map_url: string|null}  $attributes
     */
    public function update(array $attributes): StoreSetting
    {
        $settings = StoreSetting::query()->firstOrCreate(['id' => 1]);
        $settings->fill($attributes)->save();
        Cache::forget('store.settings');

        return $settings;
    }

    private function normalizeWhatsappNumber(string $number): ?string
    {
        $number = preg_replace('/\D+/', '', $number);

        if (blank($number)) {
            return null;
        }

        $number = Str::startsWith($number, '00') ? Str::substr($number, 2) : $number;

        if (Str::startsWith($number, '0')) {
            $number = '90'.Str::substr($number, 1);
        }

        return preg_match('/^[1-9][0-9]{7,14}$/', $number) === 1 ? $number : null;
    }
}
