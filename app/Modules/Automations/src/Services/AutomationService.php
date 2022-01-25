<?php

namespace App\Modules\Automations\src\Services;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\OrderLock;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class AutomationService
{
    /**
     * @param ActiveOrderCheckEvent $event
     */
    public static function runAllAutomations(ActiveOrderCheckEvent $event)
    {
        Automation::where('event_class', get_class($event))
            ->where(['enabled' => true])
            ->orderBy('priority')
            ->get()
            ->each(function (Automation $automation) use ($event) {
                AutomationService::validateAndRunAutomation($automation, $event);
            });
    }

    /**
     * @param Automation $automation
     * @param ActiveOrderCheckEvent $event
     */
    public static function validateAndRunAutomation(Automation $automation, ActiveOrderCheckEvent $event)
    {
        // this will prevent two automation processes running on same order
        try {
            OrderLock::where('created_at', '<', now()->subMinutes(10))
                ->forceDelete();

            /** @var OrderLock $lock */
            $lock = OrderLock::create(['order_id' => $event->order->getKey()]);
        } catch (Exception $exception) {
            // early exit, cannot lock order, automation is already running for it
            return;
        }

        $allConditionsPassed = $automation->allConditionsTrue($event);

        if ($allConditionsPassed === true) {
            $automation->actions()
                ->orderBy('priority')
                ->get()
                ->each(function (Action $action) use ($event) {
                    AutomationService::runAction($action, $event);
                });
        }

        Log::debug('Ran automation', [
            'class' => class_basename($automation),
            'name' => $automation->name,
            'all_conditions_passed' => $allConditionsPassed
        ]);

        $lock->forceDelete();
    }

    /**
     * @param Action $action
     * @param ActiveOrderCheckEvent $event
     */
    private static function runAction(Action $action, ActiveOrderCheckEvent $event): void
    {
        $runAction = new $action->action_class($event);

        $runAction->handle($action->action_value);
    }
}
