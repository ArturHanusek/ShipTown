<?php

return [
    'when' => [
        [
            'class' => \App\Events\Order\ActiveOrderCheckEvent::class,
            'description' => 'On Active Order Event',
            'conditions' => [
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition::class,
                    'description' => 'Status Code is',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeNotInCondition::class,
                    'description' => 'Status Code Not In',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition::class,
                    'description' => 'Can Fulfill from location (0 for all)',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanNotFulfillFromLocationCondition::class,
                    'description' => 'Can NOT Fulfill from location (0 for all)',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition::class,
                    'description' => 'Order Shipping Method Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\LineCountEqualsCondition::class,
                    'description' => 'Line count equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\TotalQuantityToShipEqualsCondition::class,
                    'description' => 'Total Quantity To Ship',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\IsPartiallyPaidCondition::class,
                    'description' => 'Is Partially Paid',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPaidCondition::class,
                    'description' => 'Is Fully Paid',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\IsFullyPickedCondition::class,
                    'description' => 'Is Fully Picked',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPackedCondition::class,
                    'description' => 'Is Fully Packed',
                ],
            ],
            'actions' => [
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\SetStatusCodeAction::class,
                    'description' => 'Set Status Code',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\AddOrderCommentAction::class,
                    'description' => 'Add order comment',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\LogMessageAction::class,
                    'description' => 'Add log message',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\SetLabelTemplateAction::class,
                    'description' => 'Set courier label template',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\SplitOrderToWarehouseCodeAction::class,
                    'description' => 'Split Order to warehouse code',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\ShipRemainingProductsAction::class,
                    'description' => 'Mark remaining products as shipped',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\PushToBoxTopOrderAction::class,
                    'description' => 'Create Warehouse Shipment in BoxTop Software',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\SendEmailToCustomerAction::class,
                    'description' => 'Send shipment email to customer',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\SplitBundleSkuAction::class,
                    'description' => 'Split bundle SKU (format: BundleSKU,SKU1,SKU2...)',
                ],
            ]
        ],
    ],
];
