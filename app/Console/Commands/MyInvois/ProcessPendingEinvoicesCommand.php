<?php

namespace App\Console\Commands\MyInvois;

use App\Jobs\MyInvois\SubmitEinvoiceJob;
use App\Models\Einvoice;
use Illuminate\Console\Command;

class ProcessPendingEinvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myinvois:process-pending {--limit=50 : Maximum number of pending einvoices to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending einvoices and dispatch submission jobs to queue';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $this->info('Fetching pending einvoices...');

        // Get pending einvoices that can be retried
        $pendingEinvoices = Einvoice::query()
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere(function ($q) {
                        $q->where('status', 'error')
                            ->where('retry_count', '<', 5);
                    });
            })
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        if ($pendingEinvoices->isEmpty()) {
            $this->info('No pending einvoices found.');

            return self::SUCCESS;
        }

        $this->info("Found {$pendingEinvoices->count()} pending einvoices. Dispatching jobs...");

        $bar = $this->output->createProgressBar($pendingEinvoices->count());
        $bar->start();

        $dispatched = 0;

        foreach ($pendingEinvoices as $einvoice) {
            try {
                SubmitEinvoiceJob::dispatch($einvoice);
                $dispatched++;
            } catch (\Exception $e) {
                $this->error("\nFailed to dispatch job for einvoice {$einvoice->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully dispatched {$dispatched} jobs to the queue.");
        $this->comment('Jobs will be processed by queue workers.');
        $this->comment('Run: php artisan queue:work --queue=myinvois-submissions,myinvois-status');

        return self::SUCCESS;
    }
}
