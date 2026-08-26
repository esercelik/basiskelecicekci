@props(['product' => null, 'floating' => false, 'label' => 'WhatsApp’tan Sipariş Ver'])
@php
    $url = $product ? $product->whatsappOrderUrl() : \App\Models\Product::generalWhatsappUrl();
    $buttonClasses = $floating ? 'fixed bottom-5 right-5 z-30 grid size-14 place-items-center rounded-full bg-[#25D366] text-white shadow-lg shadow-forest/25 transition hover:-translate-y-1 hover:shadow-xl' : 'inline-flex min-h-11 items-center justify-center rounded-full bg-forest px-4 text-sm font-bold text-cream transition hover:bg-rose';
@endphp
@if ($product && ! $product->isOrderable())
    <span class="inline-flex min-h-11 items-center text-sm font-semibold text-ink/60">Bu ürün şu an stokta yok.</span>
@elseif ($url)
    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" {{ $attributes->merge(['class' => $buttonClasses]) }} aria-label="{{ $floating ? 'WhatsApp üzerinden iletişime geç' : $label }}"><svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.86 9.86 0 0 0-8.36 15.11L2.5 21.42l4.47-1.14A9.94 9.94 0 1 0 12.04 2Zm0 17.99a8.1 8.1 0 0 1-4.13-1.13l-.3-.18-2.65.68.71-2.58-.2-.32a8.12 8.12 0 1 1 6.57 3.53Zm4.45-6.08c-.24-.12-1.43-.71-1.65-.79-.22-.08-.38-.12-.54.12-.16.24-.62.79-.76.95-.14.16-.28.18-.52.06a6.48 6.48 0 0 1-1.93-1.19 7.2 7.2 0 0 1-1.34-1.67c-.14-.24-.01-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.4h-.46c-.16 0-.42.06-.64.3-.22.24-.84.83-.84 2.02s.86 2.35.98 2.51c.12.16 1.7 2.6 4.13 3.64.58.25 1.03.4 1.38.51.58.18 1.11.15 1.53.09.47-.07 1.43-.59 1.63-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.46-.28Z" /></svg><span @class(['sr-only' => $floating, 'ml-2' => ! $floating])>{{ $floating ? 'WhatsApp ile iletişime geç' : $label }}</span></a>
@endif
