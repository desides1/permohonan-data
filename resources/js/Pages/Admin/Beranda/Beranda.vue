<script setup lang="ts">
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
    recentActivities: {
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

        <ContentBeranda
            :distribution="distribution"
            :recent-activities="recentActivities"
        />
    </LayoutDashboard>
</template>
