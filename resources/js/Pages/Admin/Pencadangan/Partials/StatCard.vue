<script setup>
defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: {
        type: String,
        default: "blue",
    },
});

const colorClasses = {
    blue: {
        bg: "bg-blue-50 dark:bg-blue-950/30",
        icon: "bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400",
        text: "text-blue-900 dark:text-blue-100",
    },
    indigo: {
        bg: "bg-indigo-50 dark:bg-indigo-950/30",
        icon: "bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400",
        text: "text-indigo-900 dark:text-indigo-100",
    },
    emerald: {
        bg: "bg-emerald-50 dark:bg-emerald-950/30",
        icon: "bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400",
        text: "text-emerald-900 dark:text-emerald-100",
    },
    red: {
        bg: "bg-red-50 dark:bg-red-950/30",
        icon: "bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400",
        text: "text-red-900 dark:text-red-100",
    },
    gray: {
        bg: "bg-gray-50 dark:bg-gray-800/50",
        icon: "bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400",
        text: "text-gray-900 dark:text-gray-100",
    },
};
</script>

<template>
    <div
        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
    >
        <div class="flex items-center gap-4">
            <div
                :class="[
                    'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                    colorClasses[color]?.icon,
                ]"
            >
                <!-- Archive -->
                <svg
                    v-if="icon === 'archive'"
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                    />
                </svg>
                <!-- Database -->
                <svg
                    v-else-if="icon === 'database'"
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"
                    />
                </svg>
                <!-- Calendar -->
                <svg
                    v-else-if="icon === 'calendar'"
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>
                <!-- Alert -->
                <svg
                    v-else-if="icon === 'alert'"
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"
                    />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                    {{ title }}
                </p>
                <p
                    :class="[
                        'mt-1 text-2xl font-bold',
                        colorClasses[color]?.text,
                    ]"
                >
                    {{ value ?? 0 }}
                </p>
            </div>
        </div>
    </div>
</template>
