<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BackupScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin_tu');
    }

    public function rules(): array
    {
        return [
            'frequency'        => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
            'time'             => ['required', 'date_format:H:i'],
            'is_active'        => ['required', 'boolean'],
            'include_files'    => ['required', 'boolean'],
            'include_database' => ['required', 'boolean'],
            'auto_cleanup'     => ['required', 'boolean'],
            'retention_days'   => ['required', 'integer', 'min:7', 'max:365'],
        ];
    }

    public function messages(): array
    {
        return [
            'frequency.required'        => 'Frekuensi jadwal wajib dipilih.',
            'frequency.in'              => 'Frekuensi tidak valid.',
            'time.required'             => 'Waktu jadwal wajib diisi.',
            'time.date_format'          => 'Format waktu harus HH:mm.',
            'retention_days.min'        => 'Minimal retensi adalah 7 hari.',
            'retention_days.max'        => 'Maksimal retensi adalah 365 hari.',
        ];
    }
}
