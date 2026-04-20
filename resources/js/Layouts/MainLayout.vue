<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import { Menu, X } from "lucide-vue-next";

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const handleResize = () => {
    if (window.innerWidth >= 768) {
        isMobileMenuOpen.value = false;
    }
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

watch(isMobileMenuOpen, (open) => {
    document.body.style.overflow = open ? "hidden" : "";
});

onMounted(() => {
    handleScroll();
    handleResize();
    window.addEventListener("scroll", handleScroll);
    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
    window.removeEventListener("resize", handleResize);
    document.body.style.overflow = "";
});
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <nav
            :class="[
                'fixed left-0 top-0 z-50 w-full transition-all duration-300',
                isScrolled || isMobileMenuOpen
                    ? 'border-b border-gray-200 bg-white shadow-md'
                    : 'bg-transparent',
            ]"
        >
            <div
                class="container mx-auto flex min-h-16 items-center justify-between px-4 py-2 sm:px-6 lg:px-10 xl:px-16 2xl:px-32"
            >
                <Link href="/" class="shrink-0" @click="closeMobileMenu">
                    <img
                        src="/images/kehutananlogo.png"
                        alt="KEHUTANAN"
                        class="h-9 object-contain sm:h-10 md:h-12"
                    />
                </Link>

                <div
                    class="hidden items-center gap-4 text-sm font-medium lg:gap-6 md:flex"
                >
                    <Link href="/" class="text-black">Beranda</Link>
                    <Link href="/formulir" class="text-black">Formulir</Link>
                    <Link href="/lacak" class="text-black"
                        >Lacak Permohonan</Link
                    >
                    <!-- <Link href="/bantuan" class="text-black">Bantuan</Link> -->
                </div>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-black transition hover:bg-black/5 md:hidden"
                    @click="toggleMobileMenu"
                    aria-label="Toggle menu"
                >
                    <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
                    <X v-else class="h-6 w-6" />
                </button>
            </div>

            <div
                v-if="isMobileMenuOpen"
                class="border-t border-gray-200 bg-white md:hidden"
            >
                <div class="container mx-auto px-4 py-3 sm:px-6">
                    <div class="flex flex-col gap-1 text-sm font-medium">
                        <Link
                            href="/"
                            class="rounded-md px-3 py-2 text-black hover:bg-gray-100"
                            @click="closeMobileMenu"
                        >
                            Beranda
                        </Link>
                        <Link
                            href="/formulir"
                            class="rounded-md px-3 py-2 text-black hover:bg-gray-100"
                            @click="closeMobileMenu"
                        >
                            Formulir
                        </Link>
                        <Link
                            href="/lacak"
                            class="rounded-md px-3 py-2 text-black hover:bg-gray-100"
                            @click="closeMobileMenu"
                        >
                            Lacak Permohonan
                        </Link>
                        <!-- <Link
                            href="/bantuan"
                            class="rounded-md px-3 py-2 text-black hover:bg-gray-100"
                            @click="closeMobileMenu"
                        >
                            Bantuan
                        </Link> -->
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="relative overflow-hidden text-white">
            <div
                class="absolute inset-0 bg-gradient-to-r from-primary via-emerald-900 to-green-950"
            ></div>

            <div
                class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"
            ></div>

            <div
                class="relative z-10 mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-14 lg:px-8"
            >
                <div class="grid gap-8 md:grid-cols-3 md:gap-10">
                    <div>
                        <h3 class="text-lg font-semibold">Alamat</h3>
                        <p class="mt-3 text-sm text-white/70 sm:text-base">
                            Jalan Kebun Cengkeh - Ambon <br />
                            Kode Pos 97128
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold">Kontak</h3>
                        <p class="mt-3 text-sm text-white/70 sm:text-base">
                            Email: bpkh9ambon@gmail.com <br />
                            Telp/Fax: (0911) - 342632
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold">Media Sosial</h3>
                        <div class="mt-4 flex gap-4 text-white/80">
                            <a
                                href="https://www.facebook.com/bpkhwilayah.ix.9/"
                                class="hover:text-green-300"
                                aria-label="Facebook"
                            >
                                <svg
                                    class="h-6 w-6 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M22 12.07C22 6.48 17.52 2 11.93 2S2 6.48 2 12.07c0 4.99 3.66 9.12 8.44 9.88v-6.99H8.08v-2.89h2.36V9.41c0-2.33 1.39-3.62 3.52-3.62 1.02 0 2.09.18 2.09.18v2.3h-1.18c-1.16 0-1.52.72-1.52 1.46v1.75h2.59l-.41 2.89h-2.18v6.99C18.34 21.19 22 17.06 22 12.07Z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="https://www.youtube.com/@bpkhixambon"
                                class="hover:text-green-300"
                                aria-label="YouTube"
                            >
                                <svg
                                    class="h-6 w-6 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="https://www.instagram.com/bpkh9ambon?igsh=MTdieTE1eWYxYnIzYw=="
                                class="hover:text-green-300"
                                aria-label="Instagram"
                            >
                                <svg
                                    class="h-6 w-6 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7Zm0 2h10c1.65 0 3 1.35 3 3v10c0 1.65-1.35 3-3 3H7c-1.65 0-3-1.35-3-3V7c0-1.65 1.35-3 3-3Zm10.25 1.5a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-10 border-t border-white/20 pt-6 text-center text-xs text-white/60 sm:mt-12 sm:text-sm"
                >
                    © 2026 BPKH Wilayah IX. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
