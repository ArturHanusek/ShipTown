<?php

namespace Tests\Feature\Modules\StocktakeSuggestions;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use App\Modules\StocktakeSuggestions\src\Jobs\OutdatedCountsJob;
use App\Modules\StocktakeSuggestions\src\Models\StocktakeSuggestionsConfiguration;
use App\Modules\StocktakeSuggestions\src\StocktakeSuggestionsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function test_in_stock_since()
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::query()->updateOrCreate([], [
            'min_count_date' => now()->subWeek()
        ]);

        $inventory = Inventory::factory()->create();

        $inventory->update([
            'last_counted_at' => now()->subMonth(),
            'in_stock_since' => now()->subMonth(),
            'quantity' => 1,
        ]);

        OutdatedCountsJob::dispatch();

        $this->assertEquals(1, StocktakeSuggestion::query()->count());

        $inventory->update([
            'last_counted_at' => now()->subMonth(),
            'in_stock_since' => now()->subDay(),
            'quantity' => 1,
        ]);

        OutdatedCountsJob::dispatch();

        $this->assertEquals(0, StocktakeSuggestion::query()->count());
    }

    public function test_in_stock_since_doesnt_add_suggestion()
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subWeek()]);

        Inventory::factory()->create()->update([
            'last_counted_at' => now()->subMonth(),
            'first_movement_at' => now()->subMonth(),
            'in_stock_since' => now()->subDay(),
            'quantity' => 1,
        ]);

        $this->assertEquals(0, StocktakeSuggestion::query()->count());
    }

    public function test_outdated_counts_suggestion_adding()
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subWeek()]);

        Inventory::factory()->create()->update([
            'last_counted_at' => now()->subMonth(),
            'first_movement_at' => now()->subMonth(),
            'in_stock_since' => now()->subMonth(),
            'quantity' => 1,
        ]);

        $this->assertEquals(1, StocktakeSuggestion::query()->count());
    }

    public function test_outdated_counts_suggestion()
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subDay()]);

        Inventory::factory()->create()->update([
            'last_counted_at' => now()->subMonth(),
            'in_stock_since' => now()->subMonth(),
            'quantity' => 1,
        ]);

        Inventory::factory()->create()->update([
            'last_counted_at' => null,
            'in_stock_since' => now()->subMonth(),
            'quantity' => 1,
        ]);

        Inventory::factory()->create()->update([
            'last_counted_at' => null,
            'in_stock_since' => now(),
            'quantity' => 1,
        ]);

        $this->assertEquals(2, StocktakeSuggestion::count());
    }

    public function test_outdated_counts_job()
    {
        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subDay()]);

        Inventory::factory()->create()->update([
            'last_counted_at' => now()->subMonth(),
            'in_stock_since' => now()->subMonth(),
            'quantity' => 1,
        ]);

        Inventory::factory()->create()->update([
            'last_counted_at' => null,
            'in_stock_since' => now()->subMonth(),
            'quantity' => 1,
        ]);

        Inventory::factory()->create()->update([
            'last_counted_at' => null,
            'in_stock_since' => now(),
            'quantity' => 1,
        ]);

        OutdatedCountsJob::dispatch();

        $this->assertEquals(2, StocktakeSuggestion::count());
    }

    /** @test */
    public function test_module_basic_functionality()
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $inventory = $product->inventory()->first();

        StocktakeSuggestion::query()->create([
            'product_id' => $product->id,
            'inventory_id' => $inventory->id,
            'points' => 1,
            'reason' => 'test',
        ]);

        StocktakeSuggestion::query()->create([
            'product_id' => $product->id,
            'inventory_id' => $inventory->id,
            'points' => 1,
            'reason' => 'test2',
        ]);

        $this->assertCount(2, StocktakeSuggestion::query()->get());
    }
}
