<?php

namespace App\Plugin\Activities;

use App\Models\UserActivity;

class ActivityManager
{
    private array $registeredActivities = [];

    function registerActivity(String $activityKey, ActivityBuilder $activityBuilder)
    {
        $this->registeredActivities[$activityKey] = $activityBuilder;
    }

    function handleActivity(UserActivity $activity): String
    {
        if (!array_key_exists($activity->key, $this->registeredActivities)) {
            throw new \Exception("Activity not registered");
        }

        return $this->registeredActivities[$activityKey]->build($activity);
    }
}