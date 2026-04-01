<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    show: Boolean,
    schedule: Object,
});

const emit = defineEmits(["close"]);

const form = useForm({
    frequency: props.schedule?.frequency ?? "daily",
    time: props.schedule?.time ?? "02:00",
    is_active: props.schedule?.is_active ?? true,
    include_files: props.schedule?.include_files ?? true,
    include_database: props.schedule?.include_database ?? true,
    auto_cleanup: props.schedule?.auto_cleanup ?? true,
    retention_days: props.schedule?.retention_days ?? 30,
});

watch(
    () => props.schedule,
    (val) => {
        if (val) {
            form.frequency = val.frequency ?? "daily";
            form.time = val.time ?? "02:00";
            form.is_active = val.is_active ?? true;
            form.include_files = val.include_files ?? true;
            form.include_database = val.include_database ?? true;
            form.auto_cleanup = val.auto_cleanup ?? true;
            form.retention_days = val.retention_days ?? 30;
        }
    },
    { immediate: true },
);

function submit() {
    form.put(route("admin.backup.schedule.update"), {
        preserveScroll: true,
        onSuccess: () => emit("close"),
    });
}

function close() {
    form.clearErrors();
    emit("close");
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4"
            >
                <!-- Overlay -->
                <div
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                    @click="close"
                />

                <!-- Modal Content -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4"
                >
                    <div
                        v-if="show"
                        class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-gray-800"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                        >
                            <div>
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                >
                                    Pengaturan Jadwal Pencadangan
                                </h3>
                                <p
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Atur pencadangan otomatis sesuai kebutuhan.
                                </p>
                            </div>
                            <button
                                @click="close"
                                class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700"
                            >
                                <svg
                                    class="h-5 w-5"
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
                            </button>
                        </div>

                        <!-- Form -->
                        <form
                            @submit.prevent="submit"
                            class="space-y-5 px-6 py-5"
                        >
                            <!-- Status Aktif -->
                            <div
                                class="flex items-center justify-between rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50"
                            >
                                <div>
                                    <label
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                        >Status Pencadangan Otomatis</label
                                    >
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Aktifkan untuk menjalankan pencadangan
                                        secara terjadwal.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="form.is_active = !form.is_active"
                                    :class="[
                                        'relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200',
                                        form.is_active
                                            ? 'bg-blue-600'
                                            : 'bg-gray-300 dark:bg-gray-600',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-200',
                                            form.is_active
                                                ? 'translate-x-5'
                                                : 'translate-x-0',
                                        ]"
                                    />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Frekuensi -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Frekuensi
                                    </label>
                                    <select
                                        v-model="form.frequency"
                                        class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="daily">Harian</option>
                                        <option value="weekly">Mingguan</option>
                                        <option value="monthly">Bulanan</option>
                                    </select>
                                    <p
                                        v-if="form.errors.frequency"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.frequency }}
                                    </p>
                                </div>

                                <!-- Waktu -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Waktu (WIB)
                                    </label>
                                    <input
                                        v-model="form.time"
                                        type="time"
                                        class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                    <p
                                        v-if="form.errors.time"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.time }}
                                    </p>
                                </div>
                            </div>

                            <!-- Retensi Data -->
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Batas Retensi (hari)
                                </label>
                                <input
                                    v-model.number="form.retention_days"
                                    type="number"
                                    min="7"
                                    max="365"
                                    class="w-full rounded-xl border-gray-300 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Data cadangan yang lebih lama dari
                                    {{ form.retention_days }} hari akan otomatis
                                    dihapus.
                                </p>
                                <p
                                    v-if="form.errors.retention_days"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {{ form.errors.retention_days }}
                                </p>
                            </div>

                            <!-- Opsi Backup -->
                            <div class="space-y-3">
                                <p
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Data yang Dicadangkan
                                </p>

                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-900/30"
                                >
                                    <input
                                        v-model="form.include_database"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                    />
                                    <div>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                            >Database</span
                                        >
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            Seluruh data permohonan, pemohon,
                                            dan hasil seksi.
                                        </p>
                                    </div>
                                </label>

                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-900/30"
                                >
                                    <input
                                        v-model="form.include_files"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                    />
                                    <div>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                            >File Lampiran</span
                                        >
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            Dokumen lampiran permohonan yang
                                            diunggah.
                                        </p>
                                    </div>
                                </label>

                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-900/30"
                                >
                                    <input
                                        v-model="form.auto_cleanup"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                    />
                                    <div>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                            >Pembersihan Otomatis</span
                                        >
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            Hapus cadangan lama secara otomatis
                                            sesuai batas retensi.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </form>

                        <!-- Footer -->
                        <div
                            class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-700"
                        >
                            <button
                                @click="close"
                                type="button"
                                class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                Batal
                            </button>
                            <button
                                @click="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <svg
                                    v-if="form.processing"
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
                                {{
                                    form.processing
                                        ? "Menyimpan..."
                                        : "Simpan Pengaturan"
                                }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
