<!-- filepath: d:\TA\project-manajemen-permohonan-bpkh\permohonan-data-bpkh\resources\js\Pages\Feedback\table.vue -->
<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    feedbacks: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
});

const badgeClass = (value, type) => {
    if (type === "satisfaction") {
        if (["sangat_puas", "puas"].includes(value)) {
            return "bg-emerald-50 text-emerald-700 ring-emerald-200";
        }

        if (value === "cukup_puas") {
            return "bg-amber-50 text-amber-700 ring-amber-200";
        }

        return "bg-rose-50 text-rose-700 ring-rose-200";
    }

    if (type === "illegal_fee") {
        if (value === "ada") {
            return "bg-rose-50 text-rose-700 ring-rose-200";
        }

        if (value === "tidak_tahu") {
            return "bg-amber-50 text-amber-700 ring-amber-200";
        }

        return "bg-emerald-50 text-emerald-700 ring-emerald-200";
    }

    if (type === "usability") {
        if (value === "sangat_mudah" || value === "mudah") {
            return "bg-emerald-50 text-emerald-700 ring-emerald-200";
        }

        if (value === "cukup") {
            return "bg-amber-50 text-amber-700 ring-amber-200";
        }

        return "bg-rose-50 text-rose-700 ring-rose-200";
    }

    return "bg-slate-50 text-slate-700 ring-slate-200";
};
</script>

<template>
    <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900">
                Detail Feedback
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Menampilkan jawaban survei beserta saran yang diberikan
                pengguna.
            </p>
        </div>

        <div class="block xl:hidden">
            <div v-if="feedbacks.data?.length" class="space-y-4 p-4 sm:p-6">
                <div
                    v-for="item in feedbacks.data"
                    :key="item.id"
                    class="rounded-2xl border border-slate-200 p-4"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                {{ item.ticket_code }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                {{ item.created_at }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3 text-sm">
                        <div>
                            <div class="mb-1 text-slate-500">Kemudahan</div>
                            <span
                                :class="[
                                    'inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1',
                                    badgeClass(
                                        item.service_usability_raw,
                                        'usability',
                                    ),
                                ]"
                            >
                                {{ item.service_usability }}
                            </span>
                        </div>

                        <div>
                            <div class="mb-1 text-slate-500">Kepuasan</div>
                            <span
                                :class="[
                                    'inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1',
                                    badgeClass(
                                        item.service_satisfaction_raw,
                                        'satisfaction',
                                    ),
                                ]"
                            >
                                {{ item.service_satisfaction }}
                            </span>
                        </div>

                        <div>
                            <div class="mb-1 text-slate-500">
                                Indikasi Pungli
                            </div>
                            <span
                                :class="[
                                    'inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1',
                                    badgeClass(
                                        item.illegal_fee_indication_raw,
                                        'illegal_fee',
                                    ),
                                ]"
                            >
                                {{ item.illegal_fee_indication }}
                            </span>
                        </div>

                        <div>
                            <div class="mb-1 text-slate-500">Saran</div>
                            <p
                                class="rounded-xl bg-slate-50 px-3 py-2 text-slate-700"
                            >
                                {{ item.suggestions || "-" }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden overflow-x-auto xl:block">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Kode Tiket</th>
                        <th class="px-6 py-4 font-semibold">Kemudahan</th>
                        <th class="px-6 py-4 font-semibold">Kepuasan</th>
                        <th class="px-6 py-4 font-semibold">Indikasi Pungli</th>
                        <th class="px-6 py-4 font-semibold">Saran</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in feedbacks.data"
                        :key="item.id"
                        class="border-t border-slate-100 align-top"
                    >
                        <td class="px-6 py-4 font-medium text-slate-900">
                            {{ item.ticket_code }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="[
                                    'inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1',
                                    badgeClass(
                                        item.service_usability_raw,
                                        'usability',
                                    ),
                                ]"
                            >
                                {{ item.service_usability }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="[
                                    'inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1',
                                    badgeClass(
                                        item.service_satisfaction_raw,
                                        'satisfaction',
                                    ),
                                ]"
                            >
                                {{ item.service_satisfaction }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="[
                                    'inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1',
                                    badgeClass(
                                        item.illegal_fee_indication_raw,
                                        'illegal_fee',
                                    ),
                                ]"
                            >
                                {{ item.illegal_fee_indication }}
                            </span>
                        </td>
                        <td class="max-w-sm px-6 py-4 text-slate-600">
                            {{ item.suggestions || "-" }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ item.created_at }}
                        </td>
                    </tr>

                    <tr v-if="!feedbacks.data?.length">
                        <td
                            colspan="6"
                            class="px-6 py-10 text-center text-slate-500"
                        >
                            Belum ada feedback.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="feedbacks.links?.length"
            class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 p-4 sm:p-6"
        >
            <Link
                v-for="link in feedbacks.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                    'rounded-xl px-3 py-2 text-sm transition',
                    link.active
                        ? 'bg-emerald-700 text-white'
                        : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
                    !link.url ? 'pointer-events-none opacity-50' : '',
                ]"
                v-html="link.label"
            />
        </div>
    </div>
</template>
