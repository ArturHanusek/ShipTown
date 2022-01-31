<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityTrait
{
    use LogsActivity;

    protected static bool $logOnlyDirty = true;
    protected static bool $submitEmptyLogs = false;

    public function log($message, array $properties = []): self
    {
        activity()->on($this)
            ->withProperties($properties)
            ->log($message);

        Log::debug('Activity', [
            'message' => $message,
            'id' => $this->getKey(),
            'class' => get_class($this)
        ]);

        return $this;
    }
}
