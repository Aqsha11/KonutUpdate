<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $content = '<h2>Info Iklan Kendariinfo</h2>
<p>Gunakan media kami yang kuat untuk mengirim umpan bisnis anda dengan benar. Kemudian melihat data secara tepat, bagaimana pengguna berinteraksi dengan iklan anda. Orang akan melihat lebih banyak produk berdasarkan minatnya.</p>
<hr />
<h3>Contact Person</h3>
<p>082224444240</p>
<h3>Rekening Perusahaan</h3>
<p><strong>BCA. 7911082521</strong><br />PT Percaya Karya Pemuda</p>
<p><strong>Bank Sultra. 101 01.04.100829-8</strong><br />PT Percaya Karya Pemuda</p>
<h3>Email</h3>
<p><a href="mailto:Kendariinfo2017@gmail.com">Kendariinfo2017@gmail.com</a></p>';

        Page::updateOrCreate(
            ['slug' => 'info-iklan'],
            [
                'title' => 'Info Iklan',
                'content' => $content,
                'is_published' => true,
            ]
        );
    }

    public function down(): void
    {
        Page::where('slug', 'info-iklan')->delete();
    }
};
