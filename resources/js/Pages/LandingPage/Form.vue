<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
// import PublicRequestForm from "@/Components/PublicRequestForm.vue";
import { Input } from "@/Components/ui/input/index.js";
import { Label } from "@/Components/ui/label/index.js";
import {
    RadioGroup,
    RadioGroupItem,
} from "@/Components/ui/radio-group/index.js";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select/index.js";
import { Textarea } from "@/Components/ui/textarea/index.js";
import { Button } from "@/Components/ui/button/index.js";
import { CirclePlus } from "lucide-vue-next";
</script>

<template>
    <MainLayout>
        <!-- hero section form -->
        <section
            class="relative h-[360px] bg-cover bg-center"
            style="background-image: url('/images/pulauPulauKecil.jpg')"
        >
            <div class="absolute inset-0 bg-black/40"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-6 py-24 text-white">
                <h1 class="text-4xl font-bold mb-4">Pengajuan Formulir</h1>
                <p class="max-w-xl text-sm">
                    Menyediakan layanan permintaan data agar masyarakat dapat
                    mengakses informasi secara terbuka, tepat waktu, dan sesuai
                    kebutuhan.
                </p>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-6 py-16">
            <h2 class="text-2xl font-bold mb-10">
                Permohonan Data Informasi Publik
            </h2>

            <PublicRequestForm />
            <form class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div>
                    <Label for="nama">Nama</Label>
                    <Input
                        id="nama"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan nama lengkap anda"
                    />
                </div>
                <div>
                    <Label for="email">Surel</Label>
                    <Input
                        id="email"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan surel anda"
                    />
                </div>
                <div>
                    <Label for="telepon">No. Telepon</Label>
                    <Input
                        id="telepon"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan nomor telepon anda"
                    />
                </div>
                <div>
                    <Label for="kodepos">Kode Pos</Label>
                    <Input
                        id="kodepos"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan kode pos anda"
                    />
                </div>
                <div class="">
                    <Label>Alamat</Label>
                    <Textarea
                        rows="5"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan alamat lengkap anda"
                    />
                </div>
                <div class="">
                    <div class="mb-4">
                        <Label for="pekerjaan">Pekerjaan</Label>
                        <Input
                            id="pekerjaan"
                            class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                            placeholder="Masukkan pekerjaan anda"
                        />
                    </div>
                    <div>
                        <Label for="instansi">Instansi</Label>
                        <Input
                            id="instansi"
                            class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                            placeholder="Masukkan instansi anda"
                        />
                    </div>
                </div>

                <div>
                    <Label for="tujuan">Tujuan Penggunaan Data</Label>
                    <Input
                        id="tujuan"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan tujuan penggunaan data anda"
                    />
                </div>
                <div class="">
                    <label for="RadioGroup">Cara Pengajuan</label>
                    <RadioGroup default-value="langsung" class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem value="langsung" id="langsung" />
                            <Label for="langsung">Langsung</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem value="tidak" id="tidak" />
                            <Label for="tidak">Tidak Langsung</Label>
                        </div>
                    </RadioGroup>
                </div>

                <div class="">
                    <Label>Detail Data Yang Di Butuhkan</Label>
                    <Textarea
                        rows="5"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan detail data yang anda butuhkan"
                    />
                </div>

                <div class="">
                    <div class="mb-4">
                        <label for="RadioGroup">Cara Pengambilan Data</label>
                        <RadioGroup default-value="salinan" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem value="Salinan" id="salinan" />
                                <Label for="salinan"
                                    >Salinan (SoftCopy/HardCopy)</Label
                                >
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem value="melihat" id="melihat" />
                                <Label for="melihat"
                                    >Melihat/Membaca/Mendengarkan/Mencatat</Label
                                >
                            </div>
                        </RadioGroup>
                    </div>

                    <div>
                        <Label>Cara Penyerahan</Label>

                        <Select>
                            <SelectTrigger>
                                <SelectValue
                                    class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                                    placeholder="Pilih metode"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="email">Email</SelectItem>
                                <SelectItem value="langsung"
                                    >Langsung</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <Label>Surat Permohonan</Label>

                    <div class="flex gap-2">
                        <input
                            ref="fileInput"
                            type="file"
                            class="hidden"
                            @change="handleFileChange"
                        />
                        <div class="flex w-full">
                            <Input
                                :value="fileName"
                                class="rounded-r-none cursor-pointer focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                                placeholder="Unggah dokumen anda"
                                readonly
                                @click="triggerFileUpload"
                            />
                            <Button
                                type="button"
                                class="rounded-l-none bg-lime-300 hover:bg-lime-400"
                                @click="triggerFileUpload"
                            >
                                Unggah
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- LAMPIRAN DINAMIS -->
                <div
                    v-for="(item, index) in lampiran"
                    :key="item.id"
                    class="md:col-span-2"
                >
                    <Label :for="'lampiran-' + item.id">
                        Lampiran {{ index + 1 }}
                    </Label>

                    <div class="flex gap-2 mt-1">
                        <input
                            ref="lampiranFile"
                            type="file"
                            class="hidden"
                            :id="'lampiran-' + item.id"
                            @change="handleLampiranFileChange($event, index)"
                        />
                        <div class="flex w-full">
                            <Input
                                :value="item.fileName"
                                class="rounded-r-none cursor-pointer focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                                placeholder="Unggah dokumen anda"
                                readonly
                                @click="triggerLampiranUpload(index)"
                            />
                            <Button
                                type="button"
                                class="rounded-l-none bg-lime-300 hover:bg-lime-400"
                                @click="triggerLampiranUpload(index)"
                            >
                                Unggah
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                class="ml-2 border-red-400 text-red-500"
                                @click="removeLampiran(index)"
                            >
                                Hapus
                            </Button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center md:col-span-2 mt-4">
                    <Button
                        type="button"
                        size="icon"
                        variant="outline"
                        class="w-10 rounded-full iconPlus bg-lime-200 hover:bg-lime-300"
                        @click="addInputField"
                    >
                        <CirclePlus />
                    </Button>
                </div>

                <div class="md:col-span-2 mt-10">
                    <Button class="hover:bg-primary-dark px-24"> Kirim </Button>
                </div>
            </form>
        </section>
    </MainLayout>
</template>

<script>
export default {
    data() {
        return {
            fileName: "",
            lampiran: [], // daftar lampiran
        };
    },
    methods: {
        // surat permohonan utama
        triggerFileUpload() {
            this.$refs.fileInput.click();
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            this.fileName = file ? file.name : "";
        },

        // lampiran dinamis
        addInputField() {
            this.lampiran.push({
                id: Date.now() + Math.random(),
                fileName: "",
                file: null,
            });
        },
        triggerLampiranUpload(index) {
            const input = this.$refs.lampiranFile?.[index];
            if (input) input.click();
        },
        handleLampiranFileChange(event, index) {
            const file = event.target.files[0];
            if (!file) return;
            this.lampiran[index].fileName = file.name;
            this.lampiran[index].file = file;
        },
        removeLampiran(index) {
            this.lampiran.splice(index, 1);
        },
    },
};
</script>
