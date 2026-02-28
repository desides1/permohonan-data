<script setup>
import { Link, router } from "@inertiajs/vue3";

const props = defineProps({
    tickets: {
        type: Object,
        required: true,
    },
    userRole: {
        type: String,
        default: "",
    },
});

const canShowDisposisi = ["pimpinan_bpkh", "pimpinan_ppkh"].includes(
    props.userRole,
);

function confirmDelete(ticketCode) {
    if (!confirm(`Apakah Anda yakin ingin menghapus tiket #${ticketCode}?`)) {
        return;
    }

    router.delete(route("admin.tickets.delete", ticketCode), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Tiket</th>
                        <th class="px-4 py-3 text-left">Pemohon</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Penugasan Saat Ini</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th v-if="canShowDisposisi" class="px-4 py-3 text-left">
                            Penugasan
                        </th>
                        <th class="px-4 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <tr
                        v-for="ticket in props.tickets.data"
                        :key="ticket.ticket_code"
                        class="hover:bg-gray-50"
                        :class="{ 'bg-blue-50': !ticket.is_read }"
                    >
                        <!-- Tiket -->
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <span
                                v-if="!ticket.is_read"
                                class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2"
                            ></span>
                            #{{ ticket.ticket_code }}
                        </td>

                        <!-- Pemohon -->
                        <td class="px-4 py-3">
                            {{ ticket.name }}
                        </td>

                        <!-- Status — warna dari backend, tanpa class conditional -->
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                :style="{
                                    backgroundColor:
                                        (ticket.status_color ?? '#94a3b8') +
                                        '18',
                                    color: ticket.status_color ?? '#64748b',
                                    border:
                                        '1px solid ' +
                                        (ticket.status_color ?? '#94a3b8') +
                                        '30',
                                }"
                            >
                                {{ ticket.status }}
                            </span>
                        </td>

                        <!-- Penugasan Saat Ini -->
                        <td class="px-4 py-3 text-gray-600">
                            {{ ticket.current_assignment ?? "-" }}
                        </td>

                        <!-- Tanggal -->
                        <td class="px-4 py-3 text-gray-600">
                            {{ ticket.date }}
                        </td>

                        <!-- Penugasan (hanya Pimpinan BPKH & PPKH) -->
                        <td v-if="canShowDisposisi" class="px-4 py-3">
                            <Link
                                :href="
                                    route(
                                        'admin.tickets.show',
                                        ticket.ticket_code,
                                    )
                                "
                                class="inline-flex items-center rounded-md bg-green-600 px-3 py-1 text-xs font-semibold text-white hover:bg-green-700"
                            >
                                Disposisi
                            </Link>
                        </td>

                        <!-- Tindakan -->
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-3">
                                <Link
                                    :href="
                                        route(
                                            'admin.tickets.show',
                                            ticket.ticket_code,
                                        )
                                    "
                                    class="text-green-600 hover:text-green-800"
                                    title="Lihat Detail"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                                        />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </Link>
                                <!-- Hapus Tiket -->
                                <button
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                    title="Hapus Tiket"
                                    @click="confirmDelete(ticket.ticket_code)"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M3 6h18" />
                                        <path d="M19 6l-1 14H6L5 6" />
                                        <path d="M10 11v6" />
                                        <path d="M14 11v6" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-if="props.tickets.data.length === 0">
                        <td
                            :colspan="canShowDisposisi ? 7 : 6"
                            class="py-10 text-center text-gray-500"
                        >
                            Tidak ada data permohonan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="props.tickets.total > 0"
            class="flex items-center justify-between px-4 py-3 text-sm text-gray-600"
        >
            <span>
                Menampilkan {{ props.tickets.from }}–{{ props.tickets.to }} dari
                {{ props.tickets.total }} hasil
            </span>

            <div class="flex gap-1">
                <Link
                    v-for="link in props.tickets.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    class="px-3 py-1 rounded border text-xs"
                    :class="{
                        'bg-green-600 text-white border-green-600': link.active,
                        'hover:bg-gray-50': !link.active && link.url,
                        'text-gray-300 cursor-not-allowed': !link.url,
                    }"
                    v-html="link.label"
                    :preserve-scroll="true"
                />
            </div>
        </div>
    </div>
</template>
