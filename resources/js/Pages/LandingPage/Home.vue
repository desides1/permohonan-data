<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Button from "@/Components/ui/button/Button.vue";
import Card from "@/Components/ui/card/Card.vue";
import CardHeader from "@/Components/ui/card/CardHeader.vue";
import CardTitle from "@/Components/ui/card/CardTitle.vue";
import CardDescription from "@/Components/ui/card/CardDescription.vue";
import CardContent from "@/Components/ui/card/CardContent.vue";
import { ref, onMounted, onBeforeUnmount } from "vue";

// header animation images

const images = [
    "/images/hutanmaluku.webp",
    "/images/requestdataofpeople.webp",
    "/images/searchforest.webp",
];

const currentImage = ref(images[0]);
const isFading = ref(false);

let index = 0;
let intervalId = null;

onMounted(() => {
    intervalId = setInterval(() => {
        isFading.value = true;

        setTimeout(() => {
            index = (index + 1) % images.length;
            currentImage.value = images[index];
            isFading.value = false;
        }, 1000);
    }, 6000);
});

onBeforeUnmount(() => {
    if (intervalId) clearInterval(intervalId);
});

// FAQ data and logic
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

const steps = [
    {
        title: "Langkah 1",
        description:
            "Masuk ke halaman formulir untuk mengajukan permintaan data",
        detail: "Silahkan pilih menu formulir atau klik tombol Ajukan Permohonan untuk masuk ke halaman formulir",
        image: "/images/langkah1.png",
        alt: "langkah1",
    },
    {
        title: "Langkah 2",
        description:
            "Isi data diri anda secara lengkap sesuai kartu identitas yang anda miliki",
        detail: "Pastikan semua informasi yang dimasukkan sesuai dengan identitas resmi Anda untuk memudahkan proses verifikasi dan komunikasi terkait permohonan data Anda.",
        image: "/images/langkah2.svg",
        alt: "langkah2",
    },
    {
        title: "Langkah 3",
        description: "Isikan jenis permohonan data yang anda inginkan",
        detail: "Pilih jenis permohonan data yang sesuai dengan kebutuhan Anda untuk memastikan proses pengajuan berjalan lancar.",
        image: "/images/langkah3.svg",
        alt: "langkah3",
    },
    {
        title: "Langkah 4",
        description: "Upload dokumen yang dibutuhkan",
        detail: "Unggah dokumen yang diperlukan sesuai dengan jenis permohonan data Anda untuk mempercepat proses verifikasi dan pastikan memiliki surat permohonan yang sah.",
        image: "/images/langkah4.svg",
        alt: "langkah4",
    },
    {
        title: "Langkah 5",
        description:
            "Tekan tombol Kirim untuk mengirimkan dokumen yang diminta",
        detail: "Setelah semua dokumen terisi dengan benar, tekan tombol Kirim untuk mengirimkan permohonan Anda. Pastikan untuk memeriksa kembali semua informasi sebelum mengirim agar proses pengajuan berjalan lancar.",
        image: "/images/langkah5.svg",
        alt: "langkah5",
    },
    {
        title: "Langkah 6",
        description: "Tunggu hingga kode tiket permohonan keluar",
        detail: "Setelah mengirimkan permohonan, tunggu hingga Anda menerima kode tiket yang akan digunakan untuk melacak status permohonan data Anda. Kode tiket ini akan dikirimkan melalui email atau dapat dilihat di halaman dashboard pemohon.",
        image: "/images/langkah6.svg",
        alt: "langkah6",
    },
];
</script>

