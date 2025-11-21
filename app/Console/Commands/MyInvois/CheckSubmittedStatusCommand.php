<?php

namespace App\Console\Commands\MyInvois;

use App\Jobs\MyInvois\CheckEinvoiceStatusJob;
use App\Models\Einvoice;
use Illuminate\Console\Command;

class CheckSubmittedStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myinvois:check-status {--limit=20 : Maximum number of submitted einvoices to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of submitted einvoices and dispatch status check jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $this->info('Fetching submitted einvoices...');

        // Get submitted einvoices that haven't reached final status
        $submittedEinvoices = Einvoice::query()
            ->whereIn('status', ['submitted', 'processing'])
            ->whereNotNull('submission_uid')
            ->orderBy('submitted_at')
            ->limit($limit)
            ->get();

        if ($submittedEinvoices->isEmpty()) {
            $this->info('No submitted einvoices pending validation.');

            return self::SUCCESS;
        }

        $this->info("Found {$submittedEinvoices->count()} submitted einvoices. Dispatching status check jobs...");

        $bar = $this->output->createProgressBar($submittedEinvoices->count());
        $bar->start();

        $dispatched = 0;

        foreach ($submittedEinvoices as $einvoice) {
            try {
                CheckEinvoiceStatusJob::dispatch($einvoice);
                $dispatched++;
            } catch (\Exception $e) {
                $this->error("\nFailed to dispatch job for einvoice {$einvoice->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully dispatched {$dispatched} status check jobs.");
        $this->comment('Jobs will be processed by queue workers.');
        $this->comment('Run: php artisan queue:work --queue=myinvois-status');

        return self::SUCCESS;
    }
}
