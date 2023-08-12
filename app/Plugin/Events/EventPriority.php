<?php

namespace App\Plugin\Events;

enum EventPriority: int
{
    case MONITOR = 0;
    case HIGHEST = 1;
    case HIGH = 2;
    case NORMAL = 3;
    case LOW = 4;
    case LOWEST = 5;
}