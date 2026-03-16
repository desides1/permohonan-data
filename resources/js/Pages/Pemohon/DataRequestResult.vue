<script setup>
import { ref, computed, onMounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    tickets: {
        type: Array,
        default: () => [],
    },
    ticket: {
        type: Object,
        default: null,
    },
    user: {
        type: Object,
        default: () => ({}),
    },
});

const selectedTicket = ref(null);
const searchQuery = ref("");
const filterStatus = ref("all");

// Status sesuai TicketStatus enum
const statusConfig = {
    sent: {
        label: "Dikirim",
        color: "bg-blue-100 text-blue-800 border-blue-200",
        icon: "📋",
        step: 1,
    },
    verified: {
        label: "Diverifikasi",
        color: "bg-cyan-100 text-cyan-800 border-cyan-200",
        icon: "🔍",
        step: 2,
    },
    approved: {
        label: "Disetujui",
        color: "bg-indigo-100 text-indigo-800 border-indigo-200",
        icon: "✔️",
        step: 3,
    },
    assigned: {
        label: "Didisposisikan",
        color: "bg-purple-100 text-purple-800 border-purple-200",
        icon: "📌",
        step: 4,
    },
    ready: {
        label: "Data Siap",
        color: "bg-teal-100 text-teal-800 border-teal-200",
        icon: "📦",
        step: 5,
    },
    under_review_ppkh: {
        label: "Review PPKH",
        color: "bg-orange-100 text-orange-800 border-orange-200",
        icon: "🔎",
        step: 6,
    },
    under_review_bpkh: {
        label: "Review BPKH",
        color: "bg-amber-100 text-amber-800 border-amber-200",
        icon: "🔎",
        step: 7,
    },
    final_approved: {
        label: "Disetujui Final",
        color: "bg-lime-100 text-lime-800 border-lime-200",
        icon: "✅",
        step: 8,
    },
    completed: {
        label: "Selesai",
        color: "bg-green-100 text-green-800 border-green-200",
        icon: "🎉",
        step: 9,
    },
    revision: {
        label: "Perlu Revisi",
        color: "bg-red-100 text-red-800 border-red-200",
        icon: "⚠️",
        step: -1,
    },
    rejected: {
        label: "Ditolak",
        color: "bg-red-200 text-red-900 border-red-300",
        icon: "❌",
        step: -2,
    },
};

const docStatusConfig = {
    uploaded: { label: "Diunggah", color: "bg-gray-100 text-gray-700" },
    verified: { label: "Terverifikasi", color: "bg-green-100 text-green-700" },
    rejected: { label: "Ditolak", color: "bg-red-100 text-red-700" },
    pending: { label: "Menunggu", color: "bg-yellow-100 text-yellow-700" },
};

// Stepper yang disederhanakan untuk pemohon (agar mudah dipahami)
const progressSteps = [
    { step: 1, label: "Dikirim", description: "Permohonan terkirim" },
    { step: 2, label: "Verifikasi", description: "Sedang diverifikasi" },
    { step: 3, label: "Diproses", description: "Sedang diproses petugas" },
    { step: 4, label: "Review", description: "Sedang direview pimpinan" },
    { step: 5, label: "Selesai", description: "Hasil tersedia" },
];

// Mapping status detail ke step sederhana
function getSimplifiedStep(status) {
    const mapping = {
        sent: 1,
        verified: 2,
        approved: 3,
        assigned: 3,
        ready: 3,
        under_review_ppkh: 4,
        under_review_bpkh: 4,
        final_approved: 4,
        completed: 5,
        revision: 3,
        rejected: -1,
    };
    return mapping[status] || 1;
}

const filteredTickets = computed(() => {
    let result = props.tickets;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (t) =>
                t.ticket_number?.toLowerCase().includes(query) ||
                t.applicant_name?.toLowerCase().includes(query) ||
                t.request_type?.toLowerCase().includes(query)
        );
    }

    if (filterStatus.value !== "all") {
        result = result.filter((t) => t.status === filterStatus.value);
    }

    return result;
});

const revisionReplies = computed(() => {
    if (!selectedTicket.value) return [];
    return (
        selectedTicket.value.replies?.filter(
            (r) => r.role !== "pemohon"
        ) || []
    );
});

function selectTicket(ticket) {
    selectedTicket.value = ticket;
}

function goBack() {
    selectedTicket.value = null;
}

function getStatusConfig(status) {
    return statusConfig[status] || statusConfig["sent"];
}

