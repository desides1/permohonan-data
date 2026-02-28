<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import ConfirmDialog from "./ConfirmDialog.vue";
import DispositionConfirmAction from "./DispositionConfirmAction.vue";
import { useActionDialog } from "./useActionDialog";

const props = defineProps({
    ticket: Object,
    can: Object,
    suratPermohonan: Object,
    lampiranLainnya: Array,
    activities: Array,
    seksiList: {
        type: Array,
        default: () => [],
    },
});

const fileUrl = (path) => `/storage/${path}`;

const {
    confirmOpen,
    disposisiOpen,
    actionReason,
    isSubmitting,
    currentConfig,
    openDialog,
    closeAllDialogs,
    submitConfirm,
    submitDisposisi,
} = useActionDialog(() => props.ticket.ticket_progress.id);
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
                    <span>
                        Tiket: <strong>{{ ticket.ticket_code }}</strong>
                    </span>
                    <span>
                        Dibuat:
                        {{
                            new Date(ticket.created_at).toLocaleDateString(
                                "id-ID",
                                {
                                    day: "2-digit",
                                    month: "short",
                                    year: "numeric",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                },
                            )
                        }}
                    </span>

                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                        :style="{
                            backgroundColor:
                                (ticket.ticket_progress?.status_color ??
                                    '#94a3b8') + '18',
                            color:
                                ticket.ticket_progress?.status_color ??
                                '#64748b',
                            border:
                                '1px solid ' +
                                (ticket.ticket_progress?.status_color ??
                                    '#94a3b8') +
                                '30',
                        }"
                    >
                        {{ ticket.ticket_progress?.status_label }}
                    </span>

                    <span
                        v-if="ticket.ticket_progress?.current_assignment_label"
                        class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700"
                    >
                        📌
                        {{ ticket.ticket_progress.current_assignment_label }}
                    </span>
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
                <h2 class="mb-4 font-semibold text-gray-800">Lampiran</h2>

                <div v-if="suratPermohonan" class="rounded-lg border p-4">
                    <h3 class="mb-2 font-semibold">Surat Permohonan</h3>
                    <iframe
                        v-if="suratPermohonan.file_path.endsWith('.pdf')"
                        :src="fileUrl(suratPermohonan.file_path)"
                        class="w-full h-[600px] rounded"
                    ></iframe>
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
                            :href="route('admin.attachments.download', file.id)"
                            class="text-blue-600 hover:underline text-sm"
                        >
                            Unduh
                        </a>
                    </div>
                </div>
            </section>

            <hr class="my-6 border-gray-300" />

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

            <!-- Riwayat Aktivitas -->
            <section
                v-if="activities && activities.length > 0"
                class="rounded-xl border bg-white p-6"
            >
                <h2 class="mb-4 font-semibold text-gray-800">
                    Riwayat Aktivitas
                </h2>
                <div class="space-y-3">
                    <div
                        v-for="act in activities"
                        :key="act.id"
                        class="flex items-start gap-3 border-l-2 border-green-300 pl-4 py-2"
                    >
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">
                                {{ act.description }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ act.causer }} · {{ act.created_at }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-end gap-3">
                <button
                    v-if="can.verify"
                    @click="openDialog('verify')"
                    class="rounded-lg bg-green-500 px-6 py-2 text-white hover:bg-green-600 text-sm font-medium"
                >
                    ✅ Verifikasi
                </button>

                <button
                    v-if="can.approve"
                    @click="openDialog('approve')"
                    class="rounded-lg bg-green-500 px-6 py-2 text-white hover:bg-green-600 text-sm font-medium"
                >
                    ✅ Setujui
                </button>

                <button
                    v-if="can.assign"
                    @click="openDialog('assign')"
                    class="rounded-lg bg-blue-500 px-6 py-2 text-white hover:bg-blue-600 text-sm font-medium"
                >
                    📋 Disposisi ke Seksi
                </button>

                <button
                    v-if="can.markReady"
                    @click="openDialog('markReady')"
                    class="rounded-lg bg-green-500 px-6 py-2 text-white hover:bg-green-600 text-sm font-medium"
                >
                    📦 Tandai Siap
                </button>

                <button
                    v-if="can.reviewPpkh"
                    @click="openDialog('reviewPpkh')"
                    class="rounded-lg bg-blue-500 px-6 py-2 text-white hover:bg-blue-600 text-sm font-medium"
                >
                    🔍 Review Data
                </button>

                <button
                    v-if="can.forwardToBpkh"
                    @click="openDialog('forwardToBpkh')"
                    class="rounded-lg bg-green-500 px-6 py-2 text-white hover:bg-green-600 text-sm font-medium"
                >
                    ➡️ Teruskan ke BPKH
                </button>

                <button
                    v-if="can.requestRevision"
                    @click="openDialog('requestRevision')"
                    class="rounded-lg bg-orange-500 px-6 py-2 text-white hover:bg-orange-600 text-sm font-medium"
                >
                    🔄 Minta Revisi
                </button>

                <button
                    v-if="can.finalApprove"
                    @click="openDialog('finalApprove')"
                    class="rounded-lg bg-green-600 px-6 py-2 text-white hover:bg-green-700 text-sm font-medium"
                >
                    🏆 Persetujuan Final
                </button>

                <button
                    v-if="can.finalize"
                    @click="openDialog('finalize')"
                    class="rounded-lg bg-green-600 px-6 py-2 text-white hover:bg-green-700 text-sm font-medium"
                >
                    🎉 Selesaikan
                </button>

                <button
                    v-if="can.reject"
                    @click="openDialog('reject')"
                    class="rounded-lg border border-red-500 px-6 py-2 text-red-600 hover:bg-red-50 text-sm font-medium"
                >
                    ❌ Tolak
                </button>
            </div>
        </div>

        <!-- Dialog Konfirmasi Umum -->
        <Teleport to="body">
            <ConfirmDialog
                :open="confirmOpen"
                :title="currentConfig.title"
                :message="currentConfig.message"
                :color="currentConfig.color"
                :needs-reason="currentConfig.needsReason"
                v-model:reason-value="actionReason"
                :is-submitting="isSubmitting"
                @close="closeAllDialogs"
                @confirm="submitConfirm"
            />
        </Teleport>

        <!-- Dialog Disposisi Seksi -->
        <Teleport to="body">
            <DispositionConfirmAction
                :open="disposisiOpen"
                :seksi-list="seksiList"
                :is-submitting="isSubmitting"
                @close="closeAllDialogs"
                @confirm="submitDisposisi"
            />
        </Teleport>
    </LayoutDashboard>
</template>
