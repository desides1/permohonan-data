<?php

return [

    'required' => ':attribute wajib diisi.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'numeric' => ':attribute harus berupa angka.',
    'min' => [
        'string' => ':attribute minimal :min karakter.',
    ],
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
        'file' => ':attribute maksimal berukuran 2 MB untuk surat permohonan dan 5 MB untuk lampiran.',
    ],
    'file' => ':attribute harus diisikan dengan file.',
    'mimes' => ':attribute harus berformat pdf.',
    'array' => ':attribute harus berupa array.',

    'attributes' => [
        'name' => 'Nama Pemohon',
        'email' => 'Email',
        'telp' => 'Nomor Telepon',
        'address' => 'Alamat',
        'surat_permohonan' => 'Surat Permohonan',
        'lampiran' => 'Lampiran',
        'ticket_code' => 'Kode Tiket',
        'job' => 'Pekerjaan',
        'postal_code' => 'Kode Pos',
        'institute' => 'Instansi',
        'submit_data' => 'Cara Pengajuan Data',
        'get_doc' => 'Cara Mendapatkan Dokumen',
        'send_doc' => 'Cara Pengiriman Dokumen',
        'data_purpose' => 'Tujuan Penggunaan Data',
        'details_data' => 'Rincian Data yang Dimohon',

    ],
];
