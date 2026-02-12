<script setup lang="ts">
import { computed } from "vue";
import VueApexCharts from "vue3-apexcharts";
import type { ApexOptions } from "apexcharts";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import { FileText, Loader, CheckCircle, XCircle } from "lucide-vue-next";

/* ========================
   Props dari Inertia
======================== */
interface Props {
    distribution: Record<string, number>;
    tickets?: {
        ticket_code: string;
        name: string;
        status: string;
    }[];
}

const props = defineProps<Props>();

/* ========================
   Status → Color Mapping
======================== */
const STATUS_COLORS: Record<string, string> = {
    Baru: "#3b82f6", // blue-500
    Proses: "#facc15", // yellow-400
    Selesai: "#22c55e", // green-500
    Tolak: "#ef4444", // red-500
};

const STATUS_ICONS: Record<string, any> = {
    Baru: FileText,
    Proses: Loader,
    Selesai: CheckCircle,
    Tolak: XCircle,
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
            <h1 class="text-2xl font-bold tracking-tight">Dashboard Admin</h1>
            <p class="text-sm text-muted-foreground">
                Ringkasan aktivitas dan status permohonan geospasial BPKH
                Wilayah IX
            </p>
        </section>

        <hr class="border-muted" />

        <!-- kontent tiket diterima, tiket proses, tiket selesai, total tiket in card with icon -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <Card
                v-for="(count, status) in props.distribution"
                :key="status"
                class="rounded-xl shadow-sm hover:shadow-md transition"
            >
                <CardContent class="flex items-center justify-between p-5">
                    <!-- Text -->
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">
                            {{ status }}
                        </p>
                        <p class="text-2xl font-bold">
                            {{ count }}
                        </p>
                    </div>

                    <!-- Icon -->
                    <div
                        class="w-12 h-12 rounded-lg flex items-center justify-center"
                        :style="{
                            backgroundColor: STATUS_COLORS[status]
                                ? STATUS_COLORS[status] + '22'
                                : '#e5e7eb',
                            color: STATUS_COLORS[status] ?? '#64748b',
                        }"
                    >
                        <component :is="STATUS_ICONS[status]" class="w-6 h-6" />
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
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="ticket in props.tickets || []"
                                    :key="ticket.ticket_code"
                                    class="border-b hover:bg-muted/40 transition"
                                >
                                    <td class="px-5 py-4 font-medium">
                                        {{ ticket.ticket_code }}
                                    </td>
                                    <td class="px-5 py-4">
                                        {{ ticket.name }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <Badge
                                            :style="{
                                                backgroundColor:
                                                    STATUS_COLORS[
                                                        ticket.status
                                                    ] + '22',
                                                color: STATUS_COLORS[
                                                    ticket.status
                                                ],
                                            }"
                                        >
                                            {{ ticket.status }}
                                        </Badge>
                                    </td>
                                </tr>

                                <tr v-if="!props.tickets?.length">
                                    <td
                                        colspan="3"
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
                        type="donut"
                        height="260"
                        :options="chartOptions"
                        :series="chartSeries"
                    />

                    <p class="text-xs text-muted-foreground">
                        Total permohonan berdasarkan status
                    </p>
                </CardContent>
            </Card>
        </section>
    </div>
</template>
