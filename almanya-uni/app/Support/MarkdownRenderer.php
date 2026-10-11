<?php

namespace App\Support;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownRenderer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $env = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'heading_permalink' => [
                'symbol' => '#',
                'html_class' => 'heading-anchor',
                'min_heading_level' => 2,
                'max_heading_level' => 4,
            ],
            // Kaynak notları: [^n] → <sup><a class="footnote-ref" href="#fn-n">, tanımlar belgenin sonunda <div class="footnotes">.
            // Başlık (## Kaynak notları) içerikte kaldığı için ayırıcı <hr> eklenmez.
            'footnote' => [
                'container_add_hr' => false,
                'ref_id_prefix' => 'fnref-',
                'footnote_id_prefix' => 'fn-',
            ],
            // Tablolar mobilde yatay kaydırılabilsin diye sarmalayıcı (CSS: .blog-content .table-wrap).
            'table' => [
                'wrap' => ['enabled' => true, 'tag' => 'div', 'attributes' => ['class' => 'table-wrap']],
            ],
        ]);

        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new AutolinkExtension());
        $env->addExtension(new StrikethroughExtension());
        $env->addExtension(new TableExtension());
        $env->addExtension(new FootnoteExtension());
        $env->addExtension(new HeadingPermalinkExtension());

        $this->converter = new MarkdownConverter($env);
    }

    public function render(string $markdown): string
    {
        return (string) $this->converter->convert($markdown);
    }
}
