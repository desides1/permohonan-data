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
import { CirclePlus, LoaderCircle } from "lucide-vue-next";
import { ref, computed, watch, reactive, onBeforeUnmount } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const isSubmitting = ref(false);
const showSuccessModal = ref(false);
const loadingSeconds = ref(0);
let loadingInterval = null;

const page = usePage();
const trackingCode = computed(() => page.props.flash?.ticket_code);

// Form data
const form = reactive({
    name: "",
    email: "",
    telp: "",
    postal_code: "",
    address: "",
    job: "",
    institute: "",
    data_purpose: "",
    submit_data: "",
    details_data: "",
    get_doc: "",
    send_doc: "",
    surat_permohonan: null,
    lampiran: [],
});

const fileName = ref("");
const lampiran = ref([]);

const fileInput = ref(null);
const lampiranFile = ref([]);

const startLoadingTimer = () => {
    loadingSeconds.value = 0;
    loadingInterval = setInterval(() => {
        loadingSeconds.value++;
    }, 1000);
};

const stopLoadingTimer = () => {
    if (loadingInterval) {
        clearInterval(loadingInterval);
        loadingInterval = null;
    }
    loadingSeconds.value = 0;
};

const submit = () => {
    if (isSubmitting.value) return;

    isSubmitting.value = true;
    startLoadingTimer();

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
            stopLoadingTimer();
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

onBeforeUnmount(() => {
    stopLoadingTimer();
});

watch(
    () => page.props.flash?.ticket_code,
    (val) => {
        if (val) {
            showSuccessModal.value = true;
            stopLoadingTimer();
        }
    },
);
</script>

<template>
    <MainLayout>
        <!-- Hero Section -->
        <section
            class="relative h-[260px] bg-cover bg-center sm:h-[320px] lg:h-[360px]"
            style="
                background-image: url(&quot;/images/requestdataofpeople.webp&quot;);
            "
        >
            <div class="absolute inset-0 bg-black/40"></div>
            <div
                class="relative z-10 mx-auto max-w-7xl px-4 py-16 text-white sm:px-6 sm:py-20 lg:px-6 lg:py-24"
            >
                <h1 class="mb-4 text-3xl font-bold sm:text-4xl">
                    Pengajuan Formulir
                </h1>
                <p class="max-w-xl text-sm">
                    Menyediakan layanan permintaan data agar masyarakat dapat
                    mengakses informasi secara terbuka, tepat waktu, dan sesuai
                    kebutuhan.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16">
            <h2 class="mb-10 text-2xl font-bold">
                Permohonan Data Informasi Publik
            </h2>

            <form
                @submit.prevent="submit"
                class="grid grid-cols-1 gap-x-6 gap-y-6 md:grid-cols-2 lg:gap-x-8"
                method="POST"
            >
                <div>
                    <Label for="name">Nama</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        name="name"
                        class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
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
                        class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
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
                        class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
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
                        class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
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
                        class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
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
                            class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
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
                            class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
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
                        class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan tujuan penggunaan data anda"
                    />
                    <InputError :message="page.props.errors.data_purpose" />
                </div>
                <div>
                    <Label>Cara Pengajuan</Label>
                    <RadioGroup v-model="form.submit_data" class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem value="direct" id="langsung" />
                            <Label for="langsung">Langsung</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem value="indirect" id="tidak" />
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
                        class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        placeholder="Masukkan detail data yang anda butuhkan"
                    />
                    <InputError :message="page.props.errors.details_data" />
                </div>
                <div>
                    <div class="mb-4">
                        <Label>Cara Pengambilan Data</Label>
                        <RadioGroup v-model="form.get_doc" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem value="copy" id="salinan" />
                                <Label for="salinan"
                                    >Salinan (SoftCopy/HardCopy)</Label
                                >
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem
                                    value="view_only"
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
                        <Select
                            v-model="form.send_doc"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                        >
                            <SelectTrigger>
                                <SelectValue
                                    class="border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                                    placeholder="Pilih metode"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="email">Email</SelectItem>
                                <SelectItem value="langsung"
                                    >Langsung</SelectItem
                                >
                                <SelectItem value="post">Pos</SelectItem>
                                <SelectItem value="courier">Kurir</SelectItem>
                            </SelectContent>
                            <InputError :message="page.props.errors.send_doc" />
                        </Select>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <Label>Surat Permohonan</Label>
                    <div class="flex flex-col gap-2 sm:flex-row">
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
                                class="cursor-pointer rounded-r-none border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                                placeholder="Unggah dokumen anda"
                                readonly
                                @click="triggerFileUpload"
                            />
                            <Button
                                type="button"
                                class="rounded-l-none border border-gray-300 rounded-md shadow-sm bg-lime-300 hover:bg-lime-400"
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

                    <div class="mt-1 flex flex-col gap-2">
                        <input
                            ref="lampiranFile"
                            type="file"
                            class="hidden"
                            :id="'lampiran-' + item.id"
                            @change="handleLampiranFileChange($event, index)"
                        />
                        <div
                            class="flex w-full flex-col gap-2 sm:flex-row sm:items-start"
                        >
                            <div class="flex w-full">
                                <Input
                                    :value="item.fileName"
                                    class="cursor-pointer rounded-r-none border border-gray-300 rounded-md shadow-sm focus:border-lime-400 focus:ring-2 focus:ring-lime-400"
                                    placeholder="Unggah dokumen anda"
                                    readonly
                                    @click="triggerLampiranUpload(index)"
                                />
                                <Button
                                    type="button"
                                    class="rounded-l-none border border-gray-300 rounded-md shadow-sm bg-lime-300 hover:bg-lime-400"
                                    @click="triggerLampiranUpload(index)"
                                >
                                    Unggah
                                </Button>
                            </div>
                            <Button
                                type="button"
                                variant="outline"
                                class="border-red-400 text-red-500 sm:ml-0"
                                @click="removeLampiran(index)"
                            >
                                Hapus
                            </Button>
                        </div>
                        <InputError :message="page.props.errors.lampiran" />
                    </div>
                </div>
                <div class="flex justify-center md:col-span-2 mt-4">
                    <Button
                        type="button"
                        size="icon"
                        variant="outline"
                        class="iconPlus w-10 rounded-full bg-lime-200 hover:bg-lime-300"
                        @click="addInputField"
                        :disabled="isSubmitting"
                    >
                        <CirclePlus />
                    </Button>
                </div>
                <div class="md:col-span-2 mt-10 lg:mt-12 flex justify-end">
                    <Button
                        class="w-full px-24 text-white hover:bg-primary-dark sm:w-auto"
                        type="submit"
                        :disabled="isSubmitting"
                    >
                        <span class="inline-flex items-center gap-2">
                            <LoaderCircle
                                v-if="isSubmitting"
                                class="animate-spin"
                            />
                            {{ isSubmitting ? "Sedang Mengirim..." : "Kirim" }}
                        </span>
                    </Button>
                </div>
            </form>
        </section>
    </MainLayout>

    <Dialog :open="isSubmitting">
        <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
        <DialogContent
            class="fixed left-1/2 top-1/2 w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-xl bg-white p-6 pointer-events-none content-center"
        >
            <h3 class="text-lg font-semibold">Permintaan Data</h3>
            <div class="my-6 flex flex-col items-center justify-center gap-4">
                <img
                    src="/images/prosesicon.svg"
                    class="mx-auto my-6 h-32 sm:h-40"
                    alt="loading"
                />
            </div>

            <LoaderCircle
                class="h-12 w-12 animate-spin text-primary text-center"
            />
            <p class="text-center text-sm text-muted-foreground">
                Sedang Mengirim Data ...
            </p>
            <p class="mt-2 text-center text-xs text-muted-foreground">
                Mohon tunggu proses berjalan dalam {{ loadingSeconds }} detik
                hingga proses selesai <br />
                jangan tutup halaman ini
            </p>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="showSuccessModal">
        <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
        <DialogContent
            class="fixed left-1/2 top-1/2 w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-xl bg-white p-6"
        >
            <h3 class="text-lg font-semibold">Permintaan Data</h3>
            <img
                src="/images/succes_prosses.svg"
                class="mx-auto my-6 h-32 sm:h-40"
                alt="success"
            />
            <p class="text-center">
                Permintaan data anda berhasil dikirimkan, berikut kode tracking
                formulir anda
            </p>
            <p class="mt-4 text-center text-2xl font-bold">
                {{ trackingCode }}
            </p>
            <p class="mt-3 text-center text-xs text-muted-foreground">
                Periksa email anda atau hubungi no. 000000000
            </p>
            <Button
                class="mt-6 w-full text-white hover:bg-primary-dark sm:w-auto"
                @click="showSuccessModal = false"
            >
                Selesai
            </Button>
        </DialogContent>
    </Dialog>
</template>
