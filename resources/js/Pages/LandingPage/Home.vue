<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Button from "@/Components/ui/button/Button.vue";
import Card from "@/Components/ui/card/Card.vue";
import CardHeader from "@/Components/ui/card/CardHeader.vue";
import CardTitle from "@/Components/ui/card/CardTitle.vue";
import CardDescription from "@/Components/ui/card/CardDescription.vue";
import CardContent from "@/Components/ui/card/CardContent.vue";
</script>

<template>
    <MainLayout>
        <!-- HERO -->
        <section class="relative w-full h-[480px]">
            <img
                src="/images/pulauPulauKecil.jpg"
                class="w-full h-full object-cover"
            />

            <div class="absolute inset-0 bg-black/40" />

            <div
                class="absolute left-10 top-1/2 px-24 transform -translate-y-1/2 text-white animate-in slide-in-from-bottom-4 duration-700"
            >
                <h1 class="text-4xl font-bold mb-2">Layanan BPKH Wilayah IX</h1>
                <p class="max-w-xl">
                    Menyediakan layanan permintaan data agar masyarakat dapat
                    mengakses informasi secara terbuka.
                </p>
            </div>
        </section>

        <!-- LAYANAN -->
        <section class="py-16 container px-32">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-10 items-center">
                <div class="animate-in fade-in duration-700">
                    <h2 class="text-3xl font-bold mb-4">
                        Layanan Data BPKH Wilayah IX
                    </h2>
                    <p class="mb-6 text-gray-700">
                        Layanan Kehutanan Digital hadir untuk menyederhanakan
                        proses Anda. Sistem online kami dirancang khusus untuk
                        mempercepat pengajuan dan pendistribusian data
                        kehutanan. Selain itu, proses kami sepenuhnya
                        transparan. Anda bisa memantau status permintaan data
                        Anda kapan saja dan di mana saja.
                    </p>
                    <Button
                        class="bg-primary hover:bg-primary-dark text-white rounded-lg px-6"
                    >
                        Ajukan Permohonan
                    </Button>
                </div>

                <!-- <img
                    src="/images/badge.png"
                    class="w-48 mx-auto animate-in zoom-in-50 duration-700"
                /> -->
            </div>
        </section>

        <!-- LANGKAH -->
        <section class="py-16 bg-gray-50 px-32">
            <div class="container mx-auto">
                <h2 class="text-3xl font-bold mb-10">
                    Cara Mengajukan Permohonan Data
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card
                        class="grid grid-cols-[auto,1fr] p-4 animate-in fade-in-50 duration-700 bg-white cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all"
                    >
                        <img
                            src="/images/langkah1.png"
                            alt="langkah1"
                            class="h-24"
                        />
                        <CardHeader class="">
                            <CardTitle>Langkah 1</CardTitle>
                            <CardDescription
                                >Masuk ke halaman formulir untuk mengajukan
                                permintaan data</CardDescription
                            >
                        </CardHeader>
                    </Card>

                    <Card
                        class="grid grid-cols-[auto,1fr] p-4 animate-in fade-in-50 duration-700 delay-150 bg-white cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all"
                    >
                        <img
                            src="/images/langkah2.svg"
                            alt="langkah2"
                            class="h-24 col"
                        />
                        <CardHeader class="">
                            <CardTitle>Langkah 2</CardTitle>
                            <CardDescription
                                >Isi data diri anda secara lengkap sesuai kartu
                                identitas yang anda miliki</CardDescription
                            >
                        </CardHeader>
                    </Card>

                    <Card
                        class="grid grid-cols-[auto,1fr] p-4 animate-in fade-in-50 duration-700 delay-300 bg-white cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all"
                    >
                        <img
                            src="/images/langkah3.svg"
                            alt="langkah3"
                            class="h-24 col"
                        />
                        <CardHeader>
                            <CardTitle>Langkah 3</CardTitle>
                            <CardDescription
                                >Isikan jenis permohonan data yang anda
                                inginkan</CardDescription
                            >
                        </CardHeader>
                    </Card>

                    <Card
                        class="grid grid-cols-[auto,1fr] p-4 animate-in fade-in-50 duration-700 delay-300 bg-white cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all"
                    >
                        <img
                            src="/images/langkah4.svg"
                            alt="langkah4"
                            class="h-24 col"
                        />
                        <CardHeader>
                            <CardTitle>Langkah 4</CardTitle>
                            <CardDescription
                                >Upload dokumen yang dibutuhkan
                            </CardDescription>
                        </CardHeader>
                    </Card>
                    <Card
                        class="grid grid-cols-[auto,1fr] p-4 animate-in fade-in-50 duration-700 delay-300 bg-white cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all"
                    >
                        <img
                            src="/images/langkah5.svg"
                            alt="langkah5"
                            class="h-24 col"
                        />
                        <CardHeader>
                            <CardTitle>Langkah 5</CardTitle>
                            <CardDescription
                                >Tekan tombol Kirim untuk mengirimkan dokumen
                                yang diminta</CardDescription
                            >
                        </CardHeader>
                    </Card>
                    <Card
                        class="grid grid-cols-[auto,1fr] p-4 animate-in fade-in-50 duration-700 delay-300 bg-white cursor-pointer hover:shadow-lg hover:scale-[1.02] transition-all"
                    >
                        <img
                            src="/images/langkah6.svg"
                            alt="langkah6"
                            class="h-24 col"
                        />
                        <CardHeader>
                            <CardTitle>Langkah 6</CardTitle>
                            <CardDescription
                                >Tunggu hingga kode tiket permohonan
                                keluar</CardDescription
                            >
                        </CardHeader>
                    </Card>

                    <!-- lanjutkan sampai Langkah 6 -->
                </div>
            </div>
        </section>
    </MainLayout>
</template>
