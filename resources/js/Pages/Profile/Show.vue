<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import DeleteUserForm from "@/Pages/Profile/Partials/DeleteUserForm.vue";
import LogoutOtherBrowserSessionsForm from "@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue";
import TwoFactorAuthenticationForm from "@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue";
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <LayoutDashboard>
        <div class="mx-auto w-full max-w-5xl px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
            <div class="space-y-4 sm:space-y-6">
                <div
                    class="overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-700 to-emerald-600 p-5 text-white shadow-sm sm:rounded-3xl sm:p-6 lg:p-8"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-100"
                    >
                        Admin
                    </p>
                    <h1 class="mt-2 text-2xl font-bold sm:text-3xl">
                        Profil Admin
                    </h1>
                    <p
                        class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base"
                    >
                        Kelola informasi akun, keamanan, dan pengaturan profil
                        admin.
                    </p>
                </div>

                <div class="space-y-4 sm:space-y-6">
                    <div
                        v-if="$page.props.jetstream.canUpdateProfileInformation"
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 sm:rounded-3xl"
                    >
                        <div class="px-4 py-5 sm:px-6 sm:py-6">
                            <UpdateProfileInformationForm
                                :user="$page.props.auth.user"
                            />
                        </div>
                    </div>

                    <div
                        v-if="$page.props.jetstream.canUpdatePassword"
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 sm:rounded-3xl"
                    >
                        <div class="px-4 py-5 sm:px-6 sm:py-6">
                            <UpdatePasswordForm />
                        </div>
                    </div>

                    <div
                        v-if="
                            $page.props.jetstream
                                .canManageTwoFactorAuthentication
                        "
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 sm:rounded-3xl"
                    >
                        <div class="px-4 py-5 sm:px-6 sm:py-6">
                            <TwoFactorAuthenticationForm
                                :requires-confirmation="
                                    confirmsTwoFactorAuthentication
                                "
                            />
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 sm:rounded-3xl"
                    >
                        <div class="px-4 py-5 sm:px-6 sm:py-6">
                            <LogoutOtherBrowserSessionsForm
                                :sessions="sessions"
                            />
                        </div>
                    </div>

                    <div
                        v-if="$page.props.jetstream.hasAccountDeletionFeatures"
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-red-200 sm:rounded-3xl"
                    >
                        <div class="px-4 py-5 sm:px-6 sm:py-6">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutDashboard>
</template>
