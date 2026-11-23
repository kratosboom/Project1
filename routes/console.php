<?php

use Illuminate\Support\Facades\Schedule;

/*
| Jadwal sinkron livescore. Di server cukup satu cron:
|   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
| Default disesuaikan paket Free API-Sports (~76 request/hari), atur di .env.
*/
$cron = config('livescore.schedule');

Schedule::command('livescore:sync today')->cron($cron['today'])->withoutOverlapping()->onOneServer();
Schedule::command('livescore:sync around')->cron($cron['around'])->withoutOverlapping()->onOneServer();
Schedule::command('livescore:sync details')->cron($cron['details'])->withoutOverlapping()->onOneServer();
Schedule::command('livescore:sync standings')->cron($cron['standings'])->withoutOverlapping()->onOneServer();
Schedule::command('livescore:sync leagues')->cron($cron['leagues'])->withoutOverlapping()->onOneServer();
