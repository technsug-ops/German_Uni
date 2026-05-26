<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\University;
use App\Services\PartnerApiClient;
use App\Services\PartnerImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Partner kuruluş webhook receiver.
 *
 * Beklenen event tipleri:
 *   - program.created / program.updated / program.deleted
 *   - university.created / university.updated / university.deleted
 *
 * Payload örneği:
 * {
 *   "event": "program.updated",
 *   "id": "019ddbba-026f-71c5-9b61-e7c39cf479c0",
 *   "timestamp": "2026-05-15T10:23:45Z",
 *   "data": { ...tam kayıt veya null... }
 * }
 *
 * Signature doğrulama: HMAC-SHA256(body, secret), header'da `X-Partner-Signature`.
 */
class PartnerWebhookController extends Controller
{
    public function handle(Request $request, PartnerApiClient $api, PartnerImporter $importer): JsonResponse
    {
        $secret = config('services.partner.webhook_secret');
        if (! $secret) {
            return response()->json(['error' => 'webhook_disabled'], 503);
        }

        $rawBody  = $request->getContent();
        $provided = $request->header('X-Partner-Signature');
        $expected = hash_hmac('sha256', $rawBody, $secret);

        if (! $provided || ! hash_equals($expected, $provided)) {
            Log::warning('Partner webhook bad signature', [
                'ip'         => $request->ip(),
                'event'      => $request->input('event'),
                'has_header' => (bool) $provided,
            ]);
            return response()->json(['error' => 'invalid_signature'], 401);
        }

        $event = $request->input('event');
        $id    = $request->input('id');
        $data  = $request->input('data');

        if (! $event || ! $id) {
            return response()->json(['error' => 'missing_event_or_id'], 422);
        }

        Log::info('Partner webhook received', ['event' => $event, 'id' => $id]);

        try {
            match (true) {
                str_starts_with($event, 'program.')    => $this->handleProgram($event, $id, $data, $api, $importer),
                str_starts_with($event, 'university.') => $this->handleUniversity($event, $id, $data),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Partner webhook error', [
                'event' => $event,
                'id'    => $id,
                'err'   => $e->getMessage(),
            ]);
            return response()->json(['error' => 'processing_failed', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['ok' => true]);
    }

    private function handleProgram(string $event, string $id, ?array $data, PartnerApiClient $api, PartnerImporter $importer): void
    {
        if ($event === 'program.deleted') {
            Program::where('partner_id', $id)->update(['is_active' => false]);
            return;
        }

        if (! $data) {
            $data = $api->fetchProgram($id);
            if (! $data) return;
        }

        $uniId = University::where('partner_id', $data['university_id'])->value('id');
        if (! $uniId) {
            Log::warning('Webhook: program için üni yok', ['program_id' => $id, 'uni_partner_id' => $data['university_id']]);
            return;
        }

        $stats = ['imported' => 0, 'updated' => 0, 'skipped_no_uni' => 0, 'errors' => 0];
        Program::withoutSyncingToSearch(fn () => $importer->upsertProgramFromApi($data, $uniId, $stats));
    }

    private function handleUniversity(string $event, string $id, ?array $data): void
    {
        if ($event === 'university.deleted') {
            University::where('partner_id', $id)->update(['is_active' => false]);
            return;
        }

        $existing = University::where('partner_id', $id)->first();
        if ($existing && $data) {
            $attrs = array_filter([
                'name_de'        => $data['name'] ?? null,
                'last_synced_at' => now(),
            ], fn ($v) => $v !== null && $v !== '');
            $existing->fill($attrs)->saveQuietly();
        }
    }
}
