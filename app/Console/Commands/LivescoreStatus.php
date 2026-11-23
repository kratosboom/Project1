<?php

namespace App\Console\Commands;

use App\Services\ApiSports\ApiSportsException;
use App\Services\ApiSports\FootballClient;
use Illuminate\Console\Command;

class LivescoreStatus extends Command
{
    protected $signature = 'livescore:status';

    protected $description = 'Cek paket & pemakaian akun API-Sports (tidak memotong kuota).';

    public function handle(FootballClient $api): int
    {
        try {
            $s = $api->status();
        } catch (ApiSportsException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->table(['Item', 'Nilai'], [
            ['Paket', data_get($s, 'subscription.plan', '-')],
            ['Aktif', data_get($s, 'subscription.active') ? 'ya' : 'tidak'],
            ['Berakhir', data_get($s, 'subscription.end', '-')],
            ['Request hari ini', data_get($s, 'requests.current', '-').' / '.data_get($s, 'requests.limit_day', '-')],
        ]);

        return self::SUCCESS;
    }
}