function getDocStatusConfig(status) {
    return docStatusConfig[status] || docStatusConfig["pending"];
}

function isStepCompleted(stepNumber, currentStatus) {
    const current = getSimplifiedStep(currentStatus);
    if (currentStatus === "rejected") return stepNumber <= 2;
    if (currentStatus === "revision") return stepNumber <= 2;
    return stepNumber <= current;
}

function isStepActive(stepNumber, currentStatus) {
    const current = getSimplifiedStep(currentStatus);
    if (currentStatus === "rejected") return stepNumber === 2;
    if (currentStatus === "revision") return stepNumber === 3;
    return stepNumber === current;
}

function isStepError(stepNumber, currentStatus) {
    if (currentStatus === "rejected" && stepNumber === 2) return true;
    if (currentStatus === "revision" && stepNumber === 3) return true;
    return false;
}

function downloadDocument(docId) {
    window.location.href = `/pemohon/hasil-permohonan/download-dokumen/${docId}`;
}

function downloadResult(ticketId) {
    window.location.href = `/pemohon/hasil-permohonan/download-hasil/${ticketId}`;
}

function viewDocument(doc) {
    window.open(`/storage/${doc.file_path}`, "_blank");
}

onMounted(() => {
    if (props.ticket) {
        selectedTicket.value = props.ticket;
    }
});
</script>

