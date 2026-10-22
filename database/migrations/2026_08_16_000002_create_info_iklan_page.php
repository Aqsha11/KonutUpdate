<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $content = '<h2>Info Iklan Konut Update</h2>
<p>Gunakan media kami yang kuat untuk mengirim umpan bisnis anda dengan benar. Kemudian melihat data secara tepat, bagaimana pengguna berinteraksi dengan iklan anda. Orang akan melihat lebih banyak produk berdasarkan minatnya.</p>
<hr />
<h3>Contact Person</h3>
<p>0896-5382-2093</p>
<h3>Rekening Perusahaan</h3>
<p><strong>Bank Mandiri</strong><br />PT. Sultra Media Nusantara</p>
<h3>Email</h3>
<p><a href="mailto:konutupdate@gmail.com">konutupdate@gmail.com</a></p>';

        Page::firstOrCreate(
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
