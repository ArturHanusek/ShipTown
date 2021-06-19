<?php


namespace App\Modules\Webhooks\src;

use App\Http\Controllers\SnsController;
use Aws\Exception\AwsException;
use Exception;
use Illuminate\Support\Facades\Log;

class AwsSns
{
    /**
     * @param string $topic
     * @param string $message
     * @return bool
     */
    public static function publish(string $topic, string $message): bool
    {
        $snsTopic = new SnsController($topic);

        try {
            return $snsTopic->publish($message);
        } catch (AwsException $e) {
            Log::error("Could not publish SNS message", [
                "code" => $e->getStatusCode(),
                "return_message" => $e->getMessage(),
                "topic" => $topic,
                "message" => $message,
            ]);
            return false;
        } catch (Exception $e) {
            Log::error("Could not publish SNS message", [
                "code" => $e->getCode(),
                "return_message" => $e->getMessage(),
                "topic" => $topic,
                "message" => $message,
            ]);
            return false;
        }
    }
}
