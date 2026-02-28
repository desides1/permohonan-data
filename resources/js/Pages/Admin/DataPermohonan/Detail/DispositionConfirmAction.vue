<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    seksiList: {
        type: Array,
        default: () => [],
    },
    isSubmitting: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "confirm"]);

const selectedPetugasId = ref("");
const catatan = ref("");

const isInvalid = computed(() => !selectedPetugasId.value);

const selectedPetugasLabel = computed(() => {
    if (!selectedPetugasId.value) return "";

    for (const seksi of props.seksiList) {
        const users = seksi.users ?? [];
        const user = users.find(
            (u) => u.id === Number(selectedPetugasId.value),
        );
        if (user) return `${user.name} — ${seksi.nama}`;
    }
    return "";
});

// Seksi yang punya petugas
const filteredSeksiList = computed(() =>
    props.seksiList.filter((seksi) => (seksi.users ?? []).length > 0),
);

function handleConfirm() {
    emit("confirm", {
        petugas_id: selectedPetugasId.value,
        notes: catatan.value,
    });
}

function handleClose() {
    selectedPetugasId.value = "";
    catatan.value = "";
    emit("close");
}
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        @click.self="handleClose"
    >
        <div
            class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl space-y-5"
        >
            <!-- Header -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
            >
                <h3 class="text-lg font-semibold text-gray-800">
                    Disposisi Petugas Permohonan
                </h3>
                <select
                    v-model="selectedPetugasId"
                    class="rounded-lg border-green-500 text-sm focus:border-green-600 focus:ring-green-600"
                >
                    <option value="" disabled>Pilih tujuan petugas</option>
                    <optgroup
                        v-for="seksi in filteredSeksiList"
                        :key="seksi.id"
                        :label="seksi.nama"
                    >
                        <option
                            v-for="user in seksi.users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }} — {{ seksi.nama }}
                        </option>
                    </optgroup>
                </select>
            </div>

            <!-- Kosong -->
            <div
                v-if="filteredSeksiList.length === 0"
                class="rounded-lg bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800"
            >
                ⚠️ Belum ada petugas seksi yang terdaftar. Silakan tambahkan
                user dengan role <strong>seksi</strong> terlebih dahulu.
            </div>

            <!-- Petugas terpilih -->
            <div
                v-if="selectedPetugasLabel"
                class="rounded-lg bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-800"
            >
                Ditujukan ke: <strong>{{ selectedPetugasLabel }}</strong>
            </div>

            <hr class="border-gray-200" />

            <!-- Catatan -->
            <div>
                <label class="text-sm font-medium text-gray-700">
                    Catatan untuk petugas
                </label>
                <textarea
                    v-model="catatan"
                    rows="4"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500"
                    placeholder="Tuliskan catatan (opsional)"
                ></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <button
                    @click="handleClose"
                    class="rounded-lg border px-4 py-2 text-sm text-gray-600 hover:bg-gray-50"
                >
                    Batal
                </button>
                <button
                    @click="handleConfirm"
                    :disabled="isInvalid || isSubmitting"
                    class="rounded-lg bg-yellow-500 px-6 py-2 text-sm font-medium text-white hover:bg-yellow-600 disabled:opacity-50"
                >
                    {{ isSubmitting ? "Mengirim..." : "Kirim Tugas" }}
                </button>
            </div>
        </div>
    </div>
</template>
