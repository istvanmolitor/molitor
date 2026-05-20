<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Molitor\Stock\Models\Warehouse;
use Tests\TestCase;

class StockApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('stock_movement_items');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('warehouse_region_products');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('warehouse_regions');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();

        Schema::create('warehouses', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_primary')->default(false);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('warehouse_regions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('name');
            $table->boolean('is_primary')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('sku');
            $table->string('slug')->nullable();
            $table->decimal('price')->default(0);
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('product_unit_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stocks', function (Blueprint $table): void {
            $table->foreignId('warehouse_region_id')->constrained('warehouse_regions');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity');
            $table->primary(['warehouse_region_id', 'product_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->unsignedBigInteger('linked_stock_movement_id')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movement_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('stock_movement_id')->constrained('stock_movements');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_region_id')->constrained('warehouse_regions');
            $table->unsignedBigInteger('destination_warehouse_region_id')->nullable();
            $table->foreign('destination_warehouse_region_id')->references('id')->on('warehouse_regions');
            $table->decimal('quantity');
            $table->timestamps();
        });
    }

    public function test_can_manage_warehouses(): void
    {
        Sanctum::actingAs(User::factory()->make());

        $createResponse = $this->postJson('/api/admin/stock/warehouses', [
            'name' => 'Központi raktár',
            'description' => 'Fő telephely',
            'is_primary' => true,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.name', 'Központi raktár');

        $warehouseId = $createResponse->json('data.id');

        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouseId,
            'name' => 'Központi raktár',
        ]);

        $updateResponse = $this->putJson("/api/admin/stock/warehouses/{$warehouseId}", [
            'name' => 'Frissített raktár',
            'description' => 'Frissített leírás',
            'is_primary' => false,
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('data.name', 'Frissített raktár');

        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouseId,
            'name' => 'Frissített raktár',
        ]);

        $deleteResponse = $this->deleteJson("/api/admin/stock/warehouses/{$warehouseId}");

        $deleteResponse->assertOk();
        $this->assertDatabaseMissing('warehouses', ['id' => $warehouseId]);
    }

    public function test_can_manage_warehouse_regions(): void
    {
        Sanctum::actingAs(User::factory()->make());

        $warehouse = Warehouse::query()->create([
            'name' => 'Raktár 1',
            'description' => 'Teszt raktár',
            'is_primary' => false,
        ]);

        $createResponse = $this->postJson('/api/admin/stock/warehouse-regions', [
            'warehouse_id' => $warehouse->id,
            'name' => 'Első régió',
            'description' => 'Teszt régió',
            'is_primary' => true,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.name', 'Első régió');

        $regionId = $createResponse->json('data.id');

        $this->assertDatabaseHas('warehouse_regions', [
            'id' => $regionId,
            'name' => 'Első régió',
            'warehouse_id' => $warehouse->id,
        ]);

        $updateResponse = $this->putJson("/api/admin/stock/warehouse-regions/{$regionId}", [
            'warehouse_id' => $warehouse->id,
            'name' => 'Frissített régió',
            'description' => 'Frissített leírás',
            'is_primary' => false,
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('data.name', 'Frissített régió');

        $this->assertDatabaseHas('warehouse_regions', [
            'id' => $regionId,
            'name' => 'Frissített régió',
        ]);

        $deleteResponse = $this->deleteJson("/api/admin/stock/warehouse-regions/{$regionId}");

        $deleteResponse->assertOk();
        $this->assertDatabaseMissing('warehouse_regions', ['id' => $regionId]);
    }

    public function test_can_create_stock_movement_draft_and_execute_in(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $warehouse = Warehouse::query()->create(['name' => 'Raktár', 'is_primary' => true]);
        $regionId = DB::table('warehouse_regions')->insertGetId([
            'warehouse_id' => $warehouse->id, 'name' => 'A régió', 'is_primary' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'sku' => 'SKU-001', 'slug' => 'sku-001', 'price' => 0, 'active' => true,
            'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
        ]);

        $createResponse = $this->postJson('/api/admin/stock/movements', [
            'type' => 'in',
            'description' => 'Bevételezés',
            'items' => [['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 10]],
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('message', 'A készletmozgás sikeresen rögzítve lett.')
            ->assertJsonPath('data.is_closed', false);

        $movementId = $createResponse->json('data.id');
        $this->assertDatabaseMissing('stocks', ['warehouse_region_id' => $regionId, 'product_id' => $productId]);

        $executeResponse = $this->postJson("/api/admin/stock/movements/{$movementId}/execute");
        $executeResponse->assertOk()
            ->assertJsonPath('message', 'A készletmozgás sikeresen végrehajtva lett.')
            ->assertJsonPath('data.is_closed', true);

        $this->assertDatabaseHas('stocks', ['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 10]);
    }

    public function test_can_update_draft_before_execute(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $warehouse = Warehouse::query()->create(['name' => 'Raktár', 'is_primary' => true]);
        $regionId = DB::table('warehouse_regions')->insertGetId([
            'warehouse_id' => $warehouse->id, 'name' => 'A régió', 'is_primary' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'sku' => 'SKU-002', 'slug' => 'sku-002', 'price' => 0, 'active' => true,
            'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
        ]);

        $createResponse = $this->postJson('/api/admin/stock/movements', [
            'type' => 'in',
            'items' => [['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 5]],
        ]);
        $movementId = $createResponse->json('data.id');

        $updateResponse = $this->putJson("/api/admin/stock/movements/{$movementId}", [
            'type' => 'in',
            'description' => 'Módosított leírás',
            'items' => [['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 20]],
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('message', 'A készletmozgás sikeresen frissítve lett.');

        $this->assertDatabaseHas('stock_movement_items', ['stock_movement_id' => $movementId, 'quantity' => 20]);
    }

    public function test_cannot_execute_out_when_not_enough_stock(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $warehouse = Warehouse::query()->create(['name' => 'Raktár', 'is_primary' => true]);
        $regionId = DB::table('warehouse_regions')->insertGetId([
            'warehouse_id' => $warehouse->id, 'name' => 'A régió', 'is_primary' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'sku' => 'SKU-003', 'slug' => 'sku-003', 'price' => 0, 'active' => true,
            'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
        ]);
        DB::table('stocks')->insert(['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 2]);

        $createResponse = $this->postJson('/api/admin/stock/movements', [
            'type' => 'out',
            'items' => [['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 3]],
        ]);
        $movementId = $createResponse->json('data.id');

        $this->postJson("/api/admin/stock/movements/{$movementId}/execute")->assertUnprocessable();

        $this->assertDatabaseHas('stocks', ['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 2]);
    }

    public function test_can_transfer_stock_between_regions(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $warehouse = Warehouse::query()->create(['name' => 'Raktár', 'is_primary' => true]);
        $sourceId = DB::table('warehouse_regions')->insertGetId([
            'warehouse_id' => $warehouse->id, 'name' => 'Forrás', 'is_primary' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $destId = DB::table('warehouse_regions')->insertGetId([
            'warehouse_id' => $warehouse->id, 'name' => 'Cél', 'is_primary' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'sku' => 'SKU-004', 'slug' => 'sku-004', 'price' => 0, 'active' => true,
            'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
        ]);
        DB::table('stocks')->insert(['warehouse_region_id' => $sourceId, 'product_id' => $productId, 'quantity' => 12]);

        $createResponse = $this->postJson('/api/admin/stock/movements', [
            'type' => 'transfer',
            'items' => [[
                'warehouse_region_id' => $sourceId,
                'destination_warehouse_region_id' => $destId,
                'product_id' => $productId,
                'quantity' => 5,
            ]],
        ]);
        $movementId = $createResponse->json('data.id');

        $this->postJson("/api/admin/stock/movements/{$movementId}/execute")
            ->assertOk()
            ->assertJsonPath('message', 'A készletmozgás sikeresen végrehajtva lett.');

        $this->assertDatabaseHas('stocks', ['warehouse_region_id' => $sourceId, 'product_id' => $productId, 'quantity' => 7]);
        $this->assertDatabaseHas('stocks', ['warehouse_region_id' => $destId, 'product_id' => $productId, 'quantity' => 5]);
    }

    public function test_can_delete_draft_movement(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $warehouse = Warehouse::query()->create(['name' => 'Raktár', 'is_primary' => true]);
        $regionId = DB::table('warehouse_regions')->insertGetId([
            'warehouse_id' => $warehouse->id, 'name' => 'A régió', 'is_primary' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'sku' => 'SKU-005', 'slug' => 'sku-005', 'price' => 0, 'active' => true,
            'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
        ]);

        $createResponse = $this->postJson('/api/admin/stock/movements', [
            'type' => 'in',
            'items' => [['warehouse_region_id' => $regionId, 'product_id' => $productId, 'quantity' => 5]],
        ]);
        $movementId = $createResponse->json('data.id');

        $this->deleteJson("/api/admin/stock/movements/{$movementId}")->assertOk();
        $this->assertDatabaseMissing('stock_movements', ['id' => $movementId]);
        $this->assertDatabaseMissing('stock_movement_items', ['stock_movement_id' => $movementId]);
    }
}
