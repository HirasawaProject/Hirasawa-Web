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

    function attachActivity(User $user, String $activityKey, array $params = []): UserActivity
    {
        if (!array_key_exists($activityKey, $this->registeredActivities)) {
            throw new \Exception("Activity not registered");
        }

        $this->registerActivity[$activityKey]->getRequiredParams()->each(function ($requiredParam) use ($params) {
            if (!array_key_exists($requiredParam, $params)) {
                throw new \Exception("Missing required parameter: $requiredParam");
            }
        });

        $activity = new UserActivity();
        $activity->user_id = $user->id;
        $activity->key = $activityKey;
        $activity->params = $params;
        $activity->save();

        return $activity;
    }
}