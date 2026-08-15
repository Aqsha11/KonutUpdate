<?php

namespace App\Http\Requests;

use App\Services\HtmlSanitizer;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreOpiniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\p{M}\s\'\-\.\,]+$/u'],
            'email' => ['bail', 'required', 'email:rfc,spoof', 'max:191'],
            'title' => ['bail', 'required', 'string', 'min:10', 'max:191', 'not_regex:/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['bail', 'required', 'string', 'max:20000'],
            'category_ids' => ['nullable', 'array', 'max:3'],
            'category_ids.*' => ['distinct', 'exists:categories,id'],
            'kecamatan_id' => ['nullable', 'exists:kecamatans,id'],
            'tags' => ['nullable', 'string', 'max:255', 'regex:/^[\p{L}\p{M}\p{N}\s\'\-\.\,\#\&\+]+$/u'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'min:1024', 'max:5120', 'dimensions:ratio=16/9'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal :min karakter.',
            'name.max' => 'Nama maksimal :max karakter.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan tanda baca dasar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal :max karakter.',
            'title.required' => 'Judul opini wajib diisi.',
            'title.min' => 'Judul opini minimal :min karakter.',
            'title.max' => 'Judul opini maksimal :max karakter.',
            'title.not_regex' => 'Judul mengandung karakter yang tidak diperbolehkan.',
            'excerpt.max' => 'Ringkasan maksimal :max karakter.',
            'body.required' => 'Isi opini wajib diisi.',
            'body.max' => 'Isi opini maksimal :max karakter.',
            'category_ids.max' => 'Maksimal :max kategori yang dipilih.',
            'category_ids.*.distinct' => 'Kategori tidak boleh duplikat.',
            'category_ids.*.exists' => 'Kategori yang dipilih tidak valid.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak valid.',
            'tags.max' => 'Tags maksimal :max karakter.',
            'tags.regex' => 'Tags hanya boleh huruf, angka, spasi, koma, dan tanda dasar.',
            'thumbnail.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Thumbnail harus berformat JPG, PNG, GIF, atau WebP.',
            'thumbnail.min' => 'Ukuran thumbnail minimal 1MB.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 5MB.',
            'thumbnail.dimensions' => 'Thumbnail harus berorientasi landscape 16:9.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'title' => 'Judul Opini',
            'excerpt' => 'Ringkasan',
            'body' => 'Isi Opini',
            'category_ids' => 'Kategori',
            'kecamatan_id' => 'Kecamatan',
            'tags' => 'Tags',
            'thumbnail' => 'Thumbnail',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $body = $this->input('body');
                if (! is_string($body)) {
                    return;
                }

                $clean = app(HtmlSanitizer::class)->sanitize($body);
                $textLength = mb_strlen(trim(strip_tags($clean)));

                if ($textLength < 50) {
                    $validator->errors()->add('body', 'Isi opini minimal 50 karakter setelah kode dihapus.');
                }
            },
        ];
    }
}
