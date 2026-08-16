<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContributorPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'type' => 'required|in:article',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_ids' => 'nullable|array|max:3',
            'category_ids.*' => 'exists:categories,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'tags' => 'nullable|string',
            'action' => 'required|in:draft,submit',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul',
            'excerpt' => 'Ringkasan',
            'body' => 'Isi',
            'type' => 'Jenis Konten',
            'thumbnail' => 'Thumbnail',
            'category_ids' => 'Kategori',
            'kecamatan_id' => 'Kecamatan',
            'tags' => 'Tags',
        ];
    }
}
