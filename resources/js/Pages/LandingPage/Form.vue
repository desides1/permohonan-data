<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
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
import { ref, computed, watch, reactive } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const isSubmitting = ref(false);
const showSuccessModal = ref(false);

const page = usePage();
const trackingCode = computed(() => page.props.flash?.ticket_code);

// Form data
const form = reactive({
    nama: "",
    email: "",
    telepon: "",
    kodepos: "",
    alamat: "",
    pekerjaan: "",
    instansi: "",
    tujuan: "",
    cara_pengajuan: "langsung",
    details_data: "",
    cara_pengambilan: "salinan",
    cara_penyerahan: "",

    surat_permohonan: null,
    lampiran: [],
});

const fileName = ref("");
const lampiran = ref([]);

const fileInput = ref(null);
const lampiranFile = ref([]);

const submit = () => {
    isSubmitting.value = true;
    // Prepare FormData if file upload is needed
    const data = new FormData();
    Object.entries(form).forEach(([key, value]) => {
        if (key === "lampiran") {
            lampiran.value.forEach((item, idx) => {
                if (item.file) data.append(`lampiran[${idx}]`, item.file);
            });
        } else if (key === "surat_permohonan" && form.surat_permohonan) {
            data.append("surat_permohonan", form.surat_permohonan);
        } else {
            data.append(key, value);
        }
    });

    router.post(route("landing.post"), data, {
        preserveScroll: true,
        forceFormData: true,
        onFinish: () => {
            isSubmitting.value = false;
        },
        onSuccess: () => {
            showSuccessModal.value = true;
        },
    });
};

const triggerFileUpload = () => fileInput.value?.click();
const handleFileChange = (event) => {
    const file = event.target.files[0];
    fileName.value = file ? file.name : "";
    form.surat_permohonan = file || null;
};

const addInputField = () => {
    lampiran.value.push({
        id: Date.now() + Math.random(),
        fileName: "",
        file: null,
    });
};

const triggerLampiranUpload = (index) => {
    lampiranFile.value[index]?.click();
};

const handleLampiranFileChange = (event, index) => {
    const file = event.target.files[0];
    if (!file) return;
    lampiran.value[index].fileName = file.name;
    lampiran.value[index].file = file;
    form.lampiran = lampiran.value;
};

const removeLampiran = (index) => {
    lampiran.value.splice(index, 1);
    form.lampiran = lampiran.value;
};

watch(
    () => page.props.flash?.ticket_code,
    (val) => {
        if (val) {
            showSuccessModal.value = true;
        }
    }
);
</script>

