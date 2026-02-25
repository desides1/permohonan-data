<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import ConfirmDialog from "./ConfirmDialog.vue";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    ticket: Object,
    can: Object,
    suratPermohonan: Object,
    lampiranLainnya: Array,
    activities: Array,
});

const fileUrl = (path) => `/storage/${path}`;

/* ────────── Dialog State ────────── */
const dialogOpen = ref(false);
const actionType = ref("");
const actionReason = ref("");
const actionNotes = ref("");
const isSubmitting = ref(false);

const actionConfig = {
    verify: {
        title: "Verifikasi Permohonan",
        message:
            "Apakah Anda yakin ingin memverifikasi dan meneruskan ke Pimpinan BPKH?",
        color: "green",
        needsReason: false,
    },
    approve: {
        title: "Setujui Permohonan",
        message:
            "Apakah Anda yakin ingin menyetujui dan meneruskan ke Pimpinan PPKH?",
        color: "green",
        needsReason: false,
    },
    assign: {
        title: "Disposisi ke Seksi",
        message: "Apakah Anda yakin ingin menugaskan permohonan ini ke Seksi?",
        color: "blue",
        needsReason: false,
        needsNotes: true,
    },
    markReady: {
        title: "Tandai Data Siap",
        message: "Apakah Anda yakin data sudah siap untuk direview?",
        color: "green",
        needsReason: false,
    },
    reviewPpkh: {
        title: "Review Data",
        message: "Mulai review data permohonan ini?",
        color: "blue",
        needsReason: false,
    },
    forwardToBpkh: {
        title: "Teruskan ke Pimpinan BPKH",
        message:
            "Data sudah direview. Teruskan ke Pimpinan BPKH untuk persetujuan final?",
        color: "green",
        needsReason: false,
    },
    requestRevision: {
        title: "Minta Revisi",
        message: "Jelaskan alasan revisi:",
        color: "orange",
        needsReason: true,
    },
    finalApprove: {
        title: "Persetujuan Final",
        message: "Apakah Anda yakin memberikan persetujuan final?",
        color: "green",
        needsReason: false,
    },
    finalize: {
        title: "Selesaikan Permohonan",
        message: "Apakah Anda yakin ingin menyelesaikan permohonan ini?",
        color: "green",
        needsReason: false,
    },
    reject: {
        title: "Tolak Permohonan",
        message: "Jelaskan alasan penolakan:",
        color: "red",
        needsReason: true,
    },
};

function openDialog(type) {
    actionType.value = type;
    actionReason.value = "";
    actionNotes.value = "";
    dialogOpen.value = true;
}

function closeDialog() {
    dialogOpen.value = false;
    isSubmitting.value = false;
}

function submitAction() {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    const id = props.ticket.ticket_progress.id;
    const routeMap = {
        verify: "admin.tickets.verify",
        approve: "admin.tickets.approve",
        assign: "admin.tickets.assign",
        markReady: "admin.tickets.markReady",
        reviewPpkh: "admin.tickets.reviewPpkh",
        forwardToBpkh: "admin.tickets.forwardToBpkh",
        requestRevision: "admin.tickets.requestRevision",
        finalApprove: "admin.tickets.finalApprove",
        finalize: "admin.tickets.finalize",
        reject: "admin.tickets.reject",
    };

    const routeName = routeMap[actionType.value];
    if (!routeName) return;

    const data = {};
    if (
        actionType.value === "reject" ||
        actionType.value === "requestRevision"
    ) {
        data.reason = actionReason.value;
    }
    if (actionType.value === "assign") {
        data.notes = actionNotes.value;
    }

    router.post(route(routeName, id), data, {
        onFinish: () => closeDialog(),
    });
}

const currentConfig = () => actionConfig[actionType.value] || {};
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

                    <!-- Status badge — warna dari backend -->
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

        <!-- Confirm Dialog -->
        <Teleport to="body">
            <div
                v-if="dialogOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                @click.self="closeDialog"
            >
                <div
                    class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl space-y-4"
                >
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ currentConfig().title }}
                    </h3>

                    <p class="text-sm text-gray-600">
                        {{ currentConfig().message }}
                    </p>

                    <!-- Reason Field -->
                    <div v-if="currentConfig().needsReason">
                        <textarea
                            v-model="actionReason"
                            rows="3"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Tuliskan alasan (min. 10 karakter)..."
                        ></textarea>
                    </div>

                    <!-- Notes Field -->
                    <div v-if="currentConfig().needsNotes">
                        <textarea
                            v-model="actionNotes"
                            rows="3"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Catatan (opsional)..."
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            @click="closeDialog"
                            class="rounded-lg border px-4 py-2 text-sm text-gray-600 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="submitAction"
                            :disabled="
                                isSubmitting ||
                                (currentConfig().needsReason &&
                                    actionReason.length < 10)
                            "
                            class="rounded-lg px-4 py-2 text-sm text-white disabled:opacity-50"
                            :class="{
                                'bg-green-600 hover:bg-green-700':
                                    currentConfig().color === 'green',
                                'bg-blue-600 hover:bg-blue-700':
                                    currentConfig().color === 'blue',
                                'bg-orange-600 hover:bg-orange-700':
                                    currentConfig().color === 'orange',
                                'bg-red-600 hover:bg-red-700':
                                    currentConfig().color === 'red',
                            }"
                        >
                            {{ isSubmitting ? "Memproses..." : "Konfirmasi" }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </LayoutDashboard>
</template>
