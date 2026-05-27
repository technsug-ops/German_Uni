<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function privacy(): View
    {
        return view('legal.privacy', [
            'updated_at'  => '2026-05-16',
            'contact_email' => 'technsug@gmail.com',
        ]);
    }

    public function terms(): View
    {
        return view('legal.terms', [
            'updated_at'  => '2026-05-16',
            'contact_email' => 'technsug@gmail.com',
        ]);
    }

    public function cookies(): View
    {
        return view('legal.cookies', [
            'updated_at'  => '2026-05-16',
            'contact_email' => 'technsug@gmail.com',
        ]);
    }

    /**
     * Impressum (Germany legal requirement § 5 TMG).
     * Same operator entity (TechNS UG) for both brands; only contact email + website
     * vary per brand domain.
     */
    public function impressum(): View
    {
        $brandKey = brand_key();
        $brandEmails = [
            'almanyauni' => 'info@almanyauni.com',
            'applytogerman' => 'info@applytogerman.com',
        ];

        return view('legal.impressum', [
            'updated_at' => '2026-05-27',
            'company' => 'TechNS UG (haftungsbeschränkt)',
            'street' => 'Ludwig-Erhard-Str. 16A',
            'postal_city' => '61440 Oberursel (Taunus)',
            'country' => 'Deutschland',
            'manager' => 'Halil Yaprakli',
            'phone' => '+49 6171 277 51 37',
            'email' => $brandEmails[$brandKey] ?? 'info@almanyauni.com',
            'website' => 'https://' . (config('brand.brands.' . $brandKey . '.domain') ?? 'almanyauni.com'),
            'register_court' => 'Amtsgericht Bad Homburg',
            'register_number' => 'HRB 16236',
            'vat_id' => 'DE312006599',
            'tax_number' => '003 246 12171',
            'responsible_content' => 'Halil Yaprakli', // §18 Abs. 2 MStV
        ]);
    }
}
