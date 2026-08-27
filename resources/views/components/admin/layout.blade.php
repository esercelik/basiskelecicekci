@props(['title'])
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ $store->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-w-0 bg-cream text-ink">
    <div class="min-h-screen lg:grid lg:grid-cols-[16rem_1fr]">
        <aside class="hidden bg-forest-dark p-5 text-cream lg:block">
            <a href="{{ route('admin.dashboard') }}" class="mb-10 flex items-center gap-3 font-display text-xl font-bold"><span class="grid size-10 place-items-center rounded-full bg-linear-to-br from-rose to-forest text-cream shadow-lg">✿</span>{{ $store->name }}</a>
            <nav class="grid gap-2 text-sm font-bold" aria-label="Yönetim menüsü">
                <a href="{{ route('admin.dashboard') }}" @class(['rounded-xl px-4 py-3 transition hover:bg-white/10', 'bg-rose-soft text-forest-dark shadow-sm' => request()->routeIs('admin.dashboard')])>Genel bakış</a>
                <a href="{{ route('admin.products.index') }}" @class(['rounded-xl px-4 py-3 transition hover:bg-white/10', 'bg-rose-soft text-forest-dark shadow-sm' => request()->routeIs('admin.products.*')])>Ürünler</a>
                <a href="{{ route('admin.categories.index') }}" @class(['rounded-xl px-4 py-3 transition hover:bg-white/10', 'bg-rose-soft text-forest-dark shadow-sm' => request()->routeIs('admin.categories.*')])>Kategoriler</a>
                <a href="{{ route('admin.settings.edit') }}" @class(['rounded-xl px-4 py-3 transition hover:bg-white/10', 'bg-rose-soft text-forest-dark shadow-sm' => request()->routeIs('admin.settings.*')])>Mağaza ayarları</a>
                <a href="{{ route('admin.profile.edit') }}" @class(['rounded-xl px-4 py-3 transition hover:bg-white/10', 'bg-rose-soft text-forest-dark shadow-sm' => request()->routeIs('admin.profile.*')])>Profil</a>
            </nav>
        </aside>
        <div class="min-w-0">
            <header class="flex min-h-16 items-center justify-between gap-3 border-b border-border bg-white/90 px-4 py-3 backdrop-blur sm:px-6">
                <details class="relative lg:hidden"><summary class="grid size-11 cursor-pointer place-items-center rounded-xl border border-border text-forest [&::-webkit-details-marker]:hidden" aria-label="Yönetim menüsünü aç">☰</summary><nav class="absolute left-0 top-12 z-30 grid w-64 gap-1 rounded-2xl bg-forest-dark p-3 text-sm font-bold text-cream shadow-xl"><a class="rounded-xl px-4 py-3 hover:bg-white/10" href="{{ route('admin.dashboard') }}">Genel bakış</a><a class="rounded-xl px-4 py-3 hover:bg-white/10" href="{{ route('admin.products.index') }}">Ürünler</a><a class="rounded-xl px-4 py-3 hover:bg-white/10" href="{{ route('admin.categories.index') }}">Kategoriler</a><a class="rounded-xl px-4 py-3 hover:bg-white/10" href="{{ route('admin.settings.edit') }}">Mağaza ayarları</a><a class="rounded-xl px-4 py-3 hover:bg-white/10" href="{{ route('admin.profile.edit') }}">Profil</a></nav></details>
                <h1 class="min-w-0 truncate font-display text-xl font-bold text-forest-dark sm:text-2xl">{{ $title }}</h1>
                <div class="flex items-center gap-2"><a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="hidden min-h-11 items-center rounded-xl border border-forest px-3 text-sm font-bold text-forest transition hover:bg-lavender sm:inline-flex">Siteyi gör</a><form action="{{ route('admin.logout') }}" method="POST">@csrf<button class="min-h-11 rounded-xl bg-forest px-3 text-sm font-bold text-cream transition hover:bg-forest-dark">Çıkış</button></form></div>
            </header>
            <main class="mx-auto max-w-7xl p-4 sm:p-6">@if (session('status'))<div class="mb-5 rounded-2xl border border-forest/20 bg-lavender px-4 py-3 text-sm font-semibold text-forest-dark" role="status">{{ session('status') }}</div>@endif @if ($errors->any())<div class="mb-5 rounded-2xl border border-rose/30 bg-rose-soft px-4 py-3 text-sm text-ink" role="alert">Lütfen formdaki alanları kontrol edin.</div>@endif{{ $slot }}</main>
        </div>
    </div>
</body>
</html>
