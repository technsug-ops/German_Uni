<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Basit görev/checklist takibi — doküman hâlindeki oyun kitaplarını (önce
 * BACKLINK-PLAYBOOK) panelden işaretlenebilir işlere çevirir.
 *
 * Tasarım notu: tek tablo, tek kullanıcı. Atama (assignee), yorum, alt görev
 * KASITLI olarak yok — istenen şey "chsk list", proje yönetimi değil. Gerekirse
 * sonradan eklenir; şimdi eklemek her ekranı ağırlaştırırdı.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('playbook', 40)->default('backlink'); // hangi oyun kitabı / kampanya
            $table->string('group', 80)->nullable();             // bölüm başlığı (tabloda gruplama)
            $table->string('title');
            $table->text('details')->nullable();                 // ne yapılacak, taktik
            $table->string('target_url', 500)->nullable();       // hedef site / kaynak
            $table->string('status', 20)->default('todo');       // todo | doing | done | skipped
            $table->string('priority', 10)->default('normal');   // high | normal | low
            $table->date('due_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();                   // takip notu (kiminle konuşuldu vb.)
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['playbook', 'status']);
            $table->index(['group', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
