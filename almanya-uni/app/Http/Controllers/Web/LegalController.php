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
}
