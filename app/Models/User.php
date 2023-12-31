<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\Mode;
use App\Facades\ActivityManager;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    function stats()
    {
        return $this->hasMany(UserStats::class);
    }

    function scores()
    {
        return $this->hasMany(Score::class);
    }

    function getUserStats(Mode $mode)
    {
        return $this->stats()->where('mode', $mode->value)->first();
    }

    function rankHistory()
    {
        return $this->hasMany(UserRankHistory::class);
    }

    function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    function buildActivities(): Array
    {
        $built = [];
        $this->activities->map(function ($activity) use (&$built) {
            $built[] = ActivityManager::handleActivity($activity);
        });

        return $built;
    }
}
