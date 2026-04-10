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
            class="relative h-[260px] bg-cover bg-center sm:h-[320px] lg:h-[360px]"
            style="background-image: url(&quot;/images/searchforest.webp&quot;)"
        >
            <div class="absolute inset-0 bg-black/40"></div>

            <div
                class="relative z-10 mx-auto max-w-7xl px-4 py-16 text-white sm:px-6 sm:py-20 lg:px-6 lg:py-24"
            >
                <h1 class="mb-4 text-3xl font-bold sm:text-4xl">
                    Lacak Permohonan
                </h1>
                <p class="max-w-xl text-sm">
                    Masukkan nomor permohonan Anda di bawah ini untuk melacak
                    status permohonan data Anda.
                </p>
            </div>
        </section>

        <section
            class="my-12 flex items-center justify-center px-4 sm:my-16 sm:px-6 lg:mx-32"
        >
            <div class="w-full max-w-md text-center">
                <h1 class="mb-4 text-2xl font-bold">Lacak Data Permohonan</h1>
                <form
                    class="flex flex-col items-center space-y-4"
                    @submit.prevent="submitTracking"
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
                        class="w-full rounded-lg bg-primary px-12 py-2 text-white hover:bg-primary-dark sm:w-auto"
                        type="submit"
                    >
                        Lacak
                    </button>
                </form>
            </div>
        </section>

        <section v-if="isVisible" class="my-12 px-4 sm:my-16 sm:px-6 lg:mx-32">
            <div class="w-full text-center">
                <h2 class="mb-4 text-xl font-bold">Hasil Pelacakan</h2>
                <div class="overflow-x-auto rounded-md border border-gray-300">
                    <table
                        class="w-full min-w-[640px] table-auto border-collapse text-sm"
                    >
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-2">
                                    Tanggal
                                </th>
                                <th class="border border-gray-300 px-4 py-2">
                                    Kode Tiket
                                </th>
                                <th class="border border-gray-300 px-4 py-2">
                                    Status
                                </th>
                                <th class="border border-gray-300 px-4 py-2">
                                    Catatan
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ ticket.updated_at }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ ticket.ticket_code }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ ticket.status }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ ticket.notes }}
                                    <p>
                                        Kunjungi halaman
                                        <a
                                            href="/pemohon/hasil-permohonan"
                                            class="underline text-primary-light"
                                            >Detail formulir</a
                                        >pemohon untuk informasi lebih
                                        lengkapnya
                                    </p>

                                    <!-- button feedback -->
                                    <div>
                                        <span>
                                            Silakan berikan feedback mengenai
                                            layanan kami untuk membantu kami
                                            meningkatkan kualitas layanan di
                                            masa depan.
                                        </span>
                                        <hr />
                                        <a
                                            href="/feedback"
                                            class="w-full sm:w-auto"
                                        >
                                            <Button
                                                class="mt-2 rounded-lg bg-primary px-4 py-2 text-white hover:bg-primary-dark sm:w-auto"
                                            >
                                                Berikan Feedback
                                            </Button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div
            class="mx-4 my-4 flex items-center justify-center rounded border border-lime-200 bg-slate-100 p-4 sm:mx-6 lg:mx-32"
            v-if="form.errors.ticket_code"
        >
            <p v-if="form.errors.ticket_code" class="text-center text-red-500">
                {{ form.errors.ticket_code }}
            </p>
        </div>
    </MainLayout>
</template>
