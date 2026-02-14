<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    filters: Object,
});

const search = ref(props.filters?.search ?? "");
const status = ref(props.filters?.status ?? "");

const submit = () => {
    router.get(
        route("admin.tickets.index"),
        { search: search.value, status: status.value },
        { preserveState: true, replace: true },
    );
};
</script>

<template>
    <div class="bg-white rounded-xl border p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <!-- Search -->
            <div class="md:col-span-2">
                <div class="relative">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari tiket..."
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
            <select
                v-model="status"
                class="rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600"
            >
                <option value="">Semua Status</option>
                <option value="NEW">Baru</option>
                <option value="VERIFIED">Terverifikasi</option>
                <option value="APPROVED">Disetujui</option>
                <option value="REJECTED">Ditolak</option>
            </select>

            <!-- Urutan -->
            <select
                v-model="filters.sort"
                class="rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600"
            >
                <option value="latest">Terbaru</option>
                <option value="oldest">Terlama</option>
            </select>

            <!-- Filter Button -->
            <button
                @click="submit"
                class="flex items-center justify-center gap-2 rounded-lg bg-green-700 text-white px-4 py-2 hover:bg-green-800"
            >
                <span>🔎</span>
                Filter
            </button>
        </div>
    </div>
</template>
