<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import { Badge } from "@/Components/ui/badge";
import { Button } from "@/Components/ui/button";
import { Link } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Mail,
    Phone,
    CheckCircle,
    XCircle,
    Clock,
} from "lucide-vue-next";

const props = defineProps({
    ticket: Object,
    activities: Array,
    notifications: Array,
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
    verify: "border-green-400",
    approve: "border-emerald-400",
    assign: "border-blue-400",
    request_revision: "border-orange-400",
    reject: "border-red-400",
    finalize: "border-green-500",
    upload_document: "border-sky-400",
};

const statusConfig = {
    sent: {
        label: "Terkirim",
        class: "bg-green-100 text-green-700",
        icon: CheckCircle,
    },
    failed: { label: "Gagal", class: "bg-red-100 text-red-700", icon: XCircle },
    pending: {
        label: "Menunggu",
        class: "bg-yellow-100 text-yellow-700",
        icon: Clock,
    },
};
</script>

<template>
    <LayoutDashboard>
        <section class="max-w-5xl mx-auto space-y-6 px-4 sm:px-0">
            <!-- Back + Header -->
            <div>
                <Link
                    :href="route('admin.activity-log.request-data')"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-3"
                >
                    <ArrowLeft class="w-4 h-4" />
                    Kembali
                </Link>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex-1">
                        <h1 class="text-xl font-semibold text-slate-800">
                            Detail Aktivitas — {{ ticket.ticket_code }}
                        </h1>
                        <p class="text-sm text-slate-500 mt-0.5">
                            {{ ticket.name }} · {{ ticket.email }}
                        </p>
                    </div>
                    <span
                        class="inline-flex self-start items-center rounded-full px-3 py-1 text-xs font-medium"
                        :style="{
                            backgroundColor:
                                (ticket.status_color ?? '#94a3b8') + '18',
                            color: ticket.status_color ?? '#64748b',
                            border:
                                '1px solid ' +
                                (ticket.status_color ?? '#94a3b8') +
                                '30',
                        }"
                    >
                        {{ ticket.status }}
                    </span>
                </div>
            </div>

            <!-- Timeline Aktivitas -->
            <div class="bg-white rounded-xl border p-4 sm:p-6">
                <h2 class="font-semibold text-gray-800 mb-4">
                    Riwayat Aktivitas Tiket
                </h2>
                <div class="space-y-0">
                    <div
                        v-for="(act, idx) in activities"
                        :key="act.id"
                        class="flex gap-4"
                    >
                        <!-- Timeline line -->
                        <div class="flex flex-col items-center">
                            <div
                                class="w-3 h-3 rounded-full border-2 mt-1.5"
                                :class="
                                    actionColors[act.action] ??
                                    'border-gray-300'
                                "
                                :style="{ backgroundColor: 'white' }"
                            />
                            <div
                                v-if="idx < activities.length - 1"
                                class="w-0.5 flex-1 bg-gray-200"
                            />
                        </div>

                        <div class="pb-6 flex-1 min-w-0">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2"
                            >
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 self-start"
                                >
                                    {{ actionLabels[act.action] ?? act.action }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ act.created_at }} · {{ act.causer }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 mt-1">
                                {{ act.description }}
                            </p>
                            <div
                                v-if="act.reason"
                                class="mt-2 rounded-md bg-orange-50 border border-orange-200 px-3 py-2"
                            >
                                <p
                                    class="text-xs font-semibold text-orange-700"
                                >
                                    Catatan Revisi:
                                </p>
                                <p
                                    class="text-xs text-gray-700 whitespace-pre-line"
                                >
                                    {{ act.reason }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <p
                        v-if="activities.length === 0"
                        class="text-sm text-gray-400 text-center py-6"
                    >
                        Belum ada riwayat aktivitas.
                    </p>
                </div>
            </div>

            <!-- Notifikasi terkait tiket -->
            <div class="bg-white rounded-xl border p-4 sm:p-6">
                <h2 class="font-semibold text-gray-800 mb-4">
                    Notifikasi Terkait
                </h2>
                <div class="space-y-3">
                    <div
                        v-for="n in notifications"
                        :key="n.id"
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 rounded-lg border p-3 hover:bg-gray-50"
                    >
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <component
                                :is="n.channel === 'email' ? Mail : Phone"
                                class="w-4 h-4 flex-shrink-0"
                                :class="
                                    n.channel === 'email'
                                        ? 'text-blue-500'
                                        : 'text-green-500'
                                "
                            />
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">
                                    {{ n.recipient }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ n.type }} · {{ n.created_at }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="flex items-center gap-2 self-end sm:self-auto"
                        >
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="statusConfig[n.status]?.class"
                            >
                                <component
                                    :is="statusConfig[n.status]?.icon"
                                    class="w-3 h-3"
                                />
                                {{ statusConfig[n.status]?.label }}
                            </span>
                            <p
                                v-if="n.error_message"
                                class="text-xs text-red-500 max-w-xs truncate"
                            >
                                {{ n.error_message }}
                            </p>
                        </div>
                    </div>

                    <p
                        v-if="notifications.length === 0"
                        class="text-sm text-gray-400 text-center py-6"
                    >
                        Belum ada notifikasi untuk tiket ini.
                    </p>
                </div>
            </div>
        </section>
    </LayoutDashboard>
</template>
