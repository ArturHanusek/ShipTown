<?php

use App\Models\Product;
use App\Models\ProductAlias;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createSkuWithAliases([
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '06',
        ]);

        factory(Product::class, 100)->create();
    }

    private function createSkuWithAliases(array $skuList): void
    {
        foreach ($skuList as $sku) {

            if (!Product::query()->where('sku', '=', $sku)->exists()) {

                $product = factory(Product::class)->create(['sku' => $sku]);

                factory(ProductAlias::class)->create([
                    'product_id' => $product->getKey(),
                    'alias' => $product->sku.'-alias'
                ]);

                factory(ProductAlias::class)->create([
                    'product_id' => $product->getKey(),
                    'alias' => $product->sku.'a'
                ]);
            }

        }
    }
}
