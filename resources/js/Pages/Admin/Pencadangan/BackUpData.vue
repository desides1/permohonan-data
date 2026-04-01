<script setup>
import { ref, computed } from "vue";
import { router, usePage, Head } from "@inertiajs/vue3";
// import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from "./Partials/StatCard.vue";
import BackupHistoryTable from "./Partials/BackupHistoryTable.vue";
import ScheduleForm from "./Partials/ScheduleForm.vue";
import ConfirmModal from "./Partials/ConfirmModal.vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";

const props = defineProps({
    statistics: Object,
    histories: Object,
    schedule: Object,
});

const page = usePage();

const isBackingUp = ref(false);
const isCleaning = ref(false);
const showBackupConfirm = ref(false);
const showCleanupConfirm = ref(false);
const showScheduleModal = ref(false);

const flash = computed(() => page.props.flash ?? {});

function runBackup() {
    isBackingUp.value = true;
    showBackupConfirm.value = false;

    router.post(
        route("admin.backup.store"),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isBackingUp.value = false;
            },
        },
    );
}

function runCleanup() {
    isCleaning.value = true;
    showCleanupConfirm.value = false;

    router.post(
        route("admin.backup.cleanup"),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isCleaning.value = false;
            },
        },
    );
}
</script>

<template>
    <LayoutDashboard title="Pencadangan Data">
        <Head title="Pencadangan Data" />

        <template #header>
            <div class="flex flex-col gap-1">
                <h2
                    class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200"
                >
                    Pencadangan Data
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Kelola pencadangan data permohonan ke Google Drive secara
                    manual maupun terjadwal.
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div
                        v-if="flash.success"
                        class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/50"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900"
                            >
                                <svg
                                    class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                            </div>
                            <p
                                class="text-sm font-medium text-emerald-800 dark:text-emerald-200"
                            >
                                {{ flash.success }}
                            </p>
                        </div>
                    </div>
                </Transition>

                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div
                        v-if="flash.error"
                        class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/50"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900"
                            >
                                <svg
                                    class="h-5 w-5 text-red-600 dark:text-red-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </div>
                            <p
                                class="text-sm font-medium text-red-800 dark:text-red-200"
                            >
                                {{ flash.error }}
                            </p>
                        </div>
                    </div>
                </Transition>

                <!-- Statistik Overview -->
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <StatCard
                        title="Total Cadangan"
                        :value="statistics.total_backups"
                        icon="archive"
                        color="blue"
                    />
                    <StatCard
                        title="Total Ukuran"
                        :value="statistics.total_size"
                        icon="database"
                        color="indigo"
                    />
                    <StatCard
                        title="Cadangan Hari Ini"
                        :value="statistics.today_backup_count"
                        icon="calendar"
                        color="emerald"
                    />
                    <StatCard
                        title="Gagal"
                        :value="statistics.failed_count"
                        icon="alert"
                        :color="statistics.failed_count > 0 ? 'red' : 'gray'"
                    />
                </div>

                <!-- Action Bar -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div
                        class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
                    >
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Tindakan Pencadangan
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                            >
                                <template v-if="statistics.last_backup">
                                    Cadangan terakhir:
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300"
                                        >{{
                                            statistics.last_backup.created_at
                                        }}</span
                                    >
                                </template>
                                <template v-else>
                                    Belum ada cadangan yang dibuat.
                                </template>
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <!-- Tombol Backup Manual -->
                            <button
                                @click="showBackupConfirm = true"
                                :disabled="isBackingUp"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-800"
                            >
                                <svg
                                    v-if="isBackingUp"
                                    class="h-4 w-4 animate-spin"
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
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                    />
                                </svg>
                                {{
                                    isBackingUp
                                        ? "Sedang Mencadangkan..."
                                        : "Cadangkan Sekarang"
                                }}
                            </button>

                            <!-- Tombol Jadwal -->
                            <button
                                @click="showScheduleModal = true"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                Atur Jadwal
                            </button>

                            <!-- Tombol Cleanup -->
                            <button
                                @click="showCleanupConfirm = true"
                                :disabled="isCleaning"
                                class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 px-5 py-2.5 text-sm font-semibold text-amber-700 shadow-sm transition-all hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:border-amber-700 dark:bg-amber-950/50 dark:text-amber-400 dark:hover:bg-amber-950"
                            >
                                <svg
                                    v-if="isCleaning"
                                    class="h-4 w-4 animate-spin"
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
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                                {{
                                    isCleaning
                                        ? "Membersihkan..."
                                        : "Bersihkan Data Lama"
                                }}
                            </button>
                        </div>
                    </div>

                    <!-- Info Jadwal Aktif -->
                    <div
                        v-if="schedule && schedule.is_active"
                        class="mt-4 rounded-xl bg-blue-50 p-4 dark:bg-blue-950/30"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900"
                            >
                                <svg
                                    class="h-4 w-4 text-blue-600 dark:text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div
                                class="text-sm text-blue-800 dark:text-blue-200"
                            >
                                <p class="font-medium">
                                    Pencadangan Otomatis Aktif
                                </p>
                                <p
                                    class="mt-1 text-blue-600 dark:text-blue-400"
                                >
                                    Jadwal:
                                    <strong>{{
                                        schedule.frequency_label
                                    }}</strong>
                                    pukul
                                    <strong>{{ schedule.time }} WIB</strong>
                                    <span v-if="schedule.next_run_at">
                                        · Berikutnya:
                                        <strong>{{
                                            schedule.next_run_at
                                        }}</strong></span
                                    >
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Backup -->
                <BackupHistoryTable :histories="histories" />
            </div>
        </div>

        <!-- Modals -->
        <ConfirmModal
            :show="showBackupConfirm"
            title="Konfirmasi Pencadangan"
            message="Apakah Anda yakin ingin mencadangkan seluruh data permohonan sekarang? Proses ini mungkin memerlukan beberapa menit."
            confirm-text="Ya, Cadangkan"
            confirm-color="blue"
            @confirm="runBackup"
            @cancel="showBackupConfirm = false"
        />

        <ConfirmModal
            :show="showCleanupConfirm"
            title="Konfirmasi Pembersihan"
            message="Apakah Anda yakin ingin membersihkan data cadangan lama? Data cadangan yang melewati batas retensi akan dihapus permanen."
            confirm-text="Ya, Bersihkan"
            confirm-color="amber"
            @confirm="runCleanup"
            @cancel="showCleanupConfirm = false"
        />

        <ScheduleForm
            :show="showScheduleModal"
            :schedule="schedule"
            @close="showScheduleModal = false"
        />
    </LayoutDashboard>
</template>
