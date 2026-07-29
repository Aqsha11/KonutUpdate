<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,ico,webp|max:1024',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }

    public function attributes(): array
    {
        return [
            'whatsapp' => 'WhatsApp',
            'site_name' => 'Nama Website',
            'tagline' => 'Tagline',
            'email' => 'Email',
            'phone' => 'Telepon',
            'address' => 'Alamat',
            'logo' => 'Logo',
            'favicon' => 'Favicon',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'primary_color' => 'Warna Utama',
            'accent_color' => 'Warna Aksen',
        ];
    }
}
