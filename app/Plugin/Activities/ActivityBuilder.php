<?php

namespace App\Plugin\Activities;

use App\Models\UserActivity;

interface ActivityBuilder
{
    public function build(UserActivity $activity): String;
}