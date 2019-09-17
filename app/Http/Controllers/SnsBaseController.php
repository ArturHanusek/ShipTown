<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;
use Log;
use Aws\Exception\AwsException;

class SnsBaseController extends Controller
{
    private $awsSnsClient;
    protected $topicNamePrefix = "";

    public function __construct()
    {
        $this->awsSnsClient = AWS::createClient('sns');
    }

    function publishMessage($message){

        $result = $this->awsSnsClient->publish([
            'Message'   => $message,
            'TargetArn' => $this->getTargetArn()
        ]);

        return $result['MessageId'];
    }

    function getUserSpecificTopicName(){

        $userID = auth('api')->user()->id;

        return "snsTopic_".$this->topicNamePrefix."_user".$userID;
    }

    public function getTargetArn()
    {
        return "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE').":".$this->getUserSpecificTopicName();
    }

    public function createSnsTopic(Request $request) {

        $topic = $request->getContent();

        try {
            $this->awsSnsClient->createTopic([
                'Name' => $this->getUserSpecificTopicName()
            ]);

        } catch (AwsException $e) {
            Log::alert("AWS error: ".$e);
            return response()->json("AWS error: ".$e);
        }

        return response()->json("Successfully created topic", 200);
    }

    public function subscribeToTopic(Request $request) {
        $subscribeUrl = $request->getContent();

        try {
            $result = $this->awsSnsClient->subscribe([
                'Protocol' => 'https',
                'Endpoint' => $subscribeUrl,
                'ReturnSubscriptionArn' => true,
                'TopicArn' => $this->getTargetArn(),
            ]);

        } catch (AwsException $e) {
            return response()->json("AWS error: ".$e);

        }

        return response()->json("Successfully subscribed '".$subscribeUrl."' to products topic", 200);
    }

    function validation($message){
        return true;
    }
}
