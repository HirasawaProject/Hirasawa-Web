<?php

namespace Tests\Feature\Plugin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserActivity;
use App\Models\User;
use App\Facades\ActivityManager;
use App\Plugin\Activities\ActivityBuilder;

class ActivityManagerTest extends TestCase
{
    function testCanAddAndBuildActivity()
    {
        $user = User::factory()->create();
        ActivityManager::registerActivity("echo", new EchoActivity());

        $activity = ActivityManager::attachActivity($user, "echo", [
            "message" => "Hello, world!"
        ]);

        $this->assertEquals("Hello, world!", ActivityManager::handleActivity($activity));
    }

    function testActivityManagerWillThrowExceptionIfActivityNotRegistered()
    {
        $user = User::factory()->create();

        $this->expectException(\Exception::class);
        $activity = ActivityManager::attachActivity($user, "echo", [
            "message" => "Hello, world!"
        ]);
    }

    function testActivityManagerWillThrowExceptionIfActivityMissingRequiredParams()
    {
        $user = User::factory()->create();
        ActivityManager::registerActivity("echo", new EchoActivity());

        $this->expectException(\Exception::class);
        $activity = ActivityManager::attachActivity($user, "echo", [
            "notMessage" => "Hello, world!"
        ]);
    }
}

class EchoActivity implements ActivityBuilder
{
    function build(UserActivity $activity): String
    {
        return $activity->params['message'];
    }

    function getRequiredParams(): Array
    {
        return [
            "message"
        ];
    }
}