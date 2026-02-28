<?php

namespace Database\Seeders;

use App\Enums\DeliveryMethod;
use App\Enums\DocumentAccessType;
use App\Enums\SubmitMethod;
use App\Enums\TicketAssignment;
use App\Enums\TicketStatus;
use App\Models\TicketDetail;
use Illuminate\Database\Seeder;

class DataPermohonanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-001',
                'name'         => 'Ahmad Fauzi',
                'email'        => 'ahmad.fauzi@kemenag.go.id',
                'telp'         => '081234567890',
                'job'          => 'Auditor Internal',
                'postal_code'  => '10110',
                'institute'    => 'Kementerian Agama RI',
                'address'      => 'Jl. Lapangan Banteng Barat No.3-4, Jakarta Pusat',
                'data_purpose' => 'Keperluan audit internal pengelolaan dana haji tahun 2023',
                'details_data' => 'Data realisasi penerimaan dan pengeluaran dana haji tahun 2023, termasuk rincian investasi dan imbal hasil.',
                'get_doc'      => DocumentAccessType::COPY,
                'submit_data'  => SubmitMethod::DIRECT,
                'send_doc'     => DeliveryMethod::EMAIL,
                'status'       => TicketStatus::SENT,
                'assignment'   => TicketAssignment::ADMIN_TU,
            ],
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-002',
                'name'         => 'Siti Nurhaliza',
                'email'        => 'siti.nurhaliza@bpk.go.id',
                'telp'         => '082345678901',
                'job'          => 'Auditor Utama',
                'postal_code'  => '10110',
                'institute'    => 'BPK RI',
                'address'      => 'Jl. Gatot Subroto No.31, Jakarta Selatan',
                'data_purpose' => 'Pemeriksaan semester atas pengelolaan investasi BPKH',
                'details_data' => 'Laporan portofolio investasi BPKH periode Januari–Juni 2024, termasuk return on investment dan risk assessment.',
                'get_doc'      => DocumentAccessType::COPY,
                'submit_data'  => SubmitMethod::DIRECT,
                'send_doc'     => DeliveryMethod::LANGSUNG,
                'status'       => TicketStatus::VERIFIED,
                'assignment'   => TicketAssignment::PIMPINAN_BPKH,
            ],
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-003',
                'name'         => 'Budi Santoso',
                'email'        => 'budi.santoso@dpr.go.id',
                'telp'         => '083456789012',
                'job'          => 'Analis Legislatif',
                'postal_code'  => '10270',
                'institute'    => 'DPR RI - Komisi VIII',
                'address'      => 'Jl. Jend. Gatot Subroto No.16, Jakarta Selatan',
                'data_purpose' => 'Bahan rapat dengar pendapat Komisi VIII dengan BPKH',
                'details_data' => 'Data jumlah jamaah haji reguler dan khusus tahun 2020-2024, serta biaya penyelenggaraan ibadah haji (BPIH) per tahun.',
                'get_doc'      => DocumentAccessType::COPY,
                'submit_data'  => SubmitMethod::INDIRECT,
                'send_doc'     => DeliveryMethod::EMAIL,
                'status'       => TicketStatus::APPROVED,
                'assignment'   => TicketAssignment::PIMPINAN_PPKH,
            ],
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-004',
                'name'         => 'Dewi Kartika',
                'email'        => 'dewi.kartika@ui.ac.id',
                'telp'         => '084567890123',
                'job'          => 'Mahasiswa S1',
                'postal_code'  => '16424',
                'institute'    => 'Universitas Indonesia - Fakultas Ekonomi',
                'address'      => 'Kampus UI Depok, Jawa Barat',
                'data_purpose' => 'Penelitian skripsi tentang optimalisasi pengelolaan dana haji',
                'details_data' => 'Data statistik setoran awal BPIH, jumlah waiting list jamaah haji, dan data penempatan investasi dana haji 5 tahun terakhir.',
                'get_doc'      => DocumentAccessType::VIEW_ONLY,
                'submit_data'  => SubmitMethod::DIRECT,
                'send_doc'     => DeliveryMethod::EMAIL,
                'status'       => TicketStatus::SENT,
                'assignment'   => TicketAssignment::ADMIN_TU,
            ],
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-005',
                'name'         => 'Rizky Ramadhan',
                'email'        => 'rizky.ramadhan@ojk.go.id',
                'telp'         => '085678901234',
                'job'          => 'Analis Pengawas',
                'postal_code'  => '12710',
                'institute'    => 'Otoritas Jasa Keuangan (OJK)',
                'address'      => 'Gedung Soemitro Djojohadikusumo, Jl. Lapangan Banteng Timur 2-4, Jakarta',
                'data_purpose' => 'Koordinasi pengawasan penempatan dana haji di instrumen keuangan',
                'details_data' => 'Laporan keuangan tahunan BPKH 2023, rincian penempatan dana di sukuk, deposito, dan instrumen pasar modal lainnya.',
                'get_doc'      => DocumentAccessType::COPY,
                'submit_data'  => SubmitMethod::DIRECT,
                'send_doc'     => DeliveryMethod::COURIER,
                'status'       => TicketStatus::SENT,
                'assignment'   => TicketAssignment::ADMIN_TU,
            ],
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-006',
                'name'         => 'Fajar Hidayat',
                'email'        => 'fajar.hidayat@kemenkeu.go.id',
                'telp'         => '086789012345',
                'job'          => 'Analis Fiskal',
                'postal_code'  => '10710',
                'institute'    => 'Kementerian Keuangan RI',
                'address'      => 'Jl. Dr. Wahidin Raya No.1, Jakarta Pusat',
                'data_purpose' => 'Penyusunan kajian fiskal terkait pengelolaan dana haji',
                'details_data' => 'Data nilai aset kelolaan BPKH, proyeksi pertumbuhan dana haji, dan data perbandingan yield investasi per instrumen.',
                'get_doc'      => DocumentAccessType::COPY,
                'submit_data'  => SubmitMethod::INDIRECT,
                'send_doc'     => DeliveryMethod::EMAIL,
                'status'       => TicketStatus::SENT,
                'assignment'   => TicketAssignment::ADMIN_TU,
            ],
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-007',
                'name'         => 'Lina Marlina',
                'email'        => 'lina.marlina@ugm.ac.id',
                'telp'         => '087890123456',
                'job'          => 'Mahasiswa S2',
                'postal_code'  => '55281',
                'institute'    => 'Universitas Gadjah Mada',
                'address'      => 'Bulaksumur, Yogyakarta',
                'data_purpose' => 'Penelitian tesis tentang tata kelola BPKH',
                'details_data' => 'Struktur organisasi BPKH, mekanisme pengambilan keputusan investasi, dan laporan tahunan 3 tahun terakhir.',
                'get_doc'      => DocumentAccessType::VIEW_ONLY,
                'submit_data'  => SubmitMethod::DIRECT,
                'send_doc'     => DeliveryMethod::LANGSUNG,
                'status'       => TicketStatus::SENT,
                'assignment'   => TicketAssignment::ADMIN_TU,
            ],
            [
                'ticket_code'  => 'DT-' . now()->format('Ymd') . '-008',
                'name'         => 'Hendra Wijaya',
                'email'        => 'hendra.wijaya@bappenas.go.id',
                'telp'         => '088901234567',
                'job'          => 'Perencana Madya',
                'postal_code'  => '10310',
                'institute'    => 'Bappenas',
                'address'      => 'Jl. Taman Suropati No.2, Jakarta Pusat',
                'data_purpose' => 'Perencanaan pembangunan nasional sektor keagamaan',
                'details_data' => 'Data jumlah pendaftar haji per provinsi 5 tahun terakhir dan estimasi biaya tunggu per jamaah.',
                'get_doc'      => DocumentAccessType::COPY,
                'submit_data'  => SubmitMethod::INDIRECT,
                'send_doc'     => DeliveryMethod::POST,
                'status'       => TicketStatus::SENT,
                'assignment'   => TicketAssignment::ADMIN_TU,
            ],
        ];

        foreach ($tickets as $data) {
            $status     = $data['status'];
            $assignment = $data['assignment'];

            unset($data['status'], $data['assignment']);

            // 1. Buat ticket detail
            $ticketDetail = TicketDetail::firstOrCreate(
                ['ticket_code' => $data['ticket_code']],
                $data,
            );

            // 2. Buat progress & assignment hanya jika baru
            if ($ticketDetail->wasRecentlyCreated) {
                $progress = $ticketDetail->ticketProgress()->create([
                    'status'             => $status,
                    'current_assignment' => $assignment,
                    'is_read'            => false,
                    'notes'               => $this->noteForStatus($status),
                ]);

                $progress->assignments()->create([
                    'assignment'  => $assignment,
                    'assigned_by' => null,
                    'notes'       => $this->noteForStatus($status),
                ]);
            }
        }

        $this->command->info('✅ ' . count($tickets) . ' data permohonan dummy berhasil dibuat.');
    }

    /**
     * Catatan otomatis berdasarkan status awal tiket.
     */
    private function noteForStatus(TicketStatus $status): string
    {
        return match ($status) {
            TicketStatus::SENT     => 'Permohonan baru diterima, menunggu verifikasi Admin TU.',
            TicketStatus::VERIFIED => 'Telah diverifikasi Admin TU, menunggu persetujuan Pimpinan BPKH.',
            TicketStatus::APPROVED => 'Telah disetujui Pimpinan BPKH, menunggu disposisi Pimpinan PPKH.',
            default                => 'Permohonan dalam proses.',
        };
    }
}
