<script setup>
import { computed } from "vue";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    actionType: {
        type: String,
        required: true, // 'approve' | 'reject'
    },
    rejectReason: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["close", "update:rejectReason", "confirm"]);

const isRejectInvalid = computed(
    () => props.actionType === "reject" && props.rejectReason.length < 10,
);
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
    >
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
            <h2 class="text-base font-semibold text-gray-800">
                Konfirmasi Tindakan
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Apakah Anda yakin ingin
                <strong>
                    {{
                        actionType === "approve"
                            ? "menyetujui"
                            : actionType === "verify"
                              ? "memverifikasi"
                              : actionType === "reject"
                                ? "menolak"
                                : ""
                    }}
                </strong>
                permohonan ini?
            </p>

            <div v-if="actionType === 'reject'" class="mt-4">
                <label class="text-sm font-medium text-gray-700">
                    Alasan Penolakan
                </label>

                <textarea
                    :value="rejectReason"
                    @input="emit('update:rejectReason', $event.target.value)"
                    rows="3"
                    minlength="10"
                    class="mt-1 w-full rounded-md border px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500"
                    placeholder="Minimal 10 karakter"
                />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button
                    @click="emit('close')"
                    class="rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
                >
                    Batal
                </button>

                <button
                    @click="emit('confirm')"
                    :disabled="isRejectInvalid"
                    class="rounded-md px-4 py-2 text-sm font-medium text-white"
                    :class="
                        actionType === 'verify'
                            ? 'bg-yellow-700 hover:bg-yellow-800 disabled:opacity-50'
                            : actionType === 'approve'
                              ? 'bg-green-700 hover:bg-green-800'
                              : actionType === 'reject'
                                ? 'bg-red-700 hover:bg-red-800 disabled:opacity-50'
                                : 'bg-yellow-700 hover:bg-yellow-800 disabled:opacity-50'
                    "
                >
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</template>
