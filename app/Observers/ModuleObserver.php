<?php

namespace App\Observers;

use App\Models\Module;

class ModuleObserver
{
    public function saving(Module $module): bool
    {
        if ($module->isAttributeNotChanged('enabled')) {
            return true;
        }

        if ($module->enabled === false) {
            return $module->service_provider_class::enabling();
        }

        if ($module->enabled === true) {
            return $module->service_provider_class::disabling();
        }

        return true;
    }
}
