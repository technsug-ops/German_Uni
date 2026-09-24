<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function show(string $key): View
    {
        $page = LegalPage::findByKey($key);

        abort_if(! $page, 404);

        // İstenen dilde çeviri yoksa 404. Bilinçli olarak BAŞKA DİLE DÜŞMÜYORUZ:
        // /en/privacy isteyene Türkçe gizlilik metni göstermek, kişinin okuduğunu
        // sandığı şeyle fiilen bağlandığı şeyi ayırmak demek. Uygulamanın genel
        // fallback_locale'i (tr) burada bilerek devre dışı; arayüz metinleri
        // (__() ile gelen kısa stringler) elbette etkilenmiyor.
        abort_if(! $page->hasContentFor(), 404);

        return view('legal.show', compact('page'));
    }

    public function privacy(): View    { return $this->show('privacy'); }
    public function terms(): View      { return $this->show('terms'); }
    public function cookies(): View    { return $this->show('cookies'); }
    public function impressum(): View  { return $this->show('impressum'); }
    public function disclaimer(): View { return $this->show('disclaimer'); }
}
