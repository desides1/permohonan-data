<script setup>
import { computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    user: Object,
});

const page = usePage();

const seksiOptions = computed(() => page.props.master?.seksiOptions ?? []);
const roles = computed(() => page.props.auth?.user?.roles ?? []);

const form = useForm({
    name: props.user.name ?? "",
    email: props.user.email ?? "",
    seksi_id: props.user.seksi_id ?? "",
});

const updateProfileInformation = () => {
    form.put(route("user-profile-information.update"), {
        errorBag: "updateProfileInformation",
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title> Informasi Admin </template>

        <template #description>
            Perbarui data akun admin yang digunakan untuk masuk ke sistem.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Nama" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="seksi_id" value="Seksi" />
                <select
                    id="seksi_id"
                    v-model="form.seksi_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">Pilih seksi</option>
                    <option
                        v-for="seksi in seksiOptions"
                        :key="seksi.id"
                        :value="seksi.id"
                    >
                        {{ seksi.label }}
                    </option>
                </select>
                <InputError :message="form.errors.seksi_id" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel value="Role" />
                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-for="role in roles"
                        :key="role"
                        class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"
                    >
                        {{ role }}
                    </span>
                    <span
                        v-if="roles.length === 0"
                        class="text-sm text-slate-500"
                    >
                        Tidak ada role.
                    </span>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Tersimpan.
            </ActionMessage>

            <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Simpan
            </PrimaryButton>
        </template>
    </FormSection>
</template>
