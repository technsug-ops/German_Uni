<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ApiClientCreate extends Command
{
    protected $signature = 'apiclient:create
        {name : Firma adı}
        {email : Kontak e-posta}
        {--plan=free : free|partner|enterprise}
        {--limit= : Dakikalık özel rate limit (boşsa plan varsayılanı)}
        {--website= : Firma web sitesi}
        {--contact= : Kontak kişi adı}';

    protected $description = 'Yeni API client kaydı + Sanctum token üretir';

    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $plan = $this->option('plan');

        if (!in_array($plan, ['free', 'partner', 'enterprise'])) {
            $this->error("Geçersiz plan: $plan. (free|partner|enterprise)");
            return self::FAILURE;
        }

        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;
        while (ApiClient::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $client = ApiClient::create([
            'name' => $name,
            'slug' => $slug,
            'contact_email' => $email,
            'contact_name' => $this->option('contact'),
            'website' => $this->option('website'),
            'plan' => $plan,
            'rate_limit_per_minute' => $this->option('limit') ?: ApiClient::PLAN_LIMITS[$plan],
            'is_active' => true,
        ]);

        $token = $client->createToken('default', $client->defaultAbilities());

        $this->newLine();
        $this->info("✅ API Client oluşturuldu:");
        $this->line("  ID:      {$client->id}");
        $this->line("  Slug:    {$client->slug}");
        $this->line("  Plan:    {$client->plan} ({$client->rate_limit_per_minute} req/dk)");
        $this->newLine();
        $this->warn("🔑 Token (BUNU KAYBETME — bir daha gösterilmez):");
        $this->line("  " . $token->plainTextToken);
        $this->newLine();
        $this->line('Kullanım örneği:');
        $this->line('  curl -H "Authorization: Bearer ' . $token->plainTextToken . '" \\');
        $this->line('       ' . config('app.url') . '/api/v1/universities');

        return self::SUCCESS;
    }
}
