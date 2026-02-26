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
    ArrowRight,
    Clock,
    User,
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

interface RecentActivity {
    id: number;
    ticket_code: string;
    action: string | null;
    description: string;
    from_status: string | null;
    from_status_label: string | null;
    from_status_color: string | null;
    to_status: string | null;
    to_status_label: string | null;
    to_status_color: string | null;
    causer: string | null;
    created_at: string;
    time_ago: string;
}

interface Props {
    distribution: StatusDistribution[];
    recentActivities?: RecentActivity[];
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
            <!-- Riwayat Aktivitas Terakhir -->
            <Card class="xl:col-span-2 rounded-xl shadow-sm">
                <CardHeader>
                    <CardTitle class="text-base">
                        Riwayat Aktivitas Terakhir
                    </CardTitle>
                </CardHeader>

                <CardContent class="p-0">
                    <!-- Desktop: Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Kode Tiket
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Perubahan Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Oleh
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Waktu
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="act in props.recentActivities ?? []"
                                    :key="act.id"
                                    class="border-b last:border-b-0 hover:bg-muted/40 transition-colors"
                                >
                                    <!-- Kode Tiket -->
                                    <td class="px-4 py-3">
                                        <span
                                            class="font-semibold text-foreground"
                                        >
                                            #{{ act.ticket_code }}
                                        </span>
                                    </td>

                                    <!-- Perubahan Status -->
                                    <td class="px-4 py-3">
                                        <div
                                            class="flex items-center gap-2 flex-wrap"
                                        >
                                            <!-- From -->
                                            <Badge
                                                v-if="act.from_status_label"
                                                class="text-[11px] font-medium whitespace-nowrap"
                                                :style="{
                                                    padding: '0.15rem 0.5rem',
                                                    backgroundColor:
                                                        (act.from_status_color ??
                                                            '#94a3b8') + '18',
                                                    color:
                                                        act.from_status_color ??
                                                        '#64748b',
                                                    border:
                                                        '1px solid ' +
                                                        (act.from_status_color ??
                                                            '#94a3b8') +
                                                        '30',
                                                }"
                                            >
                                                {{ act.from_status_label }}
                                            </Badge>

                                            <!-- Arrow -->
                                            <ArrowRight
                                                v-if="
                                                    act.from_status_label &&
                                                    act.to_status_label
                                                "
                                                class="w-3.5 h-3.5 text-muted-foreground shrink-0"
                                            />

                                            <!-- To -->
                                            <Badge
                                                v-if="act.to_status_label"
                                                class="text-[11px] font-medium whitespace-nowrap"
                                                :style="{
                                                    padding: '0.15rem 0.5rem',
                                                    backgroundColor:
                                                        (act.to_status_color ??
                                                            '#94a3b8') + '18',
                                                    color:
                                                        act.to_status_color ??
                                                        '#64748b',
                                                    border:
                                                        '1px solid ' +
                                                        (act.to_status_color ??
                                                            '#94a3b8') +
                                                        '30',
                                                }"
                                            >
                                                {{ act.to_status_label }}
                                            </Badge>
                                        </div>
                                    </td>

                                    <!-- Oleh -->
                                    <td class="px-4 py-3 text-muted-foreground">
                                        {{ act.causer }}
                                    </td>

                                    <!-- Waktu -->
                                    <td class="px-4 py-3">
                                        <span
                                            class="text-muted-foreground text-xs"
                                            :title="act.created_at"
                                        >
                                            {{ act.time_ago }}
                                        </span>
                                    </td>
                                </tr>

                                <tr v-if="!props.recentActivities?.length">
                                    <td
                                        colspan="4"
                                        class="px-5 py-8 text-center text-muted-foreground"
                                    >
                                        Belum ada riwayat aktivitas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile: Card List -->
                    <div class="md:hidden divide-y">
                        <div
                            v-for="act in props.recentActivities ?? []"
                            :key="act.id"
                            class="px-4 py-3 space-y-2 hover:bg-muted/40 transition-colors"
                        >
                            <!-- Header: Tiket + Waktu -->
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-semibold text-sm text-foreground"
                                >
                                    #{{ act.ticket_code }}
                                </span>
                                <span
                                    class="text-[11px] text-muted-foreground flex items-center gap-1"
                                    :title="act.created_at"
                                >
                                    <Clock class="w-3 h-3" />
                                    {{ act.time_ago }}
                                </span>
                            </div>

                            <!-- Perubahan Status -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <Badge
                                    v-if="act.from_status_label"
                                    class="text-[11px] font-medium"
                                    :style="{
                                        padding: '0.15rem 0.5rem',
                                        backgroundColor:
                                            (act.from_status_color ??
                                                '#94a3b8') + '18',
                                        color:
                                            act.from_status_color ?? '#64748b',
                                        border:
                                            '1px solid ' +
                                            (act.from_status_color ??
                                                '#94a3b8') +
                                            '30',
                                    }"
                                >
                                    {{ act.from_status_label }}
                                </Badge>

                                <ArrowRight
                                    v-if="
                                        act.from_status_label &&
                                        act.to_status_label
                                    "
                                    class="w-3 h-3 text-muted-foreground shrink-0"
                                />

                                <Badge
                                    v-if="act.to_status_label"
                                    class="text-[11px] font-medium"
                                    :style="{
                                        padding: '0.15rem 0.5rem',
                                        backgroundColor:
                                            (act.to_status_color ?? '#94a3b8') +
                                            '18',
                                        color: act.to_status_color ?? '#64748b',
                                        border:
                                            '1px solid ' +
                                            (act.to_status_color ?? '#94a3b8') +
                                            '30',
                                    }"
                                >
                                    {{ act.to_status_label }}
                                </Badge>
                            </div>

                            <!-- Oleh -->
                            <div
                                class="flex items-center gap-1.5 text-xs text-muted-foreground"
                            >
                                <User class="w-3 h-3" />
                                {{ act.causer }}
                            </div>
                        </div>

                        <div
                            v-if="!props.recentActivities?.length"
                            class="px-4 py-8 text-center text-muted-foreground text-sm"
                        >
                            Belum ada riwayat aktivitas
                        </div>
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
