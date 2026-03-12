<script setup>
import { ref, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    ticket: Object,
    uploadedDocuments: {
        type: Array,
        default: () => [],
    },
    can: Object,
    userRole: String,
});

const showRevisionForm = ref(false);
const isSubmitting = ref(false);

const revisionForm = useForm({
    reason: "",
});

const reviewTitle = computed(() => {
    if (props.userRole === "pimpinan_ppkh") {
        return "Review Hasil Data — Pimpinan PPKH";
    }
    if (props.userRole === "pimpinan_bpkh") {
        return "Review Hasil Data — Pimpinan BPKH";
    }
    return "Review Hasil Data";
});

const reviewDescription = computed(() => {
    const status = props.ticket.ticket_progress?.status_value;

    if (props.userRole === "pimpinan_ppkh") {
        if (status === "ready") {
            return "Data hasil permohonan telah disiapkan oleh Seksi dan menunggu review Anda. Silakan unduh dan periksa setiap dokumen, kemudian ambil keputusan.";
        }
        if (status === "under_review_ppkh") {
            return "Anda sedang mereview data ini. Jika data sudah sesuai, teruskan ke Pimpinan BPKH untuk persetujuan final. Jika belum sesuai, minta revisi ke Seksi.";
        }
    }
    if (props.userRole === "pimpinan_bpkh") {
        if (status === "under_review_bpkh") {
            return "Data telah direview dan diteruskan oleh Pimpinan PPKH. Silakan periksa kelengkapan data, lalu berikan persetujuan final atau minta revisi jika diperlukan.";
        }
    }
    return "Silakan periksa dokumen hasil permohonan di bawah ini.";
});

const statusInfo = computed(() => {
    const status = props.ticket.ticket_progress?.status_value;

    const map = {
        ready: {
            label: "Menunggu Review PPKH",
            color: "bg-amber-50 text-amber-800 border-amber-200",
            dot: "bg-amber-500",
            icon: "⏳",
        },
        under_review_ppkh: {
            label: "Sedang Direview PPKH",
            color: "bg-blue-50 text-blue-800 border-blue-200",
            dot: "bg-blue-500",
            icon: "🔍",
        },
        under_review_bpkh: {
            label: "Sedang Direview BPKH",
            color: "bg-violet-50 text-violet-800 border-violet-200",
            dot: "bg-violet-500",
            icon: "🔎",
        },
        revision: {
            label: "Perlu Revisi",
            color: "bg-red-50 text-red-800 border-red-200",
            dot: "bg-red-500",
            icon: "🔄",
        },
    };

    return map[status] ?? null;
});

const headerGradient = computed(() => {
    const status = props.ticket.ticket_progress?.status_value;
    const map = {
        ready: "from-amber-600 to-amber-700",
        under_review_ppkh: "from-blue-600 to-blue-700",
        under_review_bpkh: "from-violet-600 to-violet-700",
        revision: "from-red-600 to-red-700",
    };
    return map[status] ?? "from-blue-600 to-blue-700";
});

function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return "0 B";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

function getFileTypeLabel(mimeType) {
    if (!mimeType) return "FILE";
    if (mimeType.includes("pdf")) return "PDF";
    if (mimeType.includes("word") || mimeType.includes("document"))
        return "DOC";
    if (mimeType.includes("sheet") || mimeType.includes("excel")) return "XLS";
    if (mimeType.includes("image")) return "IMG";
    if (mimeType.includes("zip") || mimeType.includes("rar")) return "ZIP";
    return "FILE";
}

function getFileTypeColor(mimeType) {
    if (!mimeType) return "bg-gray-100 text-gray-600";
    if (mimeType.includes("pdf")) return "bg-red-100 text-red-700";
    if (mimeType.includes("word") || mimeType.includes("document"))
        return "bg-blue-100 text-blue-700";
    if (mimeType.includes("sheet") || mimeType.includes("excel"))
        return "bg-green-100 text-green-700";
    if (mimeType.includes("image")) return "bg-purple-100 text-purple-700";
    return "bg-gray-100 text-gray-600";
}

function handleReviewPpkh() {
    if (!confirm("Mulai proses review data permohonan ini?")) return;

    isSubmitting.value = true;
    router.post(
        route("admin.tickets.reviewPpkh", props.ticket.ticket_progress.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => (isSubmitting.value = false),
        },
    );
}

