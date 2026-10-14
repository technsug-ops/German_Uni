<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Sayfa 1 bağlantısını `?page=1` olmadan üretir.
 *
 * Laravel'in standart paginator'ı 1. sayfa için `...?page=1` verir. /programs bu adresi 301 ile
 * kanonik hale getirdiğinden ("page=1" varsayılan değer), 2. sayfadaki "önceki" ve "1" bağlantıları
 * gereksiz bir yönlendirmeden geçerdi. Bu sınıf yalnız url() davranışını değiştirir.
 */
class CanonicalPageOnePaginator extends LengthAwarePaginator
{
    public static function fromPaginator(LengthAwarePaginator $p): self
    {
        return new self($p->getCollection(), $p->total(), $p->perPage(), $p->currentPage(), [
            'path' => $p->path(),
            'pageName' => $p->getPageName(),
        ]);
    }

    public function url($page)
    {
        if ($page > 1) {
            return parent::url($page);
        }

        $query = $this->query;
        unset($query[$this->pageName]);

        return $this->path()
            . ($query !== [] ? (str_contains($this->path(), '?') ? '&' : '?') . \Illuminate\Support\Arr::query($query) : '')
            . $this->buildFragment();
    }
}
