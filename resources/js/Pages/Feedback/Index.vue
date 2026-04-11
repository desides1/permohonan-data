<!-- filepath: d:\TA\project-manajemen-permohonan-bpkh\permohonan-data-bpkh\resources\js\Pages\Feedback\index.vue -->
<script setup>
import { Head, router } from "@inertiajs/vue3";
import { reactive, computed } from "vue";
import Chart from "./Chart.vue";
import Table from "./Table.vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    charts: {
        type: Object,
        default: () => ({}),
    },
    feedbacks: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
});

const filter = reactive({
    search: props.filters.search ?? "",
});

const submitFilter = () => {
    router.get(
        route("admin.feedback.index"),
        { search: filter.search },
        { preserveState: true, replace: true },
    );
};

const resetFilter = () => {
    filter.search = "";
    submitFilter();
};

const summaryCards = computed(() => [
    {
        title: "Total Feedback",
        value: props.summary.total_feedback ?? 0,
        tone: "from-emerald-600 to-emerald-500",
    },
    {
        title: "Puas / Sangat Puas",
        value: props.summary.positive_satisfaction ?? 0,
        tone: "from-sky-600 to-cyan-500",
    },
    {
        title: "Indikasi Pungli",
        value: props.summary.illegal_fee_reports ?? 0,
        tone: "from-amber-500 to-orange-500",
    },
    {
        title: "Ada Saran",
        value: props.summary.with_suggestions ?? 0,
        tone: "from-violet-600 to-fuchsia-500",
    },
]);
</script>

<template>
    <LayoutDashboard>
        <Head title="Survei Kepuasan" />

        <div class="space-y-6 p-4 sm:p-6">
            <div
                class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6"
            >
                <div
                    class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
                >
                    <div>
                        <p
                            class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700"
                        >
                            Survei Kepuasan
                        </p>
                        <h1
                            class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl"
                        >
                            Monitoring Feedback Layanan
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm text-slate-600">
                            Ringkasan hasil survei kepuasan masyarakat terhadap
                            layanan permohonan data.
                        </p>
                    </div>

                    <form
                        @submit.prevent="submitFilter"
                        class="flex flex-col gap-3 sm:flex-row"
                    >
                        <input
                            v-model="filter.search"
                            type="text"
                            placeholder="Cari kode tiket..."
                            class="h-11 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-emerald-500 sm:w-72"
                        />
                        <button
                            type="submit"
                            class="h-11 rounded-2xl bg-emerald-700 px-5 text-sm font-medium text-white transition hover:bg-emerald-800"
                        >
                            Cari
                        </button>
                        <button
                            type="button"
                            class="h-11 rounded-2xl border border-slate-200 px-5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                            @click="resetFilter"
                        >
                            Reset
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in summaryCards"
                    :key="card.title"
                    class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200"
                >
                    <div :class="['h-2 bg-gradient-to-r', card.tone]"></div>
                    <div class="p-5">
                        <p class="text-sm text-slate-500">{{ card.title }}</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ card.value }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                <Chart
                    title="Kemudahan Penggunaan"
                    subtitle="Distribusi jawaban kemudahan penggunaan layanan"
                    :items="charts.service_usability ?? []"
                    color="emerald"
                />
                <Chart
                    title="Kepuasan Layanan"
                    subtitle="Distribusi tingkat kepuasan masyarakat"
                    :items="charts.service_satisfaction ?? []"
                    color="sky"
                />
                <Chart
                    title="Indikasi Biaya Tidak Resmi"
                    subtitle="Distribusi jawaban terkait pungutan liar"
                    :items="charts.illegal_fee_indication ?? []"
                    color="amber"
                />
            </div>

            <Table :feedbacks="feedbacks" />
        </div>
    </LayoutDashboard>
</template>
