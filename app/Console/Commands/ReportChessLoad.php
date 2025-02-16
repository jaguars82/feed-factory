<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReportChessLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chess:load-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a report with information about the last chess update session';

    protected $recipients = [
        'info@teacher-site.ru',
        'jaguars82@yandex.ru',
        'forteachersite@gmail.com',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Executing the report...');

        /*Mail::raw('Отчет об обновлении шахматок', function ($message) {
            $message->to($this->recipients)
                    ->subject('Обновление шахматок');
        });*/

        return Command::SUCCESS;
    }
}
