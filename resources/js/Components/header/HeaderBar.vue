<script setup>
import { defineProps } from "vue";
import NotificationButton from "./NotificationButton.vue";
import ProfileDropdown from "./ProfileDropdown.vue";

defineProps(
    {
        adminName: String,
        notificationsCount: Number,
    },
    (components = {
        NotificationButton,
        ProfileDropdown,
    }),
);

defineEmits(["open-sidebar"]);
</script>

<template>
    <header
        class="h-16 flex items-center justify-between px-6 bg-white shadow-sm"
    >
        <button class="md:hidden p-2 rounded-md" @click="$emit('open-sidebar')">
            <svg
                class="w-6 h-6 text-slate-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                ></path>
            </svg>
        </button>

        <h1 class="text-lg font-semibold">
            <slot name="title">Dashboard</slot>
        </h1>

        <div class="flex items-center gap-3">
            <NotificationButton :count="notificationsCount" />
            <ProfileDropdown :admin-name="adminName" />
        </div>
    </header>
</template>
