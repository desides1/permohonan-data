<script setup lang="ts">
import { computed } from "vue";
import VueApexCharts from "vue3-apexcharts";
import type { ApexOptions } from "apexcharts";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import {
    Send,
    ShieldCheck,
    ThumbsUp,
    UserCheck,
    PackageCheck,
    Search,
    ScanSearch,
    RotateCcw,
    BadgeCheck,
    CircleCheckBig,
    CircleX,
} from "lucide-vue-next";

/* ========================
   Props dari Inertia
======================== */
interface Props {
    distribution: Record<string, number>;
    tickets?: {
        ticket_code: string;
        name: string;
        status: string;
        current_assignment?: string;
    }[];
}

const props = defineProps<Props>();

/* ========================
   Status Label → Color Mapping
   Key = label() dari TicketStatus enum di backend
======================== */
const STATUS_COLORS: Record<string, string> = {
    Dikirim: "#facc15", // yellow-500
    "Diverifikasi Admin TU": "#3b82f6", // blue-500
    "Disetujui Pimpinan BPKH": "#8b5cf6", // violet-500
    "Ditugaskan ke Seksi": "#f59e0b", // amber-500
    "Data Siap": "#06b6d4", // cyan-500
    "Dalam Peninjauan PPKH": "#f97316", // orange-500
    "Dalam Peninjauan BPKH": "#a855f7", // purple-500
    Revisi: "#ef4444", // red-500
    "Disetujui Final": "#10b981", // emerald-500
    Selesai: "#22c55e", // green-500
    Ditolak: "#dc2626", // red-600
};

const STATUS_ICONS: Record<string, any> = {
    Dikirim: Send,
    "Diverifikasi Admin TU": ShieldCheck,
    "Disetujui Pimpinan BPKH": ThumbsUp,
    "Ditugaskan ke Seksi": UserCheck,
    "Data Siap": PackageCheck,
    "Dalam Peninjauan PPKH": Search,
    "Dalam Peninjauan BPKH": ScanSearch,
    Revisi: RotateCcw,
    "Disetujui Final": BadgeCheck,
    Selesai: CircleCheckBig,
    Ditolak: CircleX,
};

/* ========================
   Chart Computed
======================== */
const chartSeries = computed(() => Object.values(props.distribution ?? {}));

const chartLabels = computed(() => Object.keys(props.distribution ?? {}));

const chartColors = computed(() =>
    chartLabels.value.map((label) => STATUS_COLORS[label] ?? "#94a3b8"),
);

const totalTickets = computed(() =>
    chartSeries.value.reduce((a, b) => a + b, 0),
);

const chartOptions = computed<ApexOptions>(() => ({
    chart: {
        type: "donut",
    },
    labels: chartLabels.value,
    colors: chartColors.value,
    legend: {
        position: "bottom",
        fontSize: "13px",
    },
    dataLabels: {
        enabled: true,
    },
    plotOptions: {
        pie: {
            donut: {
                size: "65%",
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: "Total",
                        formatter: () => totalTickets.value.toString(),
                    },
                },
            },
        },
    },
}));
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <section class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
            <p class="text-sm text-muted-foreground">
                Ringkasan aktivitas dan status permohonan geospasial BPKH
                Wilayah IX
            </p>
        </section>

        <hr class="border-muted" />

        <!-- Status Cards -->
        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <Card
                v-for="(count, status) in props.distribution"
                :key="status"
                class="rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200"
            >
                <CardContent class="flex items-center justify-between p-5">
                    <!-- Text -->
                    <div class="space-y-1 min-w-0 flex-1 mr-3">
                        <p
                            class="text-xs font-medium text-muted-foreground leading-tight truncate"
                            :title="String(status)"
                        >
                            {{ status }}
                        </p>
                        <p class="text-2xl font-bold tracking-tight">
                            {{ count }}
                        </p>
                    </div>

                    <!-- Icon -->
                    <div
                        class="w-11 h-11 rounded-lg flex items-center justify-center shrink-0"
                        :style="{
                            backgroundColor: STATUS_COLORS[String(status)]
                                ? STATUS_COLORS[String(status)] + '18'
                                : '#f1f5f9',
                            color: STATUS_COLORS[String(status)] ?? '#64748b',
                        }"
                    >
                        <component
                            :is="STATUS_ICONS[String(status)]"
                            v-if="STATUS_ICONS[String(status)]"
                            class="w-5 h-5"
                            :stroke-width="2"
                        />
                        <Send v-else class="w-5 h-5" />
                    </div>
                </CardContent>
            </Card>
        </section>

        <!-- Konten bawah -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Tabel Permohonan -->
            <Card class="xl:col-span-2 rounded-xl shadow-sm">
                <CardHeader>
                    <CardTitle class="text-base">
                        Permohonan Terbaru
                    </CardTitle>
                </CardHeader>

                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/50">
                                <tr>
                                    <th class="px-5 py-3 text-left font-medium">
                                        Kode Tiket
                                    </th>
                                    <th class="px-5 py-3 text-left font-medium">
                                        Pemohon
                                    </th>
                                    <th class="px-5 py-3 text-left font-medium">
                                        Status
                                    </th>
                                    <th class="px-5 py-3 text-left font-medium">
                                        Penugasan
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="ticket in props.tickets || []"
                                    :key="ticket.ticket_code"
                                    class="border-b hover:bg-muted/40 transition"
                                >
                                    <td class="px-5 py-4 font-medium">
                                        #{{ ticket.ticket_code }}
                                    </td>
                                    <td class="px-5 py-4">
                                        {{ ticket.name }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <Badge
                                            class="text-xs font-medium"
                                            :style="{
                                                padding: '0.25rem 0.75rem',
                                                backgroundColor:
                                                    (STATUS_COLORS[
                                                        ticket.status
                                                    ] ?? '#94a3b8') + '18',
                                                color:
                                                    STATUS_COLORS[
                                                        ticket.status
                                                    ] ?? '#64748b',
                                                border:
                                                    '1px solid ' +
                                                    (STATUS_COLORS[
                                                        ticket.status
                                                    ] ?? '#94a3b8') +
                                                    '30',
                                            }"
                                        >
                                            {{ ticket.status }}
                                        </Badge>
                                    </td>
                                    <td class="px-5 py-4 text-muted-foreground">
                                        {{ ticket.current_assignment ?? "-" }}
                                    </td>
                                </tr>

                                <tr v-if="!props.tickets?.length">
                                    <td
                                        colspan="4"
                                        class="px-5 py-6 text-center text-muted-foreground"
                                    >
                                        Belum ada permohonan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Pie Chart Distribusi -->
            <Card class="rounded-xl shadow-sm">
                <CardHeader>
                    <CardTitle class="text-base"> Distribusi Status </CardTitle>
                </CardHeader>

                <CardContent class="flex flex-col items-center gap-2">
                    <VueApexCharts
                        v-if="totalTickets > 0"
                        type="donut"
                        height="260"
                        :options="chartOptions"
                        :series="chartSeries"
                    />

                    <div
                        v-else
                        class="flex items-center justify-center h-[260px] text-muted-foreground text-sm"
                    >
                        Belum ada data
                    </div>

                    <p class="text-xs text-muted-foreground">
                        Total permohonan berdasarkan status
                    </p>
                </CardContent>
            </Card>
        </section>
    </div>
</template>
