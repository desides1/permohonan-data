<script setup>
import { router } from "@inertiajs/vue3";

defineProps({
    histories: Object,
});

const statusStyles = {
    completed:
        "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300",
    in_progress:
        "bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300",
    failed: "bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300",
};

const typeStyles = {
    manual: "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300",
    scheduled:
        "bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300",
};

function goToPage(url) {
    if (url) {
        router.visit(url, { preserveScroll: true });
    }
}
</script>

<template>
    <div
        class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
    >
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Riwayat Pencadangan
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Daftar seluruh pencadangan data yang telah dilakukan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead
                    class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 dark:bg-gray-900/50 dark:text-gray-400"
                >
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama File</th>
                        <th class="px-6 py-4 font-semibold">Ukuran</th>
                        <th class="px-6 py-4 font-semibold">Tipe</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Dibuat Oleh</th>
                        <th class="px-6 py-4 font-semibold">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr
                        v-for="history in histories.data"
                        :key="history.id"
                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-900/30"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30"
                                >
                                    <svg
                                        class="h-4 w-4 text-blue-600 dark:text-blue-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="max-w-[200px] truncate font-medium text-gray-900 dark:text-white"
                                    :title="history.filename"
                                >
                                    {{ history.filename }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ history.formatted_size }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                :class="[
                                    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                    typeStyles[history.type],
                                ]"
                            >
                                {{ history.type_label }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span
                                    :class="[
                                        'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium',
                                        statusStyles[history.status],
                                    ]"
                                >
                                    <!-- Loading dot for in_progress -->
                                    <span
                                        v-if="history.status === 'in_progress'"
                                        class="h-1.5 w-1.5 animate-pulse rounded-full bg-current"
                                    />
                                    <!-- Check for completed -->
                                    <svg
                                        v-else-if="
                                            history.status === 'completed'
                                        "
                                        class="h-3 w-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                    <!-- X for failed -->
                                    <svg
                                        v-else
                                        class="h-3 w-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                    {{ history.status_label }}
                                </span>
                            </div>
                            <!-- Error tooltip -->
                            <p
                                v-if="history.error_message"
                                class="mt-1 max-w-[200px] truncate text-xs text-red-500"
                                :title="history.error_message"
                            >
                                {{ history.error_message }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    {{
                                        history.creator
                                            ?.charAt(0)
                                            ?.toUpperCase() ?? "S"
                                    }}
                                </div>
                                <span
                                    class="text-gray-700 dark:text-gray-300"
                                    >{{ history.creator }}</span
                                >
                            </div>
                        </td>

                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            {{ history.created_at }}
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-if="!histories.data?.length">
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700"
                                >
                                    <svg
                                        class="h-8 w-8 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                                        />
                                    </svg>
                                </div>
                                <p
                                    class="mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Belum ada data cadangan
                                </p>
                                <p
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Klik tombol "Cadangkan Sekarang" untuk
                                    membuat cadangan pertama.
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="histories.links?.length > 3"
            class="border-t border-gray-200 px-6 py-4 dark:border-gray-700"
        >
            <div
                class="flex flex-col items-center justify-between gap-3 sm:flex-row"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan
                    <span class="font-medium">{{ histories.from }}</span> -
                    <span class="font-medium">{{ histories.to }}</span> dari
                    <span class="font-medium">{{ histories.total }}</span> data
                </p>
                <nav class="flex gap-1">
                    <button
                        v-for="link in histories.links"
                        :key="link.label"
                        @click="goToPage(link.url)"
                        :disabled="!link.url"
                        :class="[
                            'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                            link.active
                                ? 'bg-blue-600 text-white'
                                : link.url
                                  ? 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'
                                  : 'cursor-not-allowed text-gray-300 dark:text-gray-600',
                        ]"
                        v-html="link.label"
                    />
                </nav>
            </div>
        </div>
    </div>
</template>
