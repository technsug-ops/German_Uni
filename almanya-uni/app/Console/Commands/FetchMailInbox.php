<?php

namespace App\Console\Commands;

use App\Services\Mail\ImapInbox;
use Illuminate\Console\Command;

class FetchMailInbox extends Command
{
    protected $signature = 'mail:fetch-inbox {--limit=40}';

    protected $description = 'partnerships@ IMAP kutusunu çek (gelen mailler)';

    public function handle(): int
    {
        if (! ImapInbox::available()) {
            $this->warn(ImapInbox::unavailableReason());

            return self::FAILURE;
        }

        try {
            $n = app(ImapInbox::class)->sync((int) $this->option('limit'));
            $this->info("$n yeni mail senkronlandı.");
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
