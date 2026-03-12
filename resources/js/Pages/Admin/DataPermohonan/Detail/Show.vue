<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import ConfirmDialog from "./ConfirmDialog.vue";
import DispositionConfirmAction from "./DispositionConfirmAction.vue";
import DialogUploadData from "./DialogUploadData.vue";
import ReviewDocumentSection from "./ReviewDocumentSection.vue";
import { useActionDialog } from "./useActionDialog";
import { router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    ticket: Object,
    can: Object,
    suratPermohonan: Object,
    lampiranLainnya: Array,
    uploadedDocuments: {
        type: Array,
        default: () => [],
    },
    activities: Array,
    seksiList: {
        type: Array,
        default: () => [],
    },
    userRole: String,
});

const fileUrl = (path) => `/storage/${path}`;
const flash = computed(() => usePage().props.flash ?? {});

const isRequestingReview = ref(false);

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

function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

function onUploaded() {
    router.reload({ only: ["uploadedDocuments", "activities"] });
}

function deleteDocument(docId) {
    if (!confirm("Apakah Anda yakin ingin menghapus dokumen ini?")) return;

    router.delete(route("admin.documents.delete", docId), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ["uploadedDocuments", "activities"] });
        },
    });
}

const latestRevisionNote = computed(() => {
    if (!props.activities || props.activities.length === 0) return null;

    const status = props.ticket.ticket_progress?.status_value;
    if (status !== "revision") return null;

    return props.activities.find(
        (act) => act.action === "request_revision" && act.reason,
    );
});

function requestReview() {
    if (isRequestingReview.value) return;
    if (
        !confirm(
            "Apakah Anda yakin data sudah lengkap dan siap direview oleh Pimpinan?",
        )
    )
        return;

    isRequestingReview.value = true;
    router.post(
        route("admin.tickets.markReady", props.ticket.ticket_progress.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => (isRequestingReview.value = false),
        },
    );
}

const showReviewSection = computed(() => {
    const status = props.ticket.ticket_progress?.status_value;
    return (
        [
            "ready",
            "under_review_ppkh",
            "under_review_bpkh",
            "revision",
        ].includes(status) &&
        (props.can.reviewPpkh ||
            props.can.forwardToBpkh ||
            props.can.requestRevision ||
            props.can.finalApprove)
    );
});

const isReviewPhase = computed(() => {
    const status = props.ticket.ticket_progress?.status_value;
    return [
        "ready",
        "under_review_ppkh",
        "under_review_bpkh",
        "revision",
    ].includes(status);
});

const isSeksiViewingReview = computed(() => {
    const status = props.ticket.ticket_progress?.status_value;
    return (
        props.userRole === "seksi" &&
        ["ready", "under_review_ppkh", "under_review_bpkh"].includes(status)
    );
});
</script>

