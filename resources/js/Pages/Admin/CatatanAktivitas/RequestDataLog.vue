<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import { Badge } from "@/Components/ui/badge";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { Search, Eye, ChevronLeft, ChevronRight } from "lucide-vue-next";

const props = defineProps({
    activities: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? "");
let debounce = null;

watch(search, (val) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            route("admin.activity-log.request-data"),
            { search: val || undefined },
            { preserveState: true, replace: true },
        );
    }, 400);
});

const actionLabels = {
    verify: "Verifikasi",
    approve: "Persetujuan",
    assign: "Disposisi",
    mark_ready: "Data Siap",
    review_ppkh: "Review PPKH",
    forward_to_bpkh: "Teruskan BPKH",
    request_revision: "Minta Revisi",
    final_approve: "Setujui Final",
    finalize: "Selesaikan",
    reject: "Tolak",
    upload_document: "Upload Dokumen",
};

const actionColors = {
    verify: "bg-green-100 text-green-700",
    approve: "bg-emerald-100 text-emerald-700",
    assign: "bg-blue-100 text-blue-700",
    mark_ready: "bg-cyan-100 text-cyan-700",
    review_ppkh: "bg-purple-100 text-purple-700",
    forward_to_bpkh: "bg-indigo-100 text-indigo-700",
    request_revision: "bg-orange-100 text-orange-700",
    final_approve: "bg-teal-100 text-teal-700",
    finalize: "bg-green-100 text-green-800",
    reject: "bg-red-100 text-red-700",
    upload_document: "bg-sky-100 text-sky-700",
};
</script>

<template>
    <LayoutDashboard>
        <section class="space-y-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">
                    Aktivitas Permohonan Data
                </h1>
                <p class="text-sm text-slate-500">
                    Riwayat semua aktivitas pada tiket permohonan
                </p>
            </div>

            <!-- Filter -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 max-w-sm">
                    <Search
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    />
                    <Input
                        v-model="search"
                        placeholder="Cari kode tiket atau deskripsi..."
                        class="pl-9"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode Tiket</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                                <th
                                    class="px-4 py-3 text-left hidden md:table-cell"
                                >
                                    Deskripsi
                                </th>
                                <th class="px-4 py-3 text-left">Oleh</th>
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="act in activities.data"
                                :key="act.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ act.ticket_code }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="
                                            actionColors[act.action] ??
                                            'bg-gray-100 text-gray-700'
                                        "
                                    >
                                        {{
                                            actionLabels[act.action] ??
                                            act.action
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-600 max-w-xs truncate hidden md:table-cell"
                                >
                                    {{ act.description }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ act.causer }}
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-500 whitespace-nowrap"
                                >
                                    <span class="hidden sm:inline">{{
                                        act.created_at
                                    }}</span>
                                    <span class="sm:hidden">{{
                                        act.time_ago
                                    }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <Link
                                        :href="
                                            route(
                                                'admin.activity-log.request-data.detail',
                                                act.ticket_code,
                                            )
                                        "
                                    >
                                        <Button variant="ghost" size="sm">
                                            <Eye class="w-4 h-4" />
                                        </Button>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="activities.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-4 py-8 text-center text-gray-400"
                                >
                                    Belum ada aktivitas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="activities.last_page > 1"
                    class="flex items-center justify-between border-t px-4 py-3"
                >
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ activities.from }}-{{
                            activities.to
                        }}
                        dari {{ activities.total }}
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in activities.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            class="px-3 py-1 text-xs rounded-md border"
                            :class="
                                link.active
                                    ? 'bg-green-600 text-white border-green-600'
                                    : 'hover:bg-gray-50'
                            "
                            v-html="link.label"
                            preserve-state
                        />
                    </div>
                </div>
            </div>
        </section>
    </LayoutDashboard>
</template>
