<!-- filepath: d:\TA\project-manajemen-permohonan-bpkh\permohonan-data-bpkh\resources\js\Pages\Feedback\Chart.vue -->
<script setup>
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    subtitle: {
        type: String,
        default: "",
    },
    items: {
        type: Array,
        default: () => [],
    },
    color: {
        type: String,
        default: "emerald",
    },
});

const colorClass = (color) => {
    const colors = {
        emerald: "bg-emerald-500",
        sky: "bg-sky-500",
        amber: "bg-amber-500",
    };

    return colors[color] ?? colors.emerald;
};
</script>

<template>
    <div
        class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6"
    >
        <div class="mb-5">
            <h2 class="text-lg font-semibold text-slate-900">{{ title }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ subtitle }}</p>
        </div>

        <div class="space-y-4">
            <div v-for="item in items" :key="item.key" class="space-y-2">
                <div class="flex items-center justify-between gap-3 text-sm">
                    <span class="font-medium text-slate-700">{{
                        item.label
                    }}</span>
                    <span class="text-slate-500"
                        >{{ item.value }} ({{ item.percentage }}%)</span
                    >
                </div>

                <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                    <div
                        :class="[
                            'h-full rounded-full transition-all duration-500',
                            colorClass(color),
                        ]"
                        :style="{ width: `${item.percentage}%` }"
                    ></div>
                </div>
            </div>

            <div
                v-if="!items.length"
                class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500"
            >
                Belum ada data feedback.
            </div>
        </div>
    </div>
</template>
