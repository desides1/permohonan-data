<script setup>
import { router } from "@inertiajs/vue3";
import { ref, reactive, watch } from "vue";

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const showAdvanced = ref(false);

const filters = reactive({
    search: "",
    status: "",
    sort: "latest",
    date_from: "",
    date_to: "",
    read: "",
});

// merge dari server (AMAN)
Object.assign(filters, props.filters);

const submit = () => {
    router.get(
        route("admin.tickets.index"),
        { ...filters },
        { preserveState: true, replace: true },
    );
};

// debounce search
let timeout = null;
watch(
    () => filters.search,
    () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            submit();
        }, 500);
    },
);
</script>

<template>
    <div class="bg-white rounded-xl border p-4 space-y-4">
        <!-- Top Filters -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="text-sm text-gray-600">Cari Tiket</label>
                <div class="relative">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Kode tiket / nama pemohon"
                        class="w-full rounded-lg border-gray-300 pl-10 focus:ring-green-600 focus:border-green-600"
                    />
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                    >
                        🔍
                    </span>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="text-sm text-gray-600">Status</label>
                <select
                    v-model="filters.status"
                    class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600"
                >
                    <option value="">Semua Status</option>
                    <option value="NEW">Baru</option>
                    <option value="VERIFIED">Terverifikasi</option>
                    <option value="APPROVED">Disetujui</option>
                    <option value="REJECTED">Ditolak</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="text-sm text-gray-600">Urutan</label>
                <select
                    v-model="filters.sort"
                    class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600"
                >
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                </select>
            </div>

            <!-- Toggle Advanced -->
            <button
                @click="showAdvanced = !showAdvanced"
                type="button"
                class="h-[42px] rounded-lg border border-gray-300 hover:bg-gray-100 text-sm flex items-center justify-center gap-2"
            >
                ⚙️ Filter Lainnya
            </button>
        </div>

        <!-- Advanced Filters -->
        <div
            v-if="showAdvanced"
            class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4"
        >
            <!-- Date From -->
            <div>
                <label class="text-sm text-gray-600">Tanggal Mulai</label>
                <input
                    type="date"
                    v-model="filters.date_from"
                    class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600"
                />
            </div>

            <!-- Date To -->
            <div>
                <label class="text-sm text-gray-600">Tanggal Akhir</label>
                <input
                    type="date"
                    v-model="filters.date_to"
                    class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600"
                />
            </div>

            <!-- Read Status -->
            <div>
                <label class="text-sm text-gray-600">Status Dibaca</label>
                <select
                    v-model="filters.read"
                    class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600"
                >
                    <option value="">Semua</option>
                    <option value="read">Sudah Dibaca</option>
                    <option value="unread">Belum Dibaca</option>
                </select>
            </div>
        </div>

        <!-- Action -->
        <div class="flex justify-end">
            <button
                @click="submit"
                class="rounded-lg bg-green-700 text-white px-6 py-2 hover:bg-green-800"
            >
                Terapkan Filter
            </button>
        </div>
    </div>
</template>
