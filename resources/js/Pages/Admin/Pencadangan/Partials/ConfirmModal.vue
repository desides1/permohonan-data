<script setup>
defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmText: {
        type: String,
        default: "Konfirmasi",
    },
    confirmColor: {
        type: String,
        default: "blue",
    },
});

const emit = defineEmits(["confirm", "cancel"]);

const colorMap = {
    blue: "bg-blue-600 hover:bg-blue-700 focus:ring-blue-500",
    red: "bg-red-600 hover:bg-red-700 focus:ring-red-500",
    amber: "bg-amber-600 hover:bg-amber-700 focus:ring-amber-500",
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4"
            >
                <div
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                    @click="$emit('cancel')"
                />

                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="show"
                        class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800"
                    >
                        <div class="flex flex-col items-center text-center">
                            <!-- Icon -->
                            <div
                                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/50"
                            >
                                <svg
                                    class="h-7 w-7 text-blue-600 dark:text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>

                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                {{ title }}
                            </h3>
                            <p
                                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ message }}
                            </p>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button
                                @click="$emit('cancel')"
                                class="flex-1 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                Batal
                            </button>
                            <button
                                @click="$emit('confirm')"
                                :class="[
                                    'flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all focus:ring-2 focus:ring-offset-2',
                                    colorMap[confirmColor],
                                ]"
                            >
                                {{ confirmText }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
