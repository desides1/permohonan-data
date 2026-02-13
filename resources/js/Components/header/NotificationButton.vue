<template>
    <button
        class="relative p-2 rounded-md hover:bg-slate-100"
        aria-label="Notifications"
    >
        <svg class="w-5 h-5 text-slate-600" viewBox="0 0 24 24" fill="none">
            <path
                d="M15 17H9"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
            />
            <path
                d="M12 3v1"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
            />
            <path
                d="M18 8a6 6 0 10-12 0v4l-1 1v1h16v-1l-1-1V8"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>

        <!-- Badge -->
        <span
            v-if="count > 0"
            class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs px-2 py-0.5 rounded-full"
        >
            {{ count }}
        </span>
    </button>
</template>

<script>
export default {
    name: "NotificationButton",
    props: {
        count: {
            type: Number,
            default: 0,
        },
    },
};
</script>
