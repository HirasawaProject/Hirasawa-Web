<?php

namespace App\Plugin\Activities;

use App\Models\UserActivity;
use App\Models\User;

class ActivityManager
{
    private array $registeredActivities = [];

    function registerActivity(String $activityKey, ActivityBuilder $activityBuilder)
    {
        $this->registeredActivities[$activityKey] = $activityBuilder;
    }

    function handleActivity(UserActivity $activity): String
    {
        if (!array_key_exists($activity->activity_key, $this->registeredActivities)) {
            throw new \Exception("Activity not registered");
        }

        return $this->registeredActivities[$activity->activity_key]->build($activity);
    }

    function attachActivity(User $user, String $activityKey, array $params = []): UserActivity
    {
        if (!array_key_exists($activityKey, $this->registeredActivities)) {
            throw new \Exception("Activity not registered");
        }

        foreach($this->registeredActivities[$activityKey]->getRequiredParams() as $requiredParam) {
            if (!array_key_exists($requiredParam, $params)) {
                throw new \Exception("Missing required parameter: $requiredParam");
            }
        }

        $activity = new UserActivity();
        $activity->user_id = $user->id;
        $activity->activity_key = $activityKey;
        $activity->params = $params;
        $activity->save();

        return $activity;
    }
}