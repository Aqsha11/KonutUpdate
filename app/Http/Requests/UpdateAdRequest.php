<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url|max:500',
            'position' => 'required|in:sidebar_top,sidebar_bottom,in_article',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul Iklan',
            'image' => 'Gambar Iklan',
            'link' => 'Tautan',
            'position' => 'Posisi',
            'is_active' => 'Aktif',
            'starts_at' => 'Tanggal Mulai',
            'ends_at' => 'Tanggal Berakhir',
            'sort_order' => 'Urutan',
        ];
    }
}
