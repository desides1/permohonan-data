<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";

// defineProps({
//     ticket: {
//         type: Object,
//         required: true,
//     },
// });

const props = defineProps({
    ticket: Object,
    suratPermohonan: Object,
    lampiranLainnya: Array,
});

const fileUrl = (path) => {
    return `/storage/${path}`;
};
</script>
<template>
    <LayoutDashboard>
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-xl font-semibold text-gray-800">
                    Detail Data Permohonan
                </h1>
                <div class="mt-1 flex flex-wrap gap-4 text-sm text-gray-500">
                    <span
                        >Tiket: <strong>{{ ticket.ticket_code }}</strong></span
                    >

                    <span
                        >Dibuat:
                        {{
                            new Date(ticket.created_at).toLocaleDateString(
                                "us-US",
                                {
                                    day: "2-digit",
                                    month: "short",
                                    year: "numeric",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                },
                            )
                        }}</span
                    >
                    <span
                        class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700"
                    >
                        {{ ticket.ticket_progress.status_label }}
                    </span>
                    <!-- <pre>{{ ticket }}</pre> -->
                </div>
            </div>

            <!-- Informasi Pengguna -->
            <section class="rounded-xl border bg-white p-6">
                <h2 class="mb-4 font-semibold text-gray-800">
                    Informasi Pengguna
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div class="space-y-2">
                        <p class="text-gray-500">Diminta Oleh</p>
                        <p class="font-medium">{{ ticket.name }}</p>

                        <p class="text-gray-500">Email</p>
                        <p class="font-medium">{{ ticket.email }}</p>

                        <p class="text-gray-500">No. Telp</p>
                        <p class="font-medium">{{ ticket.telp }}</p>
                    </div>

                    <div class="space-y-2">
                        <p class="text-gray-500">Kode Pos</p>
                        <p class="font-medium">{{ ticket.postal_code }}</p>

                        <p class="text-gray-500">Instansi</p>
                        <p class="font-medium">{{ ticket.institute }}</p>

                        <p class="text-gray-500">Alamat</p>
                        <p class="font-medium">{{ ticket.address }}</p>
                    </div>

                    <div class="space-y-2">
                        <p class="text-gray-500">Tujuan Penggunaan Data</p>
                        <p class="font-medium">{{ ticket.data_purpose }}</p>

                        <p class="text-gray-500">Cara Pengambilan Data</p>
                        <p class="font-medium">{{ ticket.get_doc }}</p>

                        <p class="text-gray-500">Cara Penyerahan</p>
                        <p class="font-medium">{{ ticket.send_doc }}</p>
                    </div>
                </div>
            </section>

            <!-- Detail Data -->
            <section class="rounded-xl border bg-white p-6">
                <h2 class="mb-3 font-semibold text-gray-800">
                    Detail Data yang Dibutuhkan
                </h2>
                <p class="text-sm leading-relaxed text-gray-700">
                    {{ ticket.details_data }}
                </p>
            </section>

            <!-- Lampiran -->
            <section class="rounded-xl border bg-white p-6">
                <!-- <h2 class="mb-4 font-semibold text-gray-800">Lampiran</h2>

                <div class="space-y-3">
                    <div
                        v-for="file in ticket.attachments"
                        :key="file.id"
                        class="flex items-center justify-between rounded-lg border p-4 text-sm"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 flex items-center justify-center rounded-lg bg-gray-100"
                            >
                                📄
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ file.filename }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ file.size }}
                                </p>
                            </div>
                        </div>

                        <a
                            :href="file.url"
                            target="_blank"
                            class="text-green-700 hover:underline text-sm"
                        >
                            Unduh
                        </a>
                    </div>
                </div> -->

                <div v-if="suratPermohonan" class="rounded-lg border p-4">
                    <h3 class="mb-2 font-semibold">Surat Permohonan</h3>

                    <!-- Jika PDF -->
                    <iframe
                        v-if="suratPermohonan.file_path.endsWith('.pdf')"
                        :src="fileUrl(suratPermohonan.file_path)"
                        class="w-full h-[600px] rounded"
                    ></iframe>

                    <!-- Fallback -->
                    <a
                        v-else
                        :href="fileUrl(suratPermohonan.file_path)"
                        target="_blank"
                        class="text-blue-600 underline"
                    >
                        Lihat Dokumen
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div
                        v-for="file in lampiranLainnya"
                        :key="file.id"
                        class="rounded-lg border p-3"
                    >
                        <p class="text-sm font-medium truncate">
                            {{ file.file_name }}
                        </p>

                        <a
                            :href="fileUrl(file.file_path)"
                            target="_blank"
                            class="text-xs text-blue-600 underline mt-1 inline-block"
                        >
                            Unduh
                        </a>
                    </div>
                </div>
            </section>

            <!-- Catatan -->
            <section class="rounded-xl border bg-white p-6">
                <h2 class="mb-3 font-semibold text-gray-800">
                    Catatan Untuk Pemohon (Opsional)
                </h2>

                <textarea
                    rows="4"
                    placeholder="Tuliskan catatan"
                    class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600 text-sm"
                ></textarea>
            </section>

            <!-- Action -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <button
                    class="rounded-lg border border-red-500 px-6 py-2 text-red-600 hover:bg-red-50 text-sm"
                >
                    Tolak
                </button>
                <button
                    class="rounded-lg bg-yellow-500 px-6 py-2 text-white hover:bg-yellow-600 text-sm"
                >
                    Terima
                </button>
            </div>
        </div>
    </LayoutDashboard>
</template>