<template>
    <MainLayout>
        <!-- Hero Section -->
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

            <form
                @submit.prevent="submit"
                class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"
                method="POST"
            >
                <div>
                    <Label for="name">Nama</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        name="name"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan nama lengkap anda"
                    />
                    <InputError :message="page.props.errors.name" />
                </div>
                <div>
                    <Label for="email">Surel</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        name="email"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan surel anda"
                    />
                    <InputError :message="page.props.errors.email" />
                </div>
                <div>
                    <Label for="telp">No. Telepon</Label>
                    <Input
                        id="telp"
                        name="telp"
                        v-model="form.telp"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan nomor telepon anda"
                    />
                    <InputError :message="page.props.errors.telp" />
                </div>
                <div>
                    <Label for="postal_code">Kode Pos</Label>
                    <Input
                        id="postal_code"
                        name="postal_code"
                        v-model="form.postal_code"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan kode pos anda"
                    />
                    <InputError :message="page.props.errors.postal_code" />
                </div>
                <div>
                    <Label>Alamat</Label>
                    <Textarea
                        rows="5"
                        v-model="form.address"
                        name="address"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan alamat lengkap anda"
                    />
                    <InputError :message="page.props.errors.address" />
                </div>
                <div>
                    <div class="mb-4">
                        <Label for="job">Pekerjaan</Label>
                        <Input
                            id="job"
                            name="job"
                            v-model="form.job"
                            class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                            placeholder="Masukkan pekerjaan anda"
                        />
                        <InputError :message="page.props.errors.job" />
                    </div>
                    <div>
                        <Label for="institute">Instansi</Label>
                        <Input
                            id="institute"
                            name="institute"
                            v-model="form.institute"
                            class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                            placeholder="Masukkan instansi anda"
                        />
                        <InputError :message="page.props.errors.institute" />
                    </div>
                </div>
                <div>
                    <Label for="tujuan">Tujuan Penggunaan Data</Label>
                    <Input
                        id="tujuan"
                        name="data_purpose"
                        v-model="form.data_purpose"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan tujuan penggunaan data anda"
                    />
                    <InputError :message="page.props.errors.data_purpose" />
                </div>
                <div>
                    <Label>Cara Pengajuan</Label>
                    <RadioGroup v-model="form.submit_data" class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem value="Langsung" id="langsung" />
                            <Label for="langsung">Langsung</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem value="Tidak Langsung" id="tidak" />
                            <Label for="tidak">Tidak Langsung</Label>
                        </div>
                    </RadioGroup>
                    <InputError :message="page.props.errors.submit_data" />
                </div>
                <div>
                    <Label>Detail Data Yang Di Butuhkan</Label>
                    <Textarea
                        rows="5"
                        v-model="form.details_data"
                        class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan detail data yang anda butuhkan"
                    />
                    <InputError :message="page.props.errors.details_data" />
                </div>
                <div>
                    <div class="mb-4">
                        <Label>Cara Pengambilan Data</Label>
                        <RadioGroup v-model="form.get_doc" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem
                                    value="Salinan (softcopy/hardcopy)"
                                    id="salinan"
                                />
                                <Label for="salinan"
                                    >Salinan (SoftCopy/HardCopy)</Label
                                >
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem
                                    value="Melihat/membaca/mendengarkan/mencatat"
                                    id="melihat"
                                />
                                <Label for="melihat"
                                    >Melihat/Membaca/Mendengarkan/Mencatat</Label
                                >
                            </div>
                        </RadioGroup>
                        <InputError :message="page.props.errors.get_doc" />
                    </div>
                    <div>
                        <Label>Cara Penyerahan</Label>
                        <Select v-model="form.send_doc">
                            <SelectTrigger>
                                <SelectValue
                                    class="focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                                    placeholder="Pilih metode"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="Email">Email</SelectItem>
                                <SelectItem value="Langsung"
                                    >Langsung</SelectItem
                                >
                                <SelectItem value="Pos">Pos</SelectItem>
                                <SelectItem value="Kurir">Kurir</SelectItem>
                            </SelectContent>
                            <InputError :message="page.props.errors.send_doc" />
                        </Select>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <Label>Surat Permohonan</Label>
                    <div class="flex gap-2">
                        <input
                            ref="fileInput"
                            type="file"
                            accept="application/pdf"
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
                    <InputError :message="page.props.errors.surat_permohonan" />
                </div>
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
                            <InputError :message="page.props.errors.lampiran" />
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
                    <Button
                        class="hover:bg-primary-dark px-24"
                        type="submit"
                        :disabled="isSubmitting"
                    >
                        Kirim
                    </Button>
                </div>
            </form>
        </section>
    </MainLayout>

    <!-- Loading Dialog -->
    <Dialog :open="isSubmitting">
        <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
        <DialogContent
            class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg rounded-xl bg-white p-6 pointer-events-none"
        >
            <h3 class="text-lg font-semibold">Permintaan Data</h3>
            <img
                src="/images/prosesicon.svg"
                class="mx-auto my-6 h-40"
                alt="loading"
            />
            <p class="text-center text-sm text-muted-foreground">
                Sedang Mengirim Data ...
            </p>
        </DialogContent>
    </Dialog>

    <!-- Success Dialog -->
    <Dialog v-model:open="showSuccessModal">
        <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
        <DialogContent
            class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg rounded-xl bg-white p-6"
        >
            <h3 class="text-lg font-semibold">Permintaan Data</h3>
            <img
                src="/images/success_prosses.svg"
                class="mx-auto my-6 h-40"
                alt="success"
            />
            <p class="text-center">
                Permintaan data anda berhasil dikirimkan, berikut kode tracking
                formulir anda
            </p>
            <p class="text-center text-2xl font-bold mt-4">
                {{ trackingCode }}
            </p>
            <p class="text-xs text-muted-foreground text-center mt-3">
                Periksa email anda atau hubungi no. 000000000
            </p>
            <Button class="mt-6 w-full" @click="showSuccessModal = false">
                Selesai
            </Button>
        </DialogContent>
    </Dialog>
</template>
