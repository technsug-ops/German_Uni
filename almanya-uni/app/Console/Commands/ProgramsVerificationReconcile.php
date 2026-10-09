<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\ProgramVerification;
use Illuminate\Console\Command;

/**
 * Doğrulanmış (VERIFIED) program alanlarını mevcut değerle karşılaştırır; değer doğrulamadan sonra değiştiyse
 * kaydı NEEDS_REVIEW yapar. Eloquent ile yazan importlar bunu Program::saved'da zaten yapar; bu komut query-builder
 * ile yazan akışlar (programs:fix-deadlines, parse-deadlines …) için günlük güvenlik ağıdır. Yalnız VERIFIED
 * kaydı olan programları tarar; hiçbir kaydı VERIFIED yapmaz, verified_at'e dokunmaz. İdempotent.
 */
class ProgramsVerificationReconcile extends Command
{
    protected $signature = 'programs:verification-reconcile';

    protected $description = 'Doğrulanmış program alanlarında sonradan değişen değerleri NEEDS_REVIEW olarak işaretler';

    public function handle(): int
    {
        $ids = ProgramVerification::where('status', ProgramVerification::VERIFIED)->distinct()->pluck('program_id');
        $flagged = 0;
        Program::whereIn('id', $ids)->get()->each(function (Program $p) use (&$flagged) {
            $flagged += ProgramVerification::reconcile($p);
        });
        $this->info("Taranan program: {$ids->count()} · NEEDS_REVIEW yapılan alan: {$flagged}");

        return self::SUCCESS;
    }
}
