<?php

namespace Tests\Feature;

use App\Models\ProductTag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductTagOptionalLabelTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_product_tag_label_is_optional_on_create_edit_and_views(): void
    {
        $admin = $this->makeAdmin();

        // 1. Create form does not mark #tagNameInput as required
        $createRes = $this->actingAs($admin)->get(route('product-tags.create'));
        $createRes->assertOk()
            ->assertSee('id="tagNameInput"', false)
            ->assertSee('Optionnel');

        $html = $createRes->getContent();
        $pos = strpos($html, 'id="tagNameInput"');
        $this->assertNotFalse($pos);
        $tagInputSnippet = substr($html, max(0, $pos - 120), 360);
        $this->assertStringNotContainsString(' required', $tagInputSnippet);

        // 2. Submitting an icon-only tag with empty name succeeds and assigns unique slugs
        $payload = [
            'name' => '',
            'icon' => 'fas fa-bolt',
            'style_type' => 'solid',
            'bg_color' => '#dc2626',
            'text_color' => '#ffffff',
            'shape' => 'pill',
            'position' => 'top-left',
            'size_scale' => 100,
            'font_weight' => '800',
            'status' => 'active',
        ];

        $this->actingAs($admin)->post(route('product-tags.store'), $payload)
            ->assertRedirect(route('product-tags.index'));

        $firstEmptyTag = ProductTag::where('icon', 'fas fa-bolt')->latest('id')->firstOrFail();
        $this->assertSame('', $firstEmptyTag->name);
        $this->assertNotEmpty($firstEmptyTag->slug);

        // Creating a second tag with omitted name also succeeds cleanly with a unique slug
        unset($payload['name']);
        $payload['icon'] = 'fas fa-fire';
        $this->actingAs($admin)->post(route('product-tags.store'), $payload)
            ->assertRedirect(route('product-tags.index'));

        $secondEmptyTag = ProductTag::where('icon', 'fas fa-fire')->latest('id')->firstOrFail();
        $this->assertSame('', $secondEmptyTag->name);
        $this->assertNotSame($firstEmptyTag->slug, $secondEmptyTag->slug);

        // 3. Updating an existing named tag to empty label succeeds
        $namedTag = ProductTag::create([
            'name' => 'TEMP LABEL',
            'icon' => 'fas fa-star',
            'style_type' => 'solid',
            'bg_color' => '#2563eb',
            'text_color' => '#ffffff',
            'shape' => 'rounded',
            'position' => 'top-right',
            'size_scale' => 100,
            'font_weight' => '700',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->put(route('product-tags.update', $namedTag), [
            'name' => '   ',
            'icon' => 'fas fa-star',
            'style_type' => 'solid',
            'bg_color' => '#2563eb',
            'text_color' => '#ffffff',
            'shape' => 'rounded',
            'position' => 'top-right',
            'size_scale' => 100,
            'font_weight' => '700',
            'status' => 'active',
        ])->assertRedirect(route('product-tags.index'));

        $namedTag->refresh();
        $this->assertSame('', $namedTag->name);

        // 4. Index and edit views render icon-only tag cleanly without required attribute
        $this->actingAs($admin)->get(route('product-tags.index'))
            ->assertOk()
            ->assertSee('Badge sans texte');

        $editRes = $this->actingAs($admin)->get(route('product-tags.edit', $namedTag));
        $editRes->assertOk()->assertSee('id="tagNameInput"', false);
        $editHtml = $editRes->getContent();
        $editPos = strpos($editHtml, 'id="tagNameInput"');
        $this->assertNotFalse($editPos);
        $editSnippet = substr($editHtml, max(0, $editPos - 120), 360);
        $this->assertStringNotContainsString(' required', $editSnippet);
    }
}
