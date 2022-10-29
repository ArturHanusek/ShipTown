<?php

namespace Tests;

use App\Models\CacheLock;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Heartbeat;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductTotal;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Services\ModulesService;
use DebugBar\DataCollector\DataCollector;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Tags\Tag;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use AdditionalAssertions;

    protected function setUp(): void
    {
        parent::setUp();

        ray()->clearAll();
        ray()->className($this)->blue();

        Activity::query()->forceDelete();

        Product::query()->forceDelete();
        Inventory::query()->forceDelete();
        ProductAlias::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        OrderStatus::query()->forceDelete();
        Warehouse::query()->forceDelete();
        Tag::query()->forceDelete();
        OrderProductTotal::query()->forceDelete();
        InventoryMovement::query()->forceDelete();

        Automation::query()->forceDelete();
        Condition::query()->forceDelete();
        Action::query()->forceDelete();
        CacheLock::query()->forceDelete();
        InventoryMovement::query()->forceDelete();
        Heartbeat::query()->forceDelete();

        Module::query()->forceDelete();

        Api2cartProductLink::query()->forceDelete();
        Api2cartConnection::query()->forceDelete();

        RmsapiConnection::query()->forceDelete();
        DataCollection::query()->forceDelete();
        DataCollectionRecord::query()->forceDelete();

        MagentoProduct::query()->forceDelete();

        ModulesService::updateModulesTable();

    }
}
