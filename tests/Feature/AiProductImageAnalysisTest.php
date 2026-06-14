<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AiProductImageAnalysisTest extends TestCase
{
    public function test_upload_page_loads(): void
    {
        $this->get(route('ai.product-image.create'))
            ->assertOk()
            ->assertSee('Test product image upload');
    }

    public function test_image_is_required(): void
    {
        $this->post(route('ai.product-image.store'))
            ->assertSessionHasErrors('image');
    }

    public function test_uploaded_file_must_be_an_image(): void
    {
        $this->post(route('ai.product-image.store'), [
            'image' => UploadedFile::fake()->create('product.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('image');
    }
}
