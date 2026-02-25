<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import ContentBeranda from "@/Pages/Admin/Beranda/ContentBeranda.vue";

defineProps<{
    distribution: {
        label: string;
        value: string;
        color: string;
        icon: string;
        count: number;
    }[];
    tickets: {
        ticket_code: string;
        name: string;
        status: string;
        status_color: string;
        status_icon: string;
        current_assignment: string;
    }[];
}>();
</script>

<template>
    <LayoutDashboard title="Dashboard">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <ContentBeranda :distribution="distribution" :tickets="tickets" />
    </LayoutDashboard>
</template>
