<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";

//
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
                <form class="flex flex-col items-center space-y-4">
                    <div class="w-full flex flex-col items-center space-y-1">
                        <label
                            for="trackingNumber"
                            class="text-sm font-medium text-black"
                            >Masukkan kode tiket</label
                        >
                        <input
                            type="text"
                            id="trackingNumber"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-center focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                            placeholder="Masukkan nomor permohonan Anda"
                        />
                    </div>
                    <button
                        type="submit"
                        class="bg-primary hover:bg-primary-dark text-white rounded-lg px-12 py-2"
                    >
                        Lacak
                    </button>
                </form>
            </div>
        </section>
    </MainLayout>
</template>