<template>
    <MainLayout>
        <!-- HERO -->
        <section
            class="relative h-[420px] w-full overflow-hidden bg-cover bg-top sm:h-[500px] lg:h-[620px]"
        >
            <img
                ref="heroImage"
                :src="currentImage"
                class="absolute inset-0 h-full w-full object-cover object-top transition-opacity duration-1000"
                :class="{ 'opacity-0': isFading }"
            />
            <div
                class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"
            ></div>

            <div
                class="absolute left-0 top-1/2 w-full -translate-y-1/2 px-4 text-white animate-in slide-in-from-bottom-4 duration-700 sm:px-8 md:px-12 lg:left-10 lg:w-auto lg:px-24"
            >
                <h1 class="mb-2 text-2xl font-bold sm:text-3xl lg:text-4xl">
                    Layanan BPKH Wilayah IX
                </h1>
                <p class="max-w-xl text-sm sm:text-base">
                    Menyediakan layanan permintaan data agar masyarakat dapat
                    mengakses informasi secara terbuka.
                </p>
            </div>
        </section>

        <!-- LAYANAN -->
        <section
            class="relative overflow-hidden container px-4 py-12 sm:px-6 sm:py-14 lg:px-16 xl:px-32 xl:py-16"
        >
            <div
                class="pointer-events-none absolute -left-16 top-6 h-36 w-36 rounded-full bg-emerald-100/70 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute right-0 top-0 h-44 w-44 rounded-full bg-lime-100/70 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute bottom-0 right-1/4 h-20 w-20 rotate-12 rounded-3xl border border-emerald-200/60 bg-white/70 backdrop-blur-sm"
            ></div>

            <div class="grid grid-cols-1 gap-10 items-center relative z-10">
                <div class="animate-in fade-in duration-700">
                    <h2 class="mb-4 text-2xl font-bold sm:text-3xl">
                        Layanan Data BPKH Wilayah IX
                    </h2>
                    <p class="mb-6 text-sm text-gray-700 sm:text-base">
                        Layanan Kehutanan Digital hadir untuk menyederhanakan
                        proses Anda. Sistem online kami dirancang khusus untuk
                        mempercepat pengajuan dan pendistribusian data
                        kehutanan. Selain itu, proses kami sepenuhnya
                        transparan. Anda bisa memantau status permintaan data
                        Anda kapan saja dan di mana saja.
                    </p>
                    <a href="/formulir" class="w-full sm:w-auto">
                        <Button
                            class="w-full rounded-lg bg-primary px-6 text-white hover:bg-primary-dark sm:w-auto"
                        >
                            Ajukan Permohonan
                        </Button>
                    </a>
                </div>
            </div>
        </section>

        <!-- LANGKAH -->
        <section
            class="relative overflow-hidden bg-gray-50 px-4 py-14 sm:px-6 sm:py-16 lg:px-16 xl:px-32 xl:py-20"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.08),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(132,204,22,0.08),transparent_30%)]"
            ></div>
            <div
                class="pointer-events-none absolute right-10 top-10 h-28 w-28 rounded-full border border-emerald-200/60 bg-white/40 backdrop-blur-sm"
            ></div>

            <div class="container relative z-10 mx-auto">
                <div class="mx-auto mb-12 max-w-3xl text-center">
                    <span
                        class="inline-flex items-center rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700 shadow-sm"
                    >
                        Alur Layanan
                    </span>
                    <h2
                        class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        Cara Mengajukan Permohonan Data
                    </h2>
                    <p
                        class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-gray-600 sm:text-base"
                    >
                        Ikuti langkah berikut agar proses pengajuan data lebih
                        tertib, cepat, dan mudah dipahami.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <Card
                        v-for="(step, index) in steps"
                        :key="step.title"
                        class="group overflow-hidden rounded-3xl border border-emerald-100/80 bg-white p-0 shadow-[0_10px_30px_rgba(15,23,42,0.05)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(5,150,105,0.12)]"
                    >
                        <div
                            class="flex h-full flex-col gap-5 p-5 sm:flex-row sm:items-center sm:gap-6 sm:p-7"
                        >
                            <div
                                class="flex items-center gap-4 sm:w-auto sm:flex-col sm:items-start"
                            >
                                <div
                                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 ring-1 ring-emerald-100 transition-transform duration-300 group-hover:scale-105 sm:h-16 sm:w-16"
                                >
                                    <img
                                        :src="step.image"
                                        :alt="step.alt"
                                        class="h-8 w-8 object-contain sm:h-10 sm:w-10"
                                    />
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div
                                    class="mb-2 text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700"
                                >
                                    Langkah {{ index + 1 }}
                                </div>

                                <h3
                                    class="text-lg font-bold leading-snug text-gray-900 sm:text-xl"
                                >
                                    {{ step.description }}
                                </h3>

                                <div
                                    class="mt-4 flex items-center gap-2 text-sm text-gray-500"
                                >
                                    <span
                                        class="h-2 w-2 rounded-full bg-emerald-500"
                                    ></span>
                                    <span>{{ step.detail }}</span>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>
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

<style>
@keyframes kenburns {
    0% {
        transform: scale(1) translateY(0);
    }
    100% {
        transform: scale(1.12) translateY(-30px);
    }
}
.animate-kenburns {
    animation: kenburns 10s ease-in-out infinite alternate;
}
</style>
