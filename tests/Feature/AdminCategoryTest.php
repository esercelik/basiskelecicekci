<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_and_update_category_with_automatic_unique_slug(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Özel Gün', 'slug' => 'ozel-gun']);

        $this->actingAs($admin)->post(route('admin.categories.store'), ['name' => 'Özel Gün', 'slug' => '', 'description' => '<b>Not</b>', 'is_active' => true, 'sort_order' => 3]);
        $category = Category::query()->where('slug', 'ozel-gun-2')->firstOrFail();
        $this->assertSame('Not', $category->description);
        $this->put(route('admin.categories.update', $category), ['name' => 'Yeni Ad', 'slug' => 'yeni-ad', 'is_active' => false, 'sort_order' => 5])->assertRedirect(route('admin.categories.edit', $category->fresh()));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'slug' => 'yeni-ad', 'is_active' => false]);
    }

    public function test_regular_user_cannot_manage_categories_and_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();
        $this->actingAs(User::factory()->create())->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->delete(route('admin.categories.destroy', $category))->assertSessionHasErrors('category');
        $this->assertModelExists($category);
    }

    public function test_category_image_must_be_a_safe_raster_image(): void
    {
        $this->actingAs(User::factory()->admin()->create())->from(route('admin.categories.create'))->post(route('admin.categories.store'), ['name' => 'Test', 'slug' => 'test', 'is_active' => true, 'sort_order' => 0, 'image' => UploadedFile::fake()->create('bad.svg', 10, 'image/svg+xml')])->assertSessionHasErrors('image');
    }
}