function handleForwardToBpkh() {
    if (
        !confirm(
            "Apakah Anda yakin data sudah sesuai dan siap diteruskan ke Pimpinan BPKH untuk persetujuan final?",
        )
    )
        return;

    isSubmitting.value = true;
    router.post(
        route("admin.tickets.forwardToBpkh", props.ticket.ticket_progress.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => (isSubmitting.value = false),
        },
    );
}

function handleFinalApprove() {
    if (
        !confirm(
            "Apakah Anda yakin memberikan persetujuan final untuk data permohonan ini? Data akan diteruskan ke Admin TU untuk penyelesaian.",
        )
    )
        return;

    isSubmitting.value = true;
    router.post(
        route("admin.tickets.finalApprove", props.ticket.ticket_progress.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => (isSubmitting.value = false),
        },
    );
}

function handleRequestRevision() {
    if (revisionForm.reason.length < 10) return;

    revisionForm.post(
        route("admin.tickets.requestRevision", props.ticket.ticket_progress.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                showRevisionForm.value = false;
                revisionForm.reset();
            },
        },
    );
}

function toggleRevisionForm() {
    showRevisionForm.value = !showRevisionForm.value;
    if (!showRevisionForm.value) {
        revisionForm.reset();
        revisionForm.clearErrors();
    }
}
</script>

