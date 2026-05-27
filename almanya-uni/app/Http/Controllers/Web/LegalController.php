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

        return view('legal.show', compact('page'));
    }

    public function privacy(): View    { return $this->show('privacy'); }
    public function terms(): View      { return $this->show('terms'); }
    public function cookies(): View    { return $this->show('cookies'); }
    public function impressum(): View  { return $this->show('impressum'); }
    public function disclaimer(): View { return $this->show('disclaimer'); }
}
