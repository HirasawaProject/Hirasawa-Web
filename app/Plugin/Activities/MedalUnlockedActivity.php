<?php

namespace App\Plugin\Activities;

use App\Models\UserActivity;

class RankActivity implements ActivityBuilder
{
    function build(UserActivity $activity): String
    {
        $username = $activity->user->username;
        $medalName = $activity->params->medalName;

        return "$username unlocked the \"$medalName\" medal!";
    }

    function getRequiredParams(): Array
    {
        return [
            "medalName"
        ];
    }
}