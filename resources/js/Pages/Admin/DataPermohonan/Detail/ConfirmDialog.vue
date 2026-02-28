<script setup>
import { computed } from "vue";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: "Konfirmasi Tindakan",
    },
    message: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "green",
    },
    needsReason: {
        type: Boolean,
        default: false,
    },
    reasonValue: {
        type: String,
        default: "",
    },
    isSubmitting: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "update:reasonValue", "confirm"]);

const isReasonInvalid = computed(
    () => props.needsReason && props.reasonValue.length < 10,
);

const colorClasses = computed(() => {
    const map = {
        green: "bg-green-600 hover:bg-green-700",
        blue: "bg-blue-600 hover:bg-blue-700",
        orange: "bg-orange-600 hover:bg-orange-700",
        red: "bg-red-600 hover:bg-red-700",
    };
    return map[props.color] || map.green;
});
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        @click.self="emit('close')"
    >
        <div
            class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl space-y-4"
        >
            <h3 class="text-lg font-semibold text-gray-800">
                {{ title }}
            </h3>

            <p class="text-sm text-gray-600">{{ message }}</p>

            <!-- Reason Field -->
            <div v-if="needsReason">
                <textarea
                    :value="reasonValue"
                    @input="emit('update:reasonValue', $event.target.value)"
                    rows="3"
                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500"
                    placeholder="Tuliskan alasan (min. 10 karakter)..."
                ></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    @click="emit('close')"
                    class="rounded-lg border px-4 py-2 text-sm text-gray-600 hover:bg-gray-50"
                >
                    Batal
                </button>
                <button
                    @click="emit('confirm')"
                    :disabled="isSubmitting || isReasonInvalid"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
                    :class="colorClasses"
                >
                    {{ isSubmitting ? "Memproses..." : "Ya, Lanjutkan" }}
                </button>
            </div>
        </div>
    </div>
</template>
