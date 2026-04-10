<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import {
    ref,
    computed,
    provide,
    watch,
    onMounted,
    onBeforeUnmount,
    markRaw,
} from "vue";
import { usePage } from "@inertiajs/vue3";
import HomeContent from "./Sections/HomeContent.vue";
import FormContent from "./Sections/FormContent.vue";
import TrackContent from "./Sections/TrackContent.vue";
import FaqContent from "./Sections/FaqContent.vue";

defineOptions({ name: "LandingPageIndex" });

const props = defineProps({
    tab: { type: String, default: "beranda" },
    ticket: { type: Object, default: null },
});

const activeTab = ref(props.tab ?? "beranda");

const urlMap = {
    beranda: "/",
    formulir: "/formulir",
    lacak: "/lacak",
    bantuan: "/bantuan",
};

const setActiveTab = (tab) => {
    if (activeTab.value === tab) return;
    activeTab.value = tab;
    window.history.pushState({}, "", urlMap[tab]);
    setTimeout(() => {
        const mainContent = document.querySelector(".main-content");
        if (mainContent) {
            mainContent.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    }, 150);
};

watch(
    () => props.tab,
    (newTab) => {
        if (newTab && newTab !== activeTab.value) activeTab.value = newTab;
    },
);

provide("activeTab", activeTab);
provide("setActiveTab", setActiveTab);

const onPopState = () => {
    const path = window.location.pathname;
    const tabFromPath = Object.entries(urlMap).find(([, url]) => url === path);
    if (tabFromPath) {
        activeTab.value = tabFromPath[0];
    }
};

onMounted(() => window.addEventListener("popstate", onPopState));
onBeforeUnmount(() => {
    window.removeEventListener("popstate", onPopState);
    if (slideshowInterval) clearInterval(slideshowInterval);
});

const tabComponents = {
    beranda: markRaw(HomeContent),
    formulir: markRaw(FormContent),
    lacak: markRaw(TrackContent),
    bantuan: markRaw(FaqContent),
};

const currentComponent = computed(() => tabComponents[activeTab.value]);

const heroTitle = "Layanan BPKH Wilayah IX";
const heroSubtitle =
    "Menyediakan layanan permintaan data agar masyarakat dapat mengakses informasi secara terbuka.";

const heroImage = "/images/hutanmaluku.webp";
</script>

<template>
    <MainLayout>
        <!-- HERO: Tidak berubah saat ganti tab -->
        <section class="relative h-[220px] overflow-hidden">
            <!-- Gambar statis, tidak ada Transition -->
            <img
                :src="heroImage"
                class="absolute inset-0 h-full w-full object-cover object-top"
            />
            <div
                class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"
            ></div>

            <div
                class="relative z-10 max-w-7xl mx-auto px-6 py-10 text-white flex flex-col justify-center h-full"
            >
                <h1 class="text-3xl font-bold mb-2">{{ heroTitle }}</h1>
                <p class="max-w-xl text-sm leading-relaxed">
                    {{ heroSubtitle }}
                </p>
            </div>
        </section>

        <!-- CONTENT: Berubah saat ganti tab dengan slide animation -->
        <div class="main-content">
            <Transition name="slide-content" mode="out-in">
                <KeepAlive
                    :include="[
                        'HomeContent',
                        'FormContent',
                        'TrackContent',
                        'FaqContent',
                    ]"
                >
                    <component :is="currentComponent" :key="activeTab" />
                </KeepAlive>
            </Transition>
        </div>
    </MainLayout>
</template>

<style scoped>
.hero-bg-enter-active,
.hero-bg-leave-active {
    transition: opacity 0.7s ease;
}

.hero-bg-enter-from,
.hero-bg-leave-to {
    opacity: 0;
}

.slide-content-enter-active,
.slide-content-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-content-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.slide-content-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

html {
    scroll-behavior: smooth;
}
</style>