<template>
    <Head title="Hasil Data Permohonan" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <!-- Header -->
        <header
            class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200"
                        >
                            <svg
                                class="w-5 h-5 sm:w-6 sm:h-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h1
                                class="text-lg sm:text-xl font-bold text-slate-800"
                            >
                                Hasil Data Permohonan
                            </h1>
                            <p
                                class="text-xs sm:text-sm text-slate-500 hidden sm:block"
                            >
                                Pantau status dan unduh hasil permohonan Anda
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div
                            class="hidden md:flex items-center gap-2 bg-slate-100 rounded-full px-4 py-2"
                        >
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center"
                            >
                                <span class="text-white text-sm font-semibold">
                                    {{ user.name?.charAt(0)?.toUpperCase() }}
                                </span>
                            </div>
                            <span class="text-sm font-medium text-slate-700">
                                {{ user.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- ═══════ DAFTAR PERMOHONAN ═══════ -->
            <div v-if="!selectedTicket">
                <!-- Search & Filter -->
                <div class="mb-6 sm:mb-8">
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6"
                    >
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                            <div class="flex-1 relative">
                                <svg
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari nomor tiket, nama, atau tujuan..."
                                    class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                />
                            </div>
                            <select
                                v-model="filterStatus"
                                class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[160px]"
                            >
                                <option value="all">Semua Status</option>
                                <option value="sent">Dikirim</option>
                                <option value="verified">Diverifikasi</option>
                                <option value="approved">Disetujui</option>
                                <option value="assigned">Didisposisikan</option>
                                <option value="ready">Data Siap</option>
                                <option value="under_review_ppkh">Review PPKH</option>
                                <option value="under_review_bpkh">Review BPKH</option>
                                <option value="final_approved">Disetujui Final</option>
                                <option value="completed">Selesai</option>
                                <option value="revision">Perlu Revisi</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div
                    class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8"
                >
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-800">{{ tickets.length }}</p>
                                <p class="text-xs text-slate-500">Total</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-800">
                                    {{ tickets.filter((t) => !["completed", "rejected"].includes(t.status)).length }}
                                </p>
                                <p class="text-xs text-slate-500">Proses</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-800">
                                    {{ tickets.filter((t) => t.status === "completed").length }}
                                </p>
                                <p class="text-xs text-slate-500">Selesai</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-800">
                                    {{ tickets.filter((t) => t.status === "revision").length }}
                                </p>
                                <p class="text-xs text-slate-500">Revisi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Cards -->
                <div class="space-y-4">
                    <div
                        v-for="ticket in filteredTickets"
                        :key="ticket.id"
                        @click="selectTicket(ticket)"
                        class="bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-md hover:border-blue-200 transition-all duration-300 cursor-pointer group"
                    >
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 sm:gap-3 mb-2">
                                        <span class="text-sm sm:text-base font-bold text-slate-800 truncate font-mono">
                                            {{ ticket.ticket_number }}
                                        </span>
                                        <span :class="[ getStatusConfig(ticket.status).color, 'px-2.5 py-1 rounded-full text-xs font-semibold border whitespace-nowrap']">
                                            {{ getStatusConfig(ticket.status).icon }}
                                            {{ getStatusConfig(ticket.status).label }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ ticket.applicant_name }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ ticket.created_at }}
                                        </span>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>

                            <!-- Mini Progress Bar -->
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-1">
                                    <template v-for="step in progressSteps" :key="step.step">
                                        <div
                                            :class="[
                                                'h-1.5 flex-1 rounded-full transition-all',
                                                isStepError(step.step, ticket.status)
                                                    ? 'bg-red-400'
                                                    : isStepCompleted(step.step, ticket.status)
                                                        ? 'bg-blue-500'
                                                        : 'bg-slate-200',
                                            ]"
                                        ></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="filteredTickets.length === 0"
                        class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-16 text-center"
                    >
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-1">Tidak Ada Permohonan</h3>
                        <p class="text-sm text-slate-500">
                            {{ searchQuery || filterStatus !== "all" ? "Tidak ditemukan permohonan yang sesuai filter." : "Anda belum memiliki permohonan data." }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ═══════ DETAIL PERMOHONAN ═══════ -->
            <div v-else>
                <button
                    @click="goBack"
                    class="mb-6 flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600 transition-colors group"
                >
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Daftar
                </button>

                <!-- Alert Revisi -->
                <div
                    v-if="selectedTicket.status === 'revision'"
                    class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-400 rounded-r-2xl p-4 sm:p-5"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">⚠️</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-amber-800">Permohonan Memerlukan Revisi</h3>
                            <p class="text-sm text-amber-700 mt-1">
                                Petugas memberikan catatan revisi. Periksa catatan di bawah.
                            </p>
                            <p v-if="selectedTicket.notes" class="text-sm text-amber-800 font-medium mt-2 bg-amber-100 rounded-lg px-3 py-2">
                                "{{ selectedTicket.notes }}"
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert Ditolak -->
                <div
                    v-if="selectedTicket.status === 'rejected'"
                    class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 rounded-r-2xl p-4 sm:p-5"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">❌</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-red-800">Permohonan Ditolak</h3>
                            <p class="text-sm text-red-700 mt-1">Silakan periksa catatan petugas untuk informasi lebih lanjut.</p>
                            <p v-if="selectedTicket.notes" class="text-sm text-red-800 font-medium mt-2 bg-red-100 rounded-lg px-3 py-2">
                                "{{ selectedTicket.notes }}"
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert Selesai -->
                <div
                    v-if="selectedTicket.status === 'completed'"
                    class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-r-2xl p-4 sm:p-5"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">🎉</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-green-800">Permohonan Selesai!</h3>
                            <p class="text-sm text-green-700 mt-1">Silakan unduh hasil data di bagian bawah.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- ─── Main Content ─── -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Info Permohonan -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-5 sm:px-6 py-4">
                                <h2 class="text-white font-semibold text-base sm:text-lg flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Informasi Permohonan
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                    <div class="space-y-1">
                                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Kode Tiket</p>
                                        <p class="text-sm font-bold text-slate-800 font-mono bg-slate-50 px-3 py-2 rounded-lg">
                                            {{ selectedTicket.ticket_number }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Nama Pemohon</p>
                                        <p class="text-sm font-semibold text-slate-800 bg-slate-50 px-3 py-2 rounded-lg">
                                            {{ selectedTicket.applicant_name }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tujuan Data</p>
                                        <p class="text-sm font-semibold text-slate-800 bg-slate-50 px-3 py-2 rounded-lg">
                                            {{ selectedTicket.request_type }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tanggal Pengajuan</p>
                                        <p class="text-sm font-semibold text-slate-800 bg-slate-50 px-3 py-2 rounded-lg">
                                            {{ selectedTicket.created_at }}
                                        </p>
                                    </div>
                                    <div class="space-y-1 sm:col-span-2">
                                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Status</p>
                                        <div class="flex items-center gap-2">
                                            <span :class="[getStatusConfig(selectedTicket.status).color, 'px-3 py-1.5 rounded-full text-sm font-semibold border']">
                                                {{ getStatusConfig(selectedTicket.status).icon }}
                                                {{ selectedTicket.status_label || getStatusConfig(selectedTicket.status).label }}
                                            </span>
                                            <span class="text-xs text-slate-400">{{ selectedTicket.updated_at }}</span>
                                        </div>
                                    </div>
                                    <div v-if="selectedTicket.description && selectedTicket.description !== '-'" class="space-y-1 sm:col-span-2">
                                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Detail Data</p>
                                        <p class="text-sm text-slate-700 bg-slate-50 px-3 py-2 rounded-lg leading-relaxed">
                                            {{ selectedTicket.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Stepper -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    Progress Permohonan
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6">
                                <!-- Desktop Horizontal -->
                                <div class="hidden sm:flex items-start justify-between relative">
                                    <div class="absolute top-5 left-[10%] right-[10%] h-0.5 bg-slate-200 z-0"></div>
                                    <div
                                        class="absolute top-5 left-[10%] h-0.5 z-0 transition-all duration-500"
                                        :class="selectedTicket.status === 'rejected' || selectedTicket.status === 'revision' ? 'bg-red-400' : 'bg-blue-500'"
                                        :style="{ width: `${((Math.max(getSimplifiedStep(selectedTicket.status), 1) - 1) / (progressSteps.length - 1)) * 80}%` }"
                                    ></div>
                                    <div v-for="step in progressSteps" :key="step.step" class="flex flex-col items-center z-10 flex-1">
                                        <div
                                            :class="[
                                                'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-300',
                                                isStepError(step.step, selectedTicket.status)
                                                    ? 'bg-red-500 border-red-500 text-white'
                                                    : isStepCompleted(step.step, selectedTicket.status)
                                                        ? 'bg-blue-600 border-blue-600 text-white'
                                                        : 'bg-white border-slate-300 text-slate-400',
                                            ]"
                                        >
                                            <svg
                                                v-if="isStepCompleted(step.step, selectedTicket.status) && !isStepActive(step.step, selectedTicket.status) && !isStepError(step.step, selectedTicket.status)"
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg
                                                v-else-if="isStepError(step.step, selectedTicket.status)"
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01" />
                                            </svg>
                                            <span v-else>{{ step.step }}</span>
                                        </div>
                                        <p class="mt-2 text-xs font-semibold text-slate-700 text-center">{{ step.label }}</p>
                                        <p class="text-xs text-slate-400 text-center mt-0.5">{{ step.description }}</p>
                                    </div>
                                </div>

                                <!-- Mobile Vertical -->
                                <div class="sm:hidden space-y-0">
                                    <div v-for="(step, index) in progressSteps" :key="step.step" class="flex items-start gap-3">
                                        <div class="flex flex-col items-center">
                                            <div
                                                :class="[
                                                    'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all',
                                                    isStepError(step.step, selectedTicket.status)
                                                        ? 'bg-red-500 border-red-500 text-white'
                                                        : isStepCompleted(step.step, selectedTicket.status)
                                                            ? 'bg-blue-600 border-blue-600 text-white'
                                                            : 'bg-white border-slate-300 text-slate-400',
                                                ]"
                                            >
                                                <span>{{ step.step }}</span>
                                            </div>
                                            <div
                                                v-if="index < progressSteps.length - 1"
                                                :class="['w-0.5 h-8', isStepCompleted(step.step + 1, selectedTicket.status) ? 'bg-blue-500' : 'bg-slate-200']"
                                            ></div>
                                        </div>
                                        <div class="pb-6 pt-1">
                                            <p class="text-sm font-semibold text-slate-700">{{ step.label }}</p>
                                            <p class="text-xs text-slate-400">{{ step.description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Dokumen
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div v-if="selectedTicket.documents?.length > 0 || selectedTicket.attachments?.length > 0">
                                    <div class="space-y-3">
                                        <div
                                            v-for="doc in [...(selectedTicket.attachments || []), ...(selectedTicket.documents || [])]"
                                            :key="'doc-' + doc.id"
                                            class="flex items-center justify-between bg-slate-50 rounded-xl p-4 hover:bg-slate-100 transition-colors"
                                        >
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-700 truncate">{{ doc.name }}</p>
                                                    <p class="text-xs text-slate-400">{{ doc.type }} · {{ doc.uploaded_at }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <span
                                                    v-if="doc.status"
                                                    :class="[getDocStatusConfig(doc.status).color, 'px-2 py-0.5 rounded-full text-xs font-semibold']"
                                                >
                                                    {{ getDocStatusConfig(doc.status).label }}
                                                </span>
                                                <button
                                                    @click.stop="viewDocument(doc)"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="Lihat"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8 text-slate-400">
                                    <p class="text-sm">Belum ada dokumen.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Petugas -->
                        <div v-if="revisionReplies.length > 0" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    Catatan Petugas
                                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                        {{ revisionReplies.length }}
                                    </span>
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6 space-y-4">
                                <div
                                    v-for="reply in revisionReplies"
                                    :key="reply.id"
                                    class="relative pl-4 border-l-2 border-amber-300"
                                >
                                    <div class="bg-amber-50/50 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 bg-amber-200 rounded-full flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <span class="text-sm font-semibold text-slate-700">{{ reply.replied_by }}</span>
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">
                                                    {{ reply.role }}
                                                </span>
                                            </div>
                                            <span class="text-xs text-slate-400">{{ reply.created_at }}</span>
                                        </div>
                                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ reply.content }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ─── Sidebar ─── -->
                    <div class="space-y-6">
                        <!-- Download Hasil -->
                        <div v-if="selectedTicket.status === 'completed' && selectedTicket.has_result" class="bg-white rounded-2xl shadow-sm border border-green-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-5 py-4">
                                <h2 class="text-white font-semibold flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Hasil Permohonan
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                        <span class="text-3xl">🎉</span>
                                    </div>
                                    <p class="text-sm text-slate-600">Data hasil siap diunduh.</p>
                                </div>
                                <button
                                    @click="downloadResult(selectedTicket.id)"
                                    class="w-full py-3 px-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold text-sm hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-200 flex items-center justify-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh Hasil Data
                                </button>

                                <div class="mt-3" v-if="!selectedTicket.has_feedback">
                                    <Link
                                        :href="`/pemohon/feedback/${selectedTicket.id}`"
                                        class="w-full py-3 px-4 bg-blue-50 text-blue-600 rounded-xl font-semibold text-sm hover:bg-blue-100 transition-all flex items-center justify-center gap-2 border border-blue-200"
                                    >
                                        ⭐ Berikan Feedback
                                    </Link>
                                </div>
                                <div v-else class="mt-3 py-3 px-4 bg-green-50 rounded-xl text-center">
                                    <p class="text-xs text-green-700 font-medium">✅ Feedback telah diberikan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100">
                                <h2 class="font-semibold text-slate-800 text-sm">Ringkasan</h2>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="text-center">
                                    <div
                                        :class="[
                                            'w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-3',
                                            selectedTicket.status === 'completed' ? 'bg-green-100' :
                                            selectedTicket.status === 'rejected' ? 'bg-red-100' :
                                            selectedTicket.status === 'revision' ? 'bg-amber-100' : 'bg-blue-100',
                                        ]"
                                    >
                                        <span class="text-3xl">{{ getStatusConfig(selectedTicket.status).icon }}</span>
                                    </div>
                                    <span :class="[getStatusConfig(selectedTicket.status).color, 'px-4 py-1.5 rounded-full text-sm font-bold border inline-block']">
                                        {{ selectedTicket.status_label || getStatusConfig(selectedTicket.status).label }}
                                    </span>
                                </div>
                                <div class="space-y-3 pt-4 border-t border-slate-100">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Kode Tiket</span>
                                        <span class="font-mono font-semibold text-slate-700 text-xs">{{ selectedTicket.ticket_number }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Diajukan</span>
                                        <span class="text-slate-700">{{ selectedTicket.created_at }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Diperbarui</span>
                                        <span class="text-slate-700">{{ selectedTicket.updated_at }}</span>
                                    </div>
                                    <div v-if="selectedTicket.assignment_label" class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Ditangani</span>
                                        <span class="text-slate-700">{{ selectedTicket.assignment_label }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Dokumen</span>
                                        <span class="text-slate-700 font-semibold">
                                            {{ (selectedTicket.documents?.length || 0) + (selectedTicket.attachments?.length || 0) }} file
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bantuan -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 p-5">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-blue-800 mb-1">Butuh Bantuan?</h3>
                                    <p class="text-xs text-blue-700 leading-relaxed">
                                        Hubungi layanan bantuan kami jika ada pertanyaan terkait permohonan.
                                    </p>
                                    <a href="/bantuan" class="text-xs font-semibold text-blue-600 hover:underline mt-2 inline-block">
                                        Lihat FAQ →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white/50 border-t border-slate-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <p class="text-xs text-slate-400">© 2026 BPKH — Badan Pengelola Keuangan Haji</p>
                    <p class="text-xs text-slate-400">Sistem Permohonan Data v1.0</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
