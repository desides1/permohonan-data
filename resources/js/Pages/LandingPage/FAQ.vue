<script setup>
import { ref } from "vue";
import MainLayout from "@/Layouts/MainLayout.vue";

const faqs = [
    {
        question: "Apakah seluruh informasi dapat diakses oleh publik?",
        answer: "Ya, seluruh informasi publik dapat diakses oleh masyarakat, kecuali informasi yang dikecualikan/rahasia.",
    },
    {
        question:
            "Bagaimana pembagian klasifikasi informasi di lingkungan Kementerian Kehutanan?",
        answer: "Informasi diklasifikasikan sesuai ketentuan yang berlaku, antara lain informasi yang wajib diumumkan secara berkala, serta informasi yang dikecualikan.",
    },
    {
        question: "Apakah ada biaya tarif dalam layanan informasi?",
        answer: "Pada prinsipnya layanan informasi tidak dipungut biaya, kecuali untuk penggandaan dokumen sesuai ketentuan.",
    },
    {
        question: "Kenapa permohonan data saya ditolak?",
        answer: "Permohonan dapat ditolak jika tidak memenuhi persyaratan, termasuk permintaan atas informasi yang dikecualikan.",
    },
];

const activeIndex = ref(null);

const toggleFaq = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index;
};
</script>

<template>
    <MainLayout>
        <!-- Hero -->
        <section
            class="relative h-[360px] bg-cover bg-center"
            style="background-image: url('/images/pulauPulauKecil.jpg')"
        >
            <div class="absolute inset-0 bg-black/40"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-6 py-24 text-white">
                <h1 class="text-4xl font-bold mb-4">FAQ - Pertanyaan Umum</h1>
                <p class="max-w-xl text-sm">
                    Temukan jawaban atas pertanyaan umum mengenai layanan
                    permohonan data kami.
                </p>
            </div>
        </section>

        <!-- FAQ -->
        <section class="max-w-5xl mx-auto py-16 px-6">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold mb-2">Pertanyaan Umum</h2>
                <p class="text-sm text-gray-600">
                    Beberapa pertanyaan yang sering diajukan mengenai Layanan
                    Permintaan Data
                </p>
            </div>

            <div class="border border-gray-200">
                <div
                    v-for="(item, index) in faqs"
                    :key="index"
                    class="border-b border-gray-200 last:border-b-0"
                >
                    <!-- Bar pertanyaan -->
                    <button
                        type="button"
                        class="w-full text-left px-4 py-3 text-sm flex justify-between items-center"
                        :class="
                            activeIndex === index
                                ? 'bg-lime-50'
                                : 'bg-gray-50 hover:bg-gray-100'
                        "
                        @click="toggleFaq(index)"
                    >
                        <span>{{ item.question }}</span>
                        <span class="ml-4 text-xl leading-none">
                            {{ activeIndex === index ? "−" : "+" }}
                        </span>
                    </button>

                    <!-- Jawaban -->
                    <div
                        v-if="activeIndex === index"
                        class="bg-lime-100 px-4 py-3 text-sm text-gray-700"
                    >
                        {{ item.answer }}
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
<style scoped></style>
