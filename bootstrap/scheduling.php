<?php

use App\Jobs\UpdateOverdueBookings;
use Illuminate\Support\Facades\Schedule;


Schedule::job(new UpdateOverdueBookings())->dailyAt('00:05');