<template>
    <section
        class="rounded-xl border-2 border-blue-200 bg-white overflow-hidden shadow-sm"
    >
        <!-- Header -->
        <div
            class="bg-gradient-to-r px-4 sm:px-6 py-4 sm:py-5"
            :class="headerGradient"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3"
            >
                <div class="flex items-start gap-3">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/20 flex-shrink-0"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                            />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-white">
                            {{ reviewTitle }}
                        </h2>
                        <p
                            class="text-xs sm:text-sm text-white/80 mt-0.5 max-w-xl"
                        >
                            {{ reviewDescription }}
                        </p>
                    </div>
                </div>

                <!-- Status Badge -->
                <span
                    v-if="statusInfo"
                    class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-medium border self-start sm:self-auto"
                    :class="statusInfo.color"
                >
                    <span
                        class="w-2 h-2 rounded-full animate-pulse"
                        :class="statusInfo.dot"
                    ></span>
                    {{ statusInfo.label }}
                </span>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-5">
            <!-- Daftar Dokumen -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">
                        📄 Dokumen yang Perlu Diperiksa
                    </h3>
                    <span
                        class="text-xs text-gray-500 bg-gray-100 rounded-full px-2.5 py-1"
                    >
                        {{ uploadedDocuments.length }} dokumen
                    </span>
                </div>

                <!-- Empty state -->
                <div
                    v-if="uploadedDocuments.length === 0"
                    class="text-center py-8 sm:py-10 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-12 h-12 mx-auto text-gray-300 mb-3"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    <p class="text-sm text-gray-500 font-medium">
                        Belum ada dokumen untuk direview
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        Menunggu Seksi mengupload dokumen hasil permohonan
                    </p>
                </div>

                <!-- Dokumen list -->
                <div v-else class="space-y-2">
                    <div
                        v-for="doc in uploadedDocuments"
                        :key="doc.id"
                        class="flex flex-col sm:flex-row sm:items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 sm:p-4 hover:bg-white hover:border-blue-200 hover:shadow-sm transition-all"
                    >
                        <!-- File type badge -->
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-lg text-xs font-bold flex-shrink-0"
                            :class="getFileTypeColor(doc.mime_type)"
                        >
                            {{ getFileTypeLabel(doc.mime_type) }}
                        </div>

                        <!-- File info -->
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium text-gray-800 truncate"
                            >
                                {{ doc.original_name }}
                            </p>
                            <div
                                class="flex flex-wrap gap-2 text-xs text-gray-500 mt-0.5"
                            >
                                <span>{{ formatFileSize(doc.file_size) }}</span>
                                <span>•</span>
                                <span>{{ doc.uploaded_by }}</span>
                                <span>•</span>
                                <span>{{ doc.uploaded_at }}</span>
                            </div>
                            <p
                                v-if="doc.keterangan"
                                class="text-xs text-gray-500 italic mt-1"
                            >
                                "{{ doc.keterangan }}"
                            </p>
                        </div>

                        <!-- Download button -->
                        <a
                            :href="route('admin.documents.download', doc.id)"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 transition-colors whitespace-nowrap self-end sm:self-auto"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                />
                            </svg>
                            Unduh & Periksa
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Revisi -->
            <div
                v-if="showRevisionForm"
                class="rounded-lg border-2 border-orange-200 bg-orange-50 p-4 sm:p-5"
            >
                <div class="flex items-center gap-2 mb-3">
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
                    <h4 class="text-sm font-semibold text-orange-800">
                        Formulir Permintaan Revisi
                    </h4>
                </div>
                <p class="text-xs text-orange-700 mb-3">
                    Jelaskan secara detail apa yang perlu diperbaiki agar Seksi
                    dapat melakukan revisi dengan tepat. Data akan dikembalikan
                    ke Seksi untuk diperbaiki.
                </p>
                <textarea
                    v-model="revisionForm.reason"
                    placeholder="Contoh: Data realisasi penerimaan di halaman 3 tidak sesuai dengan laporan bulanan. Harap perbaiki angka pada kolom 'Jumlah Pengeluaran' dan sertakan sumber data yang valid..."
                    class="w-full rounded-lg border border-orange-300 bg-white px-4 py-3 text-sm focus:border-orange-400 focus:ring-orange-400 resize-none placeholder:text-orange-300"
                    rows="4"
                ></textarea>
                <p
                    v-if="revisionForm.errors.reason"
                    class="text-xs text-red-600 mt-1"
                >
                    {{ revisionForm.errors.reason }}
                </p>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-500">
                        Minimal 10 karakter ({{
                            revisionForm.reason.length
                        }}/10)
                    </p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-2 mt-4">
                    <button
                        @click="toggleRevisionForm"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors w-full sm:w-auto"
                    >
                        Batal
                    </button>
                    <button
                        @click="handleRequestRevision"
                        :disabled="
                            revisionForm.processing ||
                            revisionForm.reason.length < 10
                        "
                        class="flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors w-full sm:w-auto"
                    >
                        <svg
                            v-if="revisionForm.processing"
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
                            revisionForm.processing
                                ? "Mengirim..."
                                : "🔄 Kirim Permintaan Revisi"
                        }}
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div
                v-if="uploadedDocuments.length > 0 && !showRevisionForm"
                class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3 pt-5 border-t border-gray-200"
            >
                <!-- Pimpinan PPKH: Mulai Review (READY → UNDER_REVIEW_PPKH) -->
                <button
                    v-if="can.reviewPpkh"
                    @click="handleReviewPpkh"
                    :disabled="isSubmitting"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 transition-colors w-full sm:w-auto"
                >
                    <svg
                        v-if="isSubmitting"
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
                    <svg
                        v-else
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
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                    🔍 Mulai Review Data
                </button>

                <!-- Pimpinan PPKH: Teruskan ke BPKH (UNDER_REVIEW_PPKH → UNDER_REVIEW_BPKH) -->
                <button
                    v-if="can.forwardToBpkh"
                    @click="handleForwardToBpkh"
                    :disabled="isSubmitting"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-50 transition-colors w-full sm:w-auto"
                >
                    <svg
                        v-if="isSubmitting"
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
                    <svg
                        v-else
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
                            d="M13 7l5 5m0 0l-5 5m5-5H6"
                        />
                    </svg>
                    ✅ Data Sesuai — Teruskan ke BPKH
                </button>

                <!-- Pimpinan BPKH: Final Approve (UNDER_REVIEW_BPKH → FINAL_APPROVED) -->
                <button
                    v-if="can.finalApprove"
                    @click="handleFinalApprove"
                    :disabled="isSubmitting"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-800 disabled:opacity-50 transition-colors w-full sm:w-auto"
                >
                    <svg
                        v-if="isSubmitting"
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
                    🏆 Persetujuan Final
                </button>

                <!-- Minta Revisi (PPKH & BPKH) -->
                <button
                    v-if="can.requestRevision"
                    @click="toggleRevisionForm"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-orange-300 bg-orange-50 px-5 py-2.5 text-sm font-medium text-orange-700 hover:bg-orange-100 hover:border-orange-400 transition-colors w-full sm:w-auto"
                >
                    <svg
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
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                    </svg>
                    🔄 Minta Revisi ke Seksi
                </button>
            </div>

            <!-- Info menunggu -->
            <div
                v-if="uploadedDocuments.length === 0"
                class="pt-4 border-t border-gray-200"
            >
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg
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
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <span>
                        Menunggu Seksi mengupload dokumen hasil permohonan
                        sebelum review dapat dilakukan.
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>
