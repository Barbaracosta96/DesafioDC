<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function create(array $data): Product
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $this->storeImage($data['image']);
        }
        unset($data['image']);

        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku($data['name']);
        }

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Remove old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $this->storeImage($data['image']);
        }
        unset($data['image']);

        $product->update($data);

        return $product->fresh('category');
    }

    public function delete(Product $product): void
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('products', 'public');
    }

    private function generateSku(string $name): string
    {
        return strtoupper(Str::slug($name, '-')) . '-' . strtoupper(Str::random(4));
    }
}
