<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    tickets: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Tiket</th>
                        <th class="px-4 py-3 text-left">Pemohon</th>
                        <th class="px-4 py-3 text-left">Status Dibaca</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Penugasan</th>
                        <th class="px-4 py-3 text-center">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <tr
                        v-for="ticket in props.tickets.data"
                        :key="ticket.ticket_code"
                        class="hover:bg-gray-50"
                    >
                        <!-- Tiket -->
                        <td class="px-4 py-3 font-medium text-gray-900">
                            #{{ ticket.ticket_code }}
                        </td>

                        <!-- Pemohon -->
                        <td class="px-4 py-3">
                            {{ ticket.name }}
                        </td>

                        <!-- Status Dibaca -->
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                :class="
                                    ticket.status === 'Belum Dibaca'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-green-100 text-green-700'
                                "
                            >
                                {{ ticket.status ?? "Belum Dibaca" }}
                            </span>
                        </td>

                        <!-- Tanggal -->
                        <td class="px-4 py-3 text-gray-600">
                            {{ ticket.date }}
                        </td>

                        <!-- Penugasan -->
                        <td class="px-4 py-3">
                            <Link
                                href="#"
                                class="inline-flex items-center rounded-md bg-green-600 px-3 py-1 text-xs font-semibold text-white hover:bg-green-700"
                            >
                                Disposisi
                            </Link>
                        </td>

                        <!-- Tindakan -->
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-3">
                                <!-- Detail -->
                                <Link
                                    class="text-green-600 hover:text-green-800"
                                    title="Lihat Detail"
                                >
                                    👁️
                                </Link>

                                <!-- Delete -->
                                <button
                                    class="text-red-600 hover:text-red-800"
                                    title="Hapus"
                                >
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-if="props.tickets.data.length === 0">
                        <td colspan="6" class="py-10 text-center text-gray-500">
                            Tidak ada data permohonan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination (simple placeholder) -->
        <div
            class="flex items-center justify-between px-4 py-3 text-sm text-gray-600"
        >
            <span>
                Menampilkan {{ props.tickets.from }}–{{ props.tickets.to }} dari
                {{ props.tickets.total }} hasil
            </span>
        </div>
    </div>
</template>