<template>
    <LayoutDashboard>
        <div class="max-w-6xl mx-auto space-y-6 px-4 sm:px-6 lg:px-0">
            <!-- Flash Messages -->
            <div
                v-if="flash.success"
                class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800"
            >
                ✅ {{ flash.success }}
            </div>
            <div
                v-if="flash.error"
                class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"
            >
                ❌ {{ flash.error }}
            </div>

            <!-- Header -->
            <div>
                <h1 class="text-lg sm:text-xl font-semibold text-gray-800">
                    Detail Data Permohonan
                </h1>
                <div
                    class="mt-1 flex flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm text-gray-500"
                >
                    <span>
                        Tiket:
                        <strong class="text-gray-800">{{
                            ticket.ticket_code
                        }}</strong>
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

            <section
                v-if="
                    ticket.ticket_progress?.status_value === 'revision' &&
                    userRole === 'seksi'
                "
                class="rounded-xl border-2 border-orange-300 bg-orange-50 p-4 sm:p-6"
            >
                <div class="flex items-start gap-3">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 flex-shrink-0"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 text-orange-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
                            />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="text-sm sm:text-base font-bold text-orange-800"
                        >
                            ⚠️ Perlu Revisi dari Pimpinan
                        </h3>
                        <p class="text-xs text-orange-600 mt-0.5">
                            Pimpinan meminta Anda untuk merevisi dokumen yang
                            telah diupload. Silakan perbaiki sesuai catatan di
                            bawah, lalu upload ulang dan klik "Minta Review
                            Pimpinan" kembali.
                        </p>

                        <!-- Alasan Revisi -->
                        <div
                            v-if="latestRevisionNote"
                            class="mt-3 rounded-lg border border-orange-200 bg-white p-4"
                        >
                            <p
                                class="text-xs font-semibold text-orange-700 uppercase tracking-wide mb-1"
                            >
                                Catatan Revisi dari
                                {{ latestRevisionNote.causer }}
                            </p>
                            <p
                                class="text-sm text-gray-800 leading-relaxed whitespace-pre-line"
                            >
                                {{ latestRevisionNote.reason }}
                            </p>
                            <p class="text-xs text-gray-400 mt-2">
                                {{ latestRevisionNote.created_at }}
                            </p>
                        </div>

                        <!-- Langkah yang harus dilakukan -->
                        <div
                            class="mt-3 flex flex-col sm:flex-row gap-2 text-xs text-orange-700"
                        >
                            <span
                                class="inline-flex items-center gap-1 bg-orange-100 rounded-full px-3 py-1"
                            >
                                1️⃣ Hapus dokumen lama jika perlu
                            </span>
                            <span
                                class="inline-flex items-center gap-1 bg-orange-100 rounded-full px-3 py-1"
                            >
                                2️⃣ Upload dokumen yang sudah diperbaiki
                            </span>
                            <span
                                class="inline-flex items-center gap-1 bg-orange-100 rounded-full px-3 py-1"
                            >
                                3️⃣ Klik "Minta Review Pimpinan"
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Informasi Pengguna -->
            <section class="rounded-xl border bg-white p-6 sm:p-6">
                <h2 class="mb-4 font-semibold text-gray-800">
                    Informasi Pengguna
                </h2>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm"
                >
                    <div class="space-y-3">
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Diminta Oleh
                        </p>
                        <p class="font-medium mt-0.5">{{ ticket.name }}</p>
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Email
                        </p>
                        <p class="font-medium mt-0.5">{{ ticket.email }}</p>
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            No. Telp
                        </p>
                        <p class="font-medium mt-0.5">{{ ticket.telp }}</p>
                    </div>
                    <div class="space-y-2">
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Kode Pos
                        </p>
                        <p class="font-medium mt-0.5">
                            {{ ticket.postal_code }}
                        </p>
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Instansi
                        </p>
                        <p class="font-medium mt-0.5">{{ ticket.institute }}</p>
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Alamat
                        </p>
                        <p class="font-medium mt-0.5">{{ ticket.address }}</p>
                    </div>
                    <div class="space-y-2">
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Tujuan Penggunaan Data
                        </p>
                        <p class="font-medium mt-0.5">
                            {{ ticket.data_purpose }}
                        </p>
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Cara Pengambilan Data
                        </p>
                        <p class="font-medium mt-0.5">{{ ticket.get_doc }}</p>
                        <p
                            class="text-gray-500 text-xs uppercase tracking-wide"
                        >
                            Cara Penyerahan
                        </p>
                        <p class="font-medium mt-0.5">{{ ticket.send_doc }}</p>
                    </div>
                </div>
            </section>

            <!-- Detail Data -->
            <section class="rounded-xl border bg-white p-4 sm:p-6">
                <h2 class="mb-3 font-semibold text-gray-800">
                    Detail Data yang Dibutuhkan
                </h2>
                <p class="text-sm leading-relaxed text-gray-700">
                    {{ ticket.details_data }}
                </p>
            </section>

            <!-- Lampiran -->
            <section class="rounded-xl border bg-white p-4 sm:p-6">
                <h2 class="mb-4 font-semibold text-gray-800">Lampiran</h2>

                <div v-if="suratPermohonan" class="rounded-lg border p-4">
                    <h3 class="mb-2 font-semibold text-sm">Surat Permohonan</h3>
                    <iframe
                        v-if="suratPermohonan.file_path.endsWith('.pdf')"
                        :src="fileUrl(suratPermohonan.file_path)"
                        class="w-full h-[400px] sm:h-[600px] rounded"
                    ></iframe>
                    <a
                        v-else
                        :href="fileUrl(suratPermohonan.file_path)"
                        target="_blank"
                        class="text-blue-600 underline text-sm"
                    >
                        Lihat Dokumen
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div
                        v-for="file in lampiranLainnya"
                        :key="file.id"
                        class="rounded-lg border p-3 flex items-center justify-between hover:bg-gray-50 transition-colors"
                    >
                        <p class="text-sm font-medium truncate flex-1 mr-2">
                            {{ file.file_name }}
                        </p>
                        <a
                            :href="route('admin.attachments.download', file.id)"
                            class="text-blue-600 hover:underline text-sm whitespace-nowrap"
                        >
                            Unduh
                        </a>
                    </div>
                </div>
            </section>

            <!-- Dokumen Hasil Permohonan (Upload dari Seksi) -->
            <section class="rounded-xl border bg-white p-4 sm:p-6">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4"
                >
                    <h2 class="font-semibold text-gray-800">
                        📁 Dokumen Hasil Permohonan
                    </h2>
                    <div class="flex flex-wrap items-center gap-2">
                        <DialogUploadData
                            v-if="can.upload"
                            :ticket-code="ticket.ticket_code"
                            :ticket-name="ticket.name"
                            @uploaded="onUploaded"
                        />
                        <!-- Tombol Minta Review (menggantikan Tandai Siap) -->
                        <button
                            v-if="can.requestReview"
                            @click="requestReview"
                            :disabled="isRequestingReview"
                            class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700 disabled:opacity-50 transition-colors"
                        >
                            <svg
                                v-if="!isRequestingReview"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <svg
                                v-else
                                class="w-4 h-4 animate-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                />
                            </svg>
                            {{
                                isRequestingReview
                                    ? "Mengirim..."
                                    : "📤 Minta Review Pimpinan"
                            }}
                        </button>
                    </div>
                </div>

                <div
                    v-if="uploadedDocuments.length === 0"
                    class="text-center py-8 text-gray-400"
                >
                    <p class="text-sm">
                        Belum ada dokumen hasil yang diupload.
                    </p>
                    <p v-if="can.upload" class="text-xs mt-1 text-gray-400">
                        Upload file terlebih dahulu sebelum mengajukan review.
                    </p>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="doc in uploadedDocuments"
                        :key="doc.id"
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-lg border p-3 sm:p-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">
                                {{ doc.original_name }}
                            </p>
                            <div
                                class="flex flex-wrap gap-2 sm:gap-3 text-xs text-gray-500 mt-1"
                            >
                                <span>{{ formatFileSize(doc.file_size) }}</span>
                                <span>Oleh: {{ doc.uploaded_by }}</span>
                                <span>{{ doc.uploaded_at }}</span>
                            </div>
                            <p
                                v-if="doc.keterangan"
                                class="text-xs text-gray-600 mt-1 italic"
                            >
                                {{ doc.keterangan }}
                            </p>
                        </div>
                        <div
                            class="flex items-center gap-2 self-end sm:self-auto"
                        >
                            <a
                                :href="
                                    route('admin.documents.download', doc.id)
                                "
                                class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-100 transition-colors whitespace-nowrap"
                            >
                                ⬇️ Unduh
                            </a>
                            <button
                                v-if="can.deleteDocument"
                                @click="deleteDocument(doc.id)"
                                class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors whitespace-nowrap"
                            >
                                🗑️ Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Seksi: Status menunggu review -->
            <section
                v-if="isSeksiViewingReview"
                class="rounded-xl border-2 border-amber-200 bg-amber-50 p-4 sm:p-6"
            >
                <div class="flex items-start gap-3">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 flex-shrink-0"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 text-amber-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-amber-800">
                            Menunggu Review Pimpinan
                        </h3>
                        <p class="text-xs text-amber-700 mt-1">
                            Data Anda telah diajukan untuk direview. Tunggu
                            konfirmasi dari Pimpinan PPKH. Jika ada revisi, Anda
                            akan mendapat notifikasi dan bisa mengupload ulang.
                        </p>
                    </div>
                </div>
            </section>
            <div
                class="rounded-lg border-2 border-red-400 bg-red-50 p-4 text-xs font-mono"
            >
                <p><strong>DEBUG INFO (hapus setelah fix)</strong></p>
                <p>userRole: {{ userRole }}</p>
                <p>status_value: {{ ticket.ticket_progress?.status_value }}</p>
                <p>can.reviewPpkh: {{ can.reviewPpkh }}</p>
                <p>can.forwardToBpkh: {{ can.forwardToBpkh }}</p>
                <p>can.requestRevision: {{ can.requestRevision }}</p>
                <p>can.finalApprove: {{ can.finalApprove }}</p>
                <p>can.finalize: {{ can.finalize }}</p>
                <p>showReviewSection: {{ showReviewSection }}</p>
                <p>isReviewPhase: {{ isReviewPhase }}</p>
            </div>
            <ReviewDocumentSection
                v-if="showReviewSection"
                :ticket="ticket"
                :uploaded-documents="uploadedDocuments"
                :can="can"
                :user-role="userRole"
            />

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
                class="rounded-xl border bg-white p-4 sm:p-6"
            >
                <h2 class="mb-4 font-semibold text-gray-800">
                    Riwayat Aktivitas
                </h2>
                <div class="space-y-3">
                    <div
                        v-for="act in activities"
                        :key="act.id"
                        class="flex items-start gap-3 border-l-2 border-green-300 pl-4 py-2"
                        :class="
                            act.action === 'request_revision'
                                ? 'border-orange-400'
                                : 'border-green-300'
                        "
                    >
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">
                                {{ act.description }}
                            </p>
                            <div
                                v-if="act.reason"
                                class="mt-1.5 rounded-md bg-orange-50 border border-orange-200 px-3 py-2"
                            >
                                <p
                                    class="text-xs font-semibold text-orange-700 mb-0.5"
                                >
                                    Catatan Revisi:
                                </p>
                                <p
                                    class="text-xs text-gray-700 whitespace-pre-line"
                                >
                                    {{ act.reason }}
                                </p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ act.causer }} · {{ act.created_at }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Action Buttons -->
            <div
                v-if="!isReviewPhase"
                class="flex flex-wrap justify-end gap-2 sm:gap-3"
            >
                <button
                    v-if="can.verify"
                    @click="openDialog('verify')"
                    class="rounded-lg bg-green-600 px-4 sm:px-6 py-2 text-white hover:bg-green-700 text-sm font-medium transition-colors"
                >
                    ✅ Verifikasi
                </button>

                <button
                    v-if="can.approve"
                    @click="openDialog('approve')"
                    class="rounded-lg bg-green-600 px-6 py-2 text-white hover:bg-green-700 text-sm font-medium transition-colors"
                >
                    ✅ Setujui
                </button>

                <button
                    v-if="can.assign"
                    @click="openDialog('assign')"
                    class="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700 text-sm font-medium transition-colors"
                >
                    📋 Disposisi ke Seksi
                </button>

                <button
                    v-if="can.markReady"
                    @click="openDialog('markReady')"
                    class="rounded-lg bg-cyan-600 px-4 sm:px-6 py-2 text-white hover:bg-cyan-700 text-sm font-medium transition-colors"
                >
                    Selesaikan dan publish
                </button>

                <button
                    v-if="can.finalize"
                    @click="openDialog('finalize')"
                    class="rounded-lg bg-green-700 px-4 sm:px-6 py-2 text-white hover:bg-green-800 text-sm font-medium transition-colors"
                >
                    🎉 Selesaikan & Publish
                </button>

                <button
                    v-if="can.reject"
                    @click="openDialog('reject')"
                    class="rounded-lg border border-red-500 px-4 sm:px-6 py-2 text-red-600 hover:bg-red-50 text-sm font-medium transition-colors"
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
