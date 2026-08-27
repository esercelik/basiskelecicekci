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
                    $settings = Schema::hasTable('store_settings') ? StoreSetting::query()->find(1) : null;
                } catch (Throwable) {
                    $settings = null;
                }

                $values = $settings?->only(array_keys($defaults)) ?? [];

                return array_replace($defaults, array_filter($values, fn (mixed $value): bool => filled($value)));
            });
        }

        return (object) [
            ...$data,
            'phoneUrl' => $this->normalizePhoneNumber($data['phone']),
            'whatsappNumber' => $this->normalizeWhatsappNumber($data['whatsapp_number']),
            'instagram_url' => $this->safeUrl($data['instagram_url']),
            'map_url' => $this->safeUrl($data['map_url']),
        ];
    }

    /**
     * @param  array{name: string, phone: string|null, whatsapp_number: string|null, address: string, instagram_url: string|null, map_url: string|null}  $attributes
     */
    public function update(array $attributes): StoreSetting
    {
        $settings = StoreSetting::query()->firstOrCreate(['id' => 1]);
        $settings->fill($attributes)->save();
        Cache::forget('store.settings');

        return $settings;
    }

    private function normalizePhoneNumber(?string $number): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $number);

        if (blank($number)) {
            return null;
        }

        $number = Str::startsWith($number, '00') ? Str::substr($number, 2) : $number;

        if (Str::startsWith($number, '0')) {
            $number = '90'.Str::substr($number, 1);
        }

        if (Str::startsWith($number, '90')) {
            $number = '+'.$number;
        }

        return preg_match('/^\+[1-9][0-9]{7,14}$/', $number) === 1 ? 'tel:'.$number : null;
    }

    private function normalizeWhatsappNumber(?string $number): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $number);

        if (blank($number)) {
            return null;
        }

        $number = Str::startsWith($number, '00') ? Str::substr($number, 2) : $number;

        if (Str::startsWith($number, '0')) {
            $number = '90'.Str::substr($number, 1);
        }

        return preg_match('/^[1-9][0-9]{7,14}$/', $number) === 1 ? $number : null;
    }

    private function safeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if (blank($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) ? $url : null;
    }
}
