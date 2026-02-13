<script setup>
import { ref, provide } from "vue";

import Sidebar from "@/Components/Sidebar/Sidebar.vue";
import HeaderBar from "@/Components/header/HeaderBar.vue";

defineProps({
    adminName: String,
    notificationsCount: Number,
});

const sidebarOpen = ref(false);

const openSidebar = () => {
    sidebarOpen.value = true;
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};

/* supaya Sidebar bisa menutup dirinya sendiri (mobile) */
provide("closeSidebar", closeSidebar);
</script>

<template>
    <div class="min-h-screen flex bg-slate-50 text-slate-800">
        <!-- OVERLAY (MOBILE) -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 bg-black/40 z-40 md:hidden"
            @click="closeSidebar"
        />

        <!-- SIDEBAR MOBILE -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar border-r border-sidebar-border transform transition-transform duration-300 md:hidden"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <Sidebar />
        </aside>

        <!-- SIDEBAR DESKTOP -->
        <aside
            class="hidden md:block w-56 bg-sidebar border-r border-sidebar-border"
        >
            <Sidebar />
        </aside>

        <div class="flex-1 flex flex-col">
            <HeaderBar
                :admin-name="adminName"
                :notifications-count="notificationsCount"
                @open-sidebar="openSidebar"
            >
                <template #title>
                    <slot name="title" />
                </template>
            </HeaderBar>

            <main class="flex-1 overflow-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
