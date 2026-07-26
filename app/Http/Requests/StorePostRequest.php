<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'type' => 'required|in:article,video',
            'thumbnail' => 'required_if:type,video|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video_file' => 'nullable|file|mimes:mp4,webm,mov,avi|max:51200',
            'video_url' => 'nullable|url|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'category_ids' => 'nullable|array|max:3',
            'category_ids.*' => 'exists:categories,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'tags' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'is_breaking' => 'boolean',
            'is_headline' => 'boolean',
            'published_at' => 'nullable|date',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul',
            'excerpt' => 'Ringkasan',
            'body' => 'Isi Berita',
            'type' => 'Jenis Berita',
            'thumbnail' => 'Thumbnail',
            'video_file' => 'File Video',
            'video_url' => 'URL Video',
            'category_id' => 'Kategori',
            'tags' => 'Tags',
            'status' => 'Status',
            'is_breaking' => 'Breaking News',
            'is_headline' => 'Headline',
        ];
    }
}
