<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('tag');

        return [
            'name' => 'required|string|max:255|unique:tags,name,'.$id,
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Tag',
        ];
    }
}
