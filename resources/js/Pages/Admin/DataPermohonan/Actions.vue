<script setup>
defineProps({
    ticket: Object,
});
</script>

<template>
    <div class="flex items-center justify-center gap-2">
        <button
            class="rounded-lg border border-slate-300 p-2 text-slate-600 hover:bg-slate-100"
            title="Detail"
        >
            👁
        </button>

        <button
            class="rounded-lg border border-emerald-600 p-2 text-emerald-600 hover:bg-emerald-50"
            title="Disposisi"
        >
            ✔
        </button>
    </div>
</template>
