<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, computed, watch } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import Input from "@/Components/ui/input/Input.vue";

const page = usePage();

const form = useForm({
    ticket_code: "",
});

const ticket = computed(() => page.props.ticket ?? null);
const error = computed(() => page.props.flash?.error ?? null);

const isVisible = ref(false);

watch(ticket, (val) => {
    if (val) {
        isVisible.value = true;
    }
});

const submitTracking = () => {
    form.post(route("ticket.track"));
};
</script>

<template>
    <MainLayout>
        <section
            class="relative h-[360px] bg-cover bg-center"
            style="background-image: url('/images/pulauPulauKecil.jpg')"
        >
            <div class="absolute inset-0 bg-black/40"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-6 py-24 text-white">
                <h1 class="text-4xl font-bold mb-4">Lacak Permohonan</h1>
                <p class="max-w-xl text-sm">
                    Masukkan nomor permohonan Anda di bawah ini untuk melacak
                    status permohonan data Anda.
                </p>
            </div>
        </section>
        <section class="flex justify-center items-center my-16 mx-32">
            <div class="w-full max-w-md text-center">
                <h1 class="text-2xl font-bold mb-4">Lacak Data Permohonan</h1>
                <form
                    @submit.prevent="submitTracking"
                    class="flex flex-col items-center space-y-4"
                    method="POST"
                >
                    <div class="w-full flex flex-col items-center space-y-1">
                        <label
                            for="trackingNumber"
                            class="text-sm font-medium text-black"
                            >Masukkan kode tiket</label
                        >
                        <input
                            type="text"
                            v-model="form.ticket_code"
                            id="trackingNumber"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-center focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                            placeholder="Masukkan nomor permohonan Anda"
                        />
                        <InputError :message="page.props.errors.ticket_code" />
                    </div>
                    <button
                        class="bg-primary hover:bg-primary-dark text-white rounded-lg px-12 py-2"
                    >
                        Lacak
                    </button>
                </form>
            </div>
        </section>

        <!-- result tracking request -->
        <section
            v-if="isVisible"
            class="flex justify-center items-center my-16 mx-32"
        >
            <div class="w-full max-w-2xl text-center">
                <h2 class="text-xl font-bold mb-4">Hasil Pelacakan</h2>
                <table
                    class="w-full table-auto border-collapse border border-gray-300"
                >
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">
                                Kode Tiket
                            </th>
                            <th class="border border-gray-300 px-4 py-2">
                                Status
                            </th>
                            <th class="border border-gray-300 px-4 py-2">
                                Penugasan
                            </th>
                            <th class="border border-gray-300 px-4 py-2">
                                Catatan
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ ticket.ticket_code }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ ticket.status }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ ticket.assignment }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ ticket.notes }}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div
            class="flex justify-center items-center my-4 mx-32 border border-lime-200 bg-slate-100 p-4 rounded"
            v-if="error"
        >
            <p v-if="error" class="text-red-500 text-center">
                {{ error }}
            </p>
        </div>
    </MainLayout>
</template>
