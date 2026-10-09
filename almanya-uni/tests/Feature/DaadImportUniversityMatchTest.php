<?php

namespace Tests\Feature;

use App\Console\Commands\DaadImport;
use App\Models\University;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * daad:import fuzzyMatch: kurum türü kelimeleri silindiği için "Ulm University" hem Universität Ulm'a hem TH Ulm'a
 * %100 benzer. Eşit skorlu birden fazla aday varsa eşleşme yapılmamalı (2026-10-09: 114 program yanlış üniversitedeydi).
 */
class DaadImportUniversityMatchTest extends TestCase
{
    use RefreshDatabase;

    private function uni(string $name): int
    {
        return DB::table('universities')->insertGetId(['name_de' => $name, 'name_tr' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function match(string $academy): ?University
    {
        $cmd = app(DaadImport::class);
        $cmd->setOutput(new \Illuminate\Console\OutputStyle(new \Symfony\Component\Console\Input\ArrayInput([]), new \Symfony\Component\Console\Output\NullOutput()));

        return (new \ReflectionMethod($cmd, 'matchUniversity'))->invoke($cmd, $academy);
    }

    public function test_ambiguous_fuzzy_match_is_skipped(): void
    {
        $this->uni('Technische Hochschule Ulm');
        $this->uni('Universität Ulm');

        $this->assertNull($this->match('Ulm University'));
    }

    public function test_unique_fuzzy_match_and_exact_name_still_match(): void
    {
        $kiel = $this->uni('Christian-Albrechts-Universität zu Kiel');
        $this->uni('Technische Hochschule Ulm');

        $this->assertSame($kiel, $this->match('Christian-Albrechts-Universitat zu Kiel')?->id);
        $this->assertSame('Technische Hochschule Ulm', $this->match('Technische Hochschule Ulm')?->name_de);
    }
}
