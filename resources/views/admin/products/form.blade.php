@csrf

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="text-sm font-bold text-forest" for="category_id">Kategori</label>
        <select class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="category_id" name="category_id" required>
            <option value="">Seçin</option>
            @foreach ($categories as $categoryOption)
                <option value="{{ $categoryOption->id }}" @selected(old('category_id', $product->category_id) == $categoryOption->id)>{{ $categoryOption->name }}</option>
            @endforeach
        </select>
        @error('category_id')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-sm font-bold text-forest" for="name">Ürün adı</label>
        <input class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        @error('name')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-sm font-bold text-forest" for="slug">Slug <span class="font-normal text-ink/50">(boşsa otomatik)</span></label>
        <input class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
        @error('slug')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-sm font-bold text-forest" for="sku">SKU</label>
        <input class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
        @error('sku')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="text-sm font-bold text-forest" for="short_description">Kısa açıklama</label>
        <textarea class="mt-2 min-h-20 w-full rounded-xl border border-forest/20 p-3" id="short_description" name="short_description" required>{{ old('short_description', $product->short_description) }}</textarea>
        @error('short_description')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="text-sm font-bold text-forest" for="description">Detaylı açıklama</label>
        <textarea class="mt-2 min-h-28 w-full rounded-xl border border-forest/20 p-3" id="description" name="description">{{ old('description', $product->description) }}</textarea>
        @error('description')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-sm font-bold text-forest" for="price">Normal fiyat</label>
        <input class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="price" name="price" type="number" min="0.01" step="0.01" value="{{ old('price', $product->price) }}" required>
        @error('price')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-sm font-bold text-forest" for="sale_price">İndirimli fiyat</label>
        <input class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="sale_price" name="sale_price" type="number" min="0.01" step="0.01" value="{{ old('sale_price', $product->sale_price) }}">
        @error('sale_price')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-sm font-bold text-forest" for="stock_status">Stok durumu</label>
        <select class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="stock_status" name="stock_status">
            <option value="in_stock" @selected(old('stock_status', $product->stock_status) === 'in_stock')>Stokta</option>
            <option value="pre_order" @selected(old('stock_status', $product->stock_status) === 'pre_order')>Ön sipariş</option>
            <option value="out_of_stock" @selected(old('stock_status', $product->stock_status) === 'out_of_stock')>Stokta yok</option>
        </select>
        @error('stock_status')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-sm font-bold text-forest" for="sort_order">Sıralama</label>
        <input class="mt-2 min-h-11 w-full rounded-xl border border-forest/20 px-3" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $product->sort_order ?? 0) }}" required>
        @error('sort_order')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-wrap gap-5 sm:col-span-2">
        <label class="flex min-h-11 items-center gap-3 text-sm font-bold text-forest"><input name="is_active" type="hidden" value="0"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active ?? true))> Aktif</label>
        <label class="flex min-h-11 items-center gap-3 text-sm font-bold text-forest"><input name="is_featured" type="hidden" value="0"><input name="is_featured" type="checkbox" value="1" @checked(old('is_featured', $product->is_featured ?? false))> Öne çıkar</label>
    </div>

    <div class="sm:col-span-2">
        <label class="text-sm font-bold text-forest" for="images">Yeni görseller <span class="font-normal text-ink/50">(en fazla 10, JPG/PNG/WebP)</span></label>
        <input class="mt-2 block w-full text-sm" id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
        @error('images')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
        @error('images.*')<p class="mt-1 text-sm text-rose">{{ $message }}</p>@enderror
    </div>
</div>

@if ($product->exists)
    <section class="mt-8 border-t border-forest/10 pt-6">
        <h2 class="font-display text-2xl font-bold text-forest">Mevcut görseller</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($product->images as $image)
                <article class="rounded-2xl border border-forest/10 p-3">
                    <img class="aspect-square w-full rounded-xl object-cover" src="{{ $image->imageUrl() }}" alt="{{ $image->alt_text ?: $product->name }}">
                    <label class="mt-3 flex items-center gap-2 text-sm font-bold"><input type="radio" name="primary_image_id" value="{{ $image->id }}" @checked(old('primary_image_id', $product->images->firstWhere('is_primary', true)?->id) == $image->id)> Ana görsel</label>
                    <label class="mt-3 block text-sm font-bold">Alt metin<input class="mt-1 min-h-10 w-full rounded-lg border border-forest/20 px-2 font-normal" name="image_alts[{{ $image->id }}]" value="{{ old('image_alts.'.$image->id, $image->alt_text) }}"></label>
                    <label class="mt-3 block text-sm font-bold">Sıra<input class="mt-1 min-h-10 w-full rounded-lg border border-forest/20 px-2 font-normal" name="image_order[{{ $image->id }}]" type="number" min="0" value="{{ old('image_order.'.$image->id, $image->sort_order) }}"></label>
                    <button class="mt-3 min-h-10 text-sm font-bold text-rose underline" form="delete-image-{{ $image->id }}" type="submit">Görseli sil</button>
                </article>
            @empty
                <p class="text-sm text-ink/60">Henüz görsel yok.</p>
            @endforelse
        </div>
    </section>
@endif

<div class="mt-7 flex flex-wrap gap-3">
    <button class="min-h-11 rounded-xl bg-forest px-5 text-sm font-bold text-cream">Kaydet</button>
    <a class="inline-flex min-h-11 items-center rounded-xl border border-forest/20 px-5 text-sm font-bold text-forest" href="{{ route('admin.products.index') }}">İptal</a>
</div>

@if ($product->exists)
    @foreach ($product->images as $image)
        <form id="delete-image-{{ $image->id }}" method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" onsubmit="return confirm('Bu görseli silmek istediğinizden emin misiniz?')">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif
