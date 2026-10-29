<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reminder:update')
    ->everyMinute();
