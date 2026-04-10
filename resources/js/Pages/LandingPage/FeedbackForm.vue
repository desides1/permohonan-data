<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

import { Button } from "@/Components/ui/button/index.js";
import { Input } from "@/Components/ui/input/index.js";
import { Label } from "@/Components/ui/label/index.js";
import { Textarea } from "@/Components/ui/textarea/index.js";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card/index.js";
import {
    RadioGroup,
    RadioGroupItem,
} from "@/Components/ui/radio-group/index.js";
import { CheckCircle2, MessageSquareHeart, ShieldCheck } from "lucide-vue-next";

const page = usePage();

const form = useForm({
    ticket_code: page.props.ticket_code ?? "",
    service_usability: "",
    service_satisfaction: "",
    illegal_fee_indication: "",
    suggestions: "",
});

const flashSuccess = computed(() => page.props.flash?.success);

const submitFeedback = () => {
    form.post(route("feedback.submit"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Feedback Layanan" />

    <div class="min-h-screen bg-slate-50">
        <section
            class="relative overflow-hidden bg-gradient-to-br from-emerald-900 via-emerald-800 to-lime-700 text-white"
        >
            <div class="absolute inset-0 bg-black/10"></div>
            <div
                class="absolute -left-12 top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl"
            ></div>
            <div
                class="absolute right-0 top-0 h-56 w-56 rounded-full bg-lime-300/20 blur-3xl"
            ></div>

            <div
                class="relative mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8"
            >
                <div
                    class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"
                >
                    <div class="max-w-2xl">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]"
                        >
                            <MessageSquareHeart class="h-4 w-4" />
                            Survei Layanan
                        </div>
                        <h1
                            class="text-3xl font-extrabold tracking-tight sm:text-4xl"
                        >
                            Form Feedback Pelayanan
                        </h1>
                        <p
                            class="mt-3 text-sm leading-6 text-white/85 sm:text-base"
                        >
                            Masukan Anda membantu kami meningkatkan kualitas
                            layanan permohonan data agar lebih mudah, cepat, dan
                            transparan.
                        </p>
                    </div>

                    <Link
                        href="/"
                        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15"
                    >
                        Kembali ke Beranda
                    </Link>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <Card
                    class="rounded-3xl border-0 shadow-[0_20px_60px_rgba(15,23,42,0.08)]"
                >
                    <CardHeader class="space-y-2 p-5 sm:p-7">
                        <CardTitle class="text-2xl font-bold text-slate-900">
                            Isi Feedback Anda
                        </CardTitle>
                        <CardDescription
                            class="text-sm leading-6 text-slate-600"
                        >
                            Pilih jawaban yang paling sesuai dengan pengalaman
                            Anda. Jika ada masukan tambahan, tuliskan pada kolom
                            saran.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="p-5 pt-0 sm:p-7 sm:pt-0">
                        <div
                            v-if="flashSuccess"
                            class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                        >
                            <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0" />
                            <span>{{ flashSuccess }}</span>
                        </div>

                        <form
                            @submit.prevent="submitFeedback"
                            class="space-y-6"
                        >
                            <div class="space-y-2">
                                <Label for="ticket_code">Kode Tiket</Label>
                                <Input
                                    id="ticket_code"
                                    v-model="form.ticket_code"
                                    type="text"
                                    placeholder="Contoh: BPKH-2026-0001"
                                    class="h-11 rounded-xl"
                                />
                                <p
                                    v-if="form.errors.ticket_code"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.ticket_code }}
                                </p>
                            </div>

                            <div
                                class="space-y-3 rounded-2xl border border-slate-200 p-4 sm:p-5"
                            >
                                <Label
                                    class="text-sm font-semibold text-slate-900"
                                >
                                    Kemudahan penggunaan layanan
                                </Label>
                                <RadioGroup
                                    v-model="form.service_usability"
                                    class="grid gap-3"
                                >
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="sangat_mudah"
                                            class="mt-1"
                                        />
                                        <div>
                                            <div
                                                class="font-medium text-slate-900"
                                            >
                                                Sangat mudah
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                Form dan alurnya jelas, mudah
                                                dipahami.
                                            </div>
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="mudah"
                                            class="mt-1"
                                        />
                                        <div>
                                            <div
                                                class="font-medium text-slate-900"
                                            >
                                                Mudah
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                Cukup mudah digunakan dengan
                                                sedikit penyesuaian.
                                            </div>
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="cukup"
                                            class="mt-1"
                                        />
                                        <div>
                                            <div
                                                class="font-medium text-slate-900"
                                            >
                                                Cukup
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                Dapat digunakan, tetapi masih
                                                ada bagian yang membingungkan.
                                            </div>
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="sulit"
                                            class="mt-1"
                                        />
                                        <div>
                                            <div
                                                class="font-medium text-slate-900"
                                            >
                                                Sulit
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                Banyak bagian yang perlu
                                                diperbaiki.
                                            </div>
                                        </div>
                                    </label>
                                </RadioGroup>
                                <p
                                    v-if="form.errors.service_usability"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.service_usability }}
                                </p>
                            </div>

                            <div
                                class="space-y-3 rounded-2xl border border-slate-200 p-4 sm:p-5"
                            >
                                <Label
                                    class="text-sm font-semibold text-slate-900"
                                >
                                    Tingkat kepuasan terhadap layanan
                                </Label>
                                <RadioGroup
                                    v-model="form.service_satisfaction"
                                    class="grid gap-3"
                                >
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="sangat_puas"
                                            class="mt-1"
                                        />
                                        <div class="font-medium text-slate-900">
                                            Sangat puas
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="puas"
                                            class="mt-1"
                                        />
                                        <div class="font-medium text-slate-900">
                                            Puas
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="cukup_puas"
                                            class="mt-1"
                                        />
                                        <div class="font-medium text-slate-900">
                                            Cukup puas
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="tidak_puas"
                                            class="mt-1"
                                        />
                                        <div class="font-medium text-slate-900">
                                            Tidak puas
                                        </div>
                                    </label>
                                </RadioGroup>
                                <p
                                    v-if="form.errors.service_satisfaction"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.service_satisfaction }}
                                </p>
                            </div>

                            <div
                                class="space-y-3 rounded-2xl border border-slate-200 p-4 sm:p-5"
                            >
                                <Label
                                    class="text-sm font-semibold text-slate-900"
                                >
                                    Apakah terdapat indikasi biaya tidak resmi?
                                </Label>
                                <RadioGroup
                                    v-model="form.illegal_fee_indication"
                                    class="grid gap-3"
                                >
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="tidak_ada"
                                            class="mt-1"
                                        />
                                        <div class="font-medium text-slate-900">
                                            Tidak ada
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="ada"
                                            class="mt-1"
                                        />
                                        <div class="font-medium text-slate-900">
                                            Ada
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <RadioGroupItem
                                            value="tidak_tahu"
                                            class="mt-1"
                                        />
                                        <div class="font-medium text-slate-900">
                                            Tidak tahu
                                        </div>
                                    </label>
                                </RadioGroup>
                                <p
                                    v-if="form.errors.illegal_fee_indication"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.illegal_fee_indication }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="suggestions">
                                    Saran atau jawaban lain
                                </Label>
                                <Textarea
                                    id="suggestions"
                                    v-model="form.suggestions"
                                    rows="5"
                                    placeholder="Tuliskan saran, kritik, atau hal lain yang ingin disampaikan..."
                                    class="rounded-2xl"
                                />
                                <p class="text-xs text-slate-500">
                                    Kolom ini dapat diisi untuk masukan
                                    tambahan.
                                </p>
                                <p
                                    v-if="form.errors.suggestions"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.suggestions }}
                                </p>
                            </div>

                            <Button
                                type="submit"
                                class="h-11 w-full rounded-xl bg-emerald-700 text-white hover:bg-emerald-800"
                                :disabled="form.processing"
                            >
                                {{
                                    form.processing
                                        ? "Mengirim..."
                                        : "Kirim Feedback"
                                }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <div class="space-y-5">
                    <Card
                        class="rounded-3xl border-0 bg-white shadow-[0_20px_60px_rgba(15,23,42,0.06)]"
                    >
                        <CardHeader class="p-5 sm:p-6">
                            <CardTitle
                                class="flex items-center gap-2 text-lg text-slate-900"
                            >
                                <ShieldCheck class="h-5 w-5 text-emerald-700" />
                                Privasi Masukan
                            </CardTitle>
                        </CardHeader>
                        <CardContent
                            class="space-y-3 p-5 pt-0 text-sm leading-6 text-slate-600 sm:p-6 sm:pt-0"
                        >
                            <p>
                                Feedback digunakan untuk evaluasi peningkatan
                                mutu layanan.
                            </p>
                            <p>
                                Masukan Anda membantu kami memperbaiki proses,
                                tampilan, dan kecepatan layanan.
                            </p>
                        </CardContent>
                    </Card>

                    <Card
                        class="rounded-3xl border-0 bg-gradient-to-br from-emerald-700 to-lime-600 text-white shadow-[0_20px_60px_rgba(5,150,105,0.18)]"
                    >
                        <CardContent class="p-5 sm:p-6">
                            <div
                                class="text-sm font-semibold uppercase tracking-[0.16em] text-white/80"
                            >
                                Informasi
                            </div>
                            <h3 class="mt-2 text-xl font-bold">
                                Isi feedback dengan jujur dan singkat
                            </h3>
                            <p class="mt-3 text-sm leading-6 text-white/85">
                                Jika Anda memiliki kode tiket, cantumkan agar
                                masukan dapat dikaitkan dengan layanan yang
                                diterima.
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>
    </div>
</template>
