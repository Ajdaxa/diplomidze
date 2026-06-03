<?php

namespace App\Console\Commands;

use Database\Seeders\StoreDemoDataSeeder;
use Illuminate\Console\Command;

class SeedStoreDemoCommand extends Command
{
    protected $signature = 'store:seed-demo {--fresh : Удалить ранее созданные демо-данные и заполнить заново}';

    protected $description = 'Заполнить БД демо-отзывами, заказами и промокодами';

    public function handle(): int
    {
        $seeder = new StoreDemoDataSeeder;
        $seeder->setCommand($this);

        if ($this->option('fresh')) {
            $seeder->purgeDemoData();
            $this->info('Старые демо-данные удалены.');
        }

        $seeder->run();

        return self::SUCCESS;
    }
}
