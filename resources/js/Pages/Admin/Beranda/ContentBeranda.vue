<script setup lang="ts">
// filepath: d:\TA\project-manajemen-permohonan-bpkh\permohonan-data-bpkh\resources\js\Pages\Admin\Beranda\ContentBeranda.vue
import { computed, type Component } from "vue";
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
   Types
======================== */
interface StatusDistribution {
    label: string;
    value: string;
    color: string;
    icon: string;
    count: number;
}

interface Ticket {
    ticket_code: string;
    name: string;
    status: string;
    status_color: string;
    status_icon: string;
    current_assignment?: string;
}

interface Props {
    distribution: StatusDistribution[];
    tickets?: Ticket[];
}

const props = defineProps<Props>();

/* ========================
   Icon name → Component mapping
   (Satu-satunya mapping yang dibutuhkan di frontend)
======================== */
const ICON_MAP: Record<string, Component> = {
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
};

function resolveIcon(iconName: string): Component {
    return ICON_MAP[iconName] ?? Send;
}

/* ========================
   Chart Computed
======================== */
const chartSeries = computed(() => props.distribution.map((d) => d.count));

const chartLabels = computed(() => props.distribution.map((d) => d.label));

const chartColors = computed(() => props.distribution.map((d) => d.color));

const totalTickets = computed(() =>
    chartSeries.value.reduce((a, b) => a + b, 0),
);

const chartOptions = computed<ApexOptions>(() => ({
    chart: { type: "donut" },
    labels: chartLabels.value,
    colors: chartColors.value,
    legend: { position: "bottom", fontSize: "13px" },
    dataLabels: { enabled: true },
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

        <!-- Status Cards — warna & icon langsung dari backend -->
        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <Card
                v-for="item in distribution"
                :key="item.value"
                class="rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200"
            >
                <CardContent class="flex items-center justify-between p-5">
                    <!-- Text -->
                    <div class="space-y-1 min-w-0 flex-1 mr-3">
                        <p
                            class="text-xs font-medium text-muted-foreground leading-tight truncate"
                            :title="item.label"
                        >
                            {{ item.label }}
                        </p>
                        <p class="text-2xl font-bold tracking-tight">
                            {{ item.count }}
                        </p>
                    </div>

                    <!-- Icon -->
                    <div
                        class="w-11 h-11 rounded-lg flex items-center justify-center shrink-0"
                        :style="{
                            backgroundColor: item.color + '18',
                            color: item.color,
                        }"
                    >
                        <component
                            :is="resolveIcon(item.icon)"
                            class="w-5 h-5"
                            :stroke-width="2"
                        />
                    </div>
                </CardContent>
            </Card>
        </section>

        <!-- Konten bawah -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Tabel Permohonan Terbaru -->
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
                                    v-for="ticket in props.tickets ?? []"
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
                                                    ticket.status_color + '18',
                                                color: ticket.status_color,
                                                border:
                                                    '1px solid ' +
                                                    ticket.status_color +
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
