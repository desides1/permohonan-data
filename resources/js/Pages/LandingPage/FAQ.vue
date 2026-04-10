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
            class="relative h-[260px] bg-cover bg-center sm:h-[320px] lg:h-[360px]"
            style="background-image: url(&quot;/images/hutanmaluku.webp&quot;)"
        >
            <div class="absolute inset-0 bg-black/40"></div>

            <div
                class="relative z-10 mx-auto max-w-7xl px-4 py-16 text-white sm:px-6 sm:py-20 lg:px-6 lg:py-24"
            >
                <h1 class="mb-4 text-3xl font-bold sm:text-4xl">
                    FAQ - Pertanyaan Umum
                </h1>
                <p class="max-w-xl text-sm">
                    Temukan jawaban atas pertanyaan umum mengenai layanan
                    permohonan data kami.
                </p>
            </div>
        </section>

        <!-- FAQ -->
        <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16">
            <div class="mb-10 text-center">
                <h2 class="mb-2 text-2xl font-bold sm:text-3xl">
                    Pertanyaan Umum
                </h2>
                <p class="text-sm text-gray-600">
                    Beberapa pertanyaan yang sering diajukan mengenai Layanan
                    Permintaan Data
                </p>
            </div>

            <div class="overflow-hidden rounded-md border border-gray-200">
                <div
                    v-for="(item, index) in faqs"
                    :key="index"
                    class="border-b border-gray-200 last:border-b-0"
                >
                    <button
                        type="button"
                        class="flex w-full items-start justify-between px-4 py-3 text-left text-sm sm:items-center"
                        :class="
                            activeIndex === index
                                ? 'bg-lime-50'
                                : 'bg-gray-50 hover:bg-gray-100'
                        "
                        @click="toggleFaq(index)"
                    >
                        <span class="pr-4">{{ item.question }}</span>
                        <span class="ml-4 shrink-0 text-xl leading-none">
                            {{ activeIndex === index ? "−" : "+" }}
                        </span>
                    </button>

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
