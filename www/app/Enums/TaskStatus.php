<?php namespace App\Enums;

enum TaskStatus: int
{
    case Pending = 1;
    case InProgress = 2;
    case Completed = 3;
}
