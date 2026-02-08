<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telp' => 'required|string|max:20',
            'job' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'institute' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'submit_data' => 'required|string',
            'get_doc' => 'required|string',
            'send_doc' => 'required|string',
            'data_purpose' => 'required|string|max:500',
            'details_data' => 'nullable|string|max:1000',
            'surat_permohonan' => 'required|file|mimes:pdf|max:2048',
            'lampiran.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5048',
        ];
    }
}
