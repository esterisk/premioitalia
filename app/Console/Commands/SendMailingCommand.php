<?php

namespace App\Console\Commands;

use App\Models\Annata;
use App\Models\User;
use Illuminate\Console\Command;

class SendMailingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Controlla se ci sono mailing da eseguire e se necessario li esegue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $annata = Annata::corrente();
        ray('Anno ' . $annata->anno . ' ' . $annata->mailing_status . "\n");
        switch ($annata->mailing_status) {
            case 'access-waiting':
            case 'access-sending':
                User::accessMailingStart();
                break;
            case 'access-checking':
                $annata->accessMailingProblem();
                break;
            case 'reminder-waiting':
                User::remainderMailingStart();
                break;
            case 'reminder-sending':
                User::remainderMailingCheck();
                break;
            case 'reminder-checking':
                User::remainderMailingProblem();
                break;
        }
        return Command::SUCCESS;
    }
}
