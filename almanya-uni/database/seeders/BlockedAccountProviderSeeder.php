<?php

namespace Database\Seeders;

use App\Models\BlockedAccountProvider;
use Illuminate\Database\Seeder;

class BlockedAccountProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'slug' => 'expatrio',
                'name' => 'Expatrio',
                'website_url' => 'https://www.expatrio.com/blocked-account',
                'type' => 'fintech',
                'backend_bank' => 'UniCredit (eski Aion Bank)',
                'setup_fee_eur' => 89.00,
                'monthly_fee_eur' => 5.00,
                'activation_days_min' => 2,
                'activation_days_max' => 5,
                'combo_insurance' => true,
                'insurance_provider_name' => 'TK / ottonova (Value Package)',
                'monthly_withdrawal_limit_eur' => 992,
                'required_yearly_deposit_eur' => 11904,
                'has_mobile_app' => true,
                'bafin_licensed' => true,
                'supported_languages' => ['tr', 'en', 'de', 'es', 'fr', 'pt', 'ru', 'zh', 'ar'],
                'description' => 'Almanya\'nın en popüler dijital Sperrkonto sağlayıcılarından. Vize için anlık onay + opsiyonel sağlık sigortası combo paketi.',
                'pros' => [
                    'Türkçe müşteri desteği',
                    'Hızlı aktivasyon (2-5 gün)',
                    'Sağlık sigortası combo (Value Package)',
                    'Mobil app + Apple Pay / Google Pay',
                ],
                'cons' => [
                    'Aylık servis ücreti var (€5)',
                    'Açılış ücreti diğerlerinden biraz yüksek',
                ],
                'is_published' => true,
                'is_featured' => true,
                'sort_order' => 10,
                'last_verified_at' => now(),
            ],
            [
                'slug' => 'fintiba',
                'name' => 'Fintiba',
                'website_url' => 'https://www.fintiba.com',
                'type' => 'fintech',
                'backend_bank' => 'Sutor Bank',
                'description' => 'Frankfurt merkezli, Almanya\'nın ilk dijital Sperrkonto sağlayıcılarından. Standard ve Plus plan seçenekleri.',
                'has_mobile_app' => true,
                'bafin_licensed' => true,
                'is_published' => false,
                'sort_order' => 20,
            ],
            [
                'slug' => 'coracle',
                'name' => 'Coracle',
                'website_url' => 'https://www.coracle.de',
                'type' => 'fintech',
                'description' => 'Berlin merkezli dijital Sperrkonto + sağlık sigortası sağlayıcısı.',
                'has_mobile_app' => true,
                'is_published' => false,
                'sort_order' => 30,
            ],
            [
                'slug' => 'deutsche-bank',
                'name' => 'Deutsche Bank',
                'website_url' => 'https://www.deutsche-bank.de/pk/kontomodelle/sperrkonto-fuer-auslaendische-studierende.html',
                'type' => 'traditional_bank',
                'description' => 'Almanya\'nın en büyük geleneksel bankası. Sperrkonto için şubede başvuru gerekir, süreç daha uzun.',
                'bafin_licensed' => true,
                'is_published' => false,
                'sort_order' => 40,
            ],
            [
                'slug' => 'sutor-bank',
                'name' => 'Sutor Bank',
                'website_url' => 'https://www.sutorbank.de',
                'type' => 'traditional_bank',
                'description' => 'Hamburg merkezli geleneksel banka. Fintiba\'nın arka uç bankası, doğrudan da Sperrkonto sunar.',
                'bafin_licensed' => true,
                'is_published' => false,
                'sort_order' => 50,
            ],
        ];

        foreach ($providers as $data) {
            BlockedAccountProvider::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
