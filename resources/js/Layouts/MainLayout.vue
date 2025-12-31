<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <!-- NAVBAR -->
        <nav class="w-full fixed top-0 z-50 bg-white/50 backdrop-blur border-b">
            <div
                class="container flex mx-auto items-center justify-between h-16 px-32"
            >
                <!-- <img class="font-bold text-green-800">KEHUTANAN</img> -->
                <img
                    src="/images/kehutananlogo.png"
                    alt="KEHUTANAN"
                    class="h-12 object-contain"
                />

                <div class="flex gap-6 text-sm font-medium items-center">
                    <Link href="/" class="hover:text-green-700">Beranda</Link>
                    <Link href="/formulir" class="hover:text-green-700"
                        >Formulir</Link
                    >
                    <Link href="/lacak" class="hover:text-green-700"
                        >Lacak Permohonan</Link
                    >
                    <Link href="/bantuan" class="hover:text-green-700"
                        >Bantuan</Link
                    >
                </div>
            </div>
        </nav>

        <!-- Content -->

        <main class="pt-16 flex-1">
            <slot />
        </main>

        <!-- FOOTER -->
        <footer class="bg-primary text-green-100 py-10 px-32">
            <div
                class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-6"
            >
                <div>
                    <strong class="font-bold mb-2">Alamat</strong>
                    <p>Jalan Kebun Cengkeh - Ambon</p>
                    <p>Kode Pos 97128</p>
                </div>

                <div>
                    <strong class="font-bold mb-2">Kontak</strong>
                    <p>Email: bpkh9ambon@gmail.com</p>
                    <p>Telepon/fax: (0911) - 342632</p>
                    <p>Kotak Pos: 1125</p>
                </div>

                <div>
                    <strong class="font-bold mb-2">Media Sosial</strong>
                    <p>Ikuti kami di media sosial</p>
                    <div class="flex gap-4 mt-2">
                        <a
                            href="#"
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
                        <a href="#" class="hover:text-green-300" aria-label="X">
                            <svg
                                class="h-6 w-6 fill-current"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M18.15 2H21l-6.52 7.45L22 22h-6.95l-4.34-5.68L5.7 22H3l6.98-7.98L2 2h7.09l3.91 5.19L18.15 2Zm-1.22 18h1.93L7.14 4h-2L16.93 20Z"
                                />
                            </svg>
                        </a>
                        <a
                            href="#"
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
        </footer>
    </div>
</template>

<style></style>
