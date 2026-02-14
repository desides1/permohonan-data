<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import Table from "./Table.vue";
import Filters from "./Filters.vue";

defineProps({
    tickets: Array,
    filters: Object,
});
</script>

<template>
    <LayoutDashboard>
        <section class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">
                    Data Permohonan
                </h1>
                <p class="text-sm text-slate-500">
                    Kelola dan monitor seluruh permohonan perizinan
                </p>
            </div>

            <!-- Filter -->
            <Filters :filters="filters" />
            <!-- Table -->
            <Table :tickets="tickets" />
        </section>
    </LayoutDashboard>
</template>
