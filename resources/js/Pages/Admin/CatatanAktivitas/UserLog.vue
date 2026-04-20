<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import { Input } from "@/Components/ui/input";
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import {
    Search,
    LogIn,
    LogOut,
    KeyRound,
    UserCog,
    Shield,
    Pencil,
} from "lucide-vue-next";

const props = defineProps({
    activities: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? "");
let debounce = null;

watch(search, (val) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            route("admin.activity-log.user"),
            { search: val || undefined },
            { preserveState: true, replace: true },
        );
    }, 400);
});

const actionConfig = {
    login: {
        label: "Login",
        icon: LogIn,
        class: "bg-green-100 text-green-700",
    },
    logout: {
        label: "Logout",
        icon: LogOut,
        class: "bg-gray-100 text-gray-700",
    },
    password_reset: {
        label: "Reset Password",
        icon: KeyRound,
        class: "bg-yellow-100 text-yellow-700",
    },
    update_profile: {
        label: "Update Profil",
        icon: Pencil,
        class: "bg-blue-100 text-blue-700",
    },
    role_changed: {
        label: "Ubah Role",
        icon: Shield,
        class: "bg-purple-100 text-purple-700",
    },
    crud: {
        label: "CRUD Admin",
        icon: UserCog,
        class: "bg-indigo-100 text-indigo-700",
    },
};

function getConfig(action) {
    return (
        actionConfig[action] ?? {
            label: action,
            icon: UserCog,
            class: "bg-gray-100 text-gray-700",
        }
    );
}
</script>

<template>
    <LayoutDashboard>
        <section class="space-y-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">
                    Aktivitas Pengguna
                </h1>
                <p class="text-sm text-slate-500">
                    Rekam login, logout, perubahan profil, dan aktivitas admin
                </p>
            </div>

            <!-- Filter -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 max-w-sm">
                    <Search
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    />
                    <Input
                        v-model="search"
                        placeholder="Cari nama atau deskripsi..."
                        class="pl-9"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">Pengguna</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                                <th
                                    class="px-4 py-3 text-left hidden md:table-cell"
                                >
                                    Deskripsi
                                </th>
                                <th
                                    class="px-4 py-3 text-left hidden lg:table-cell"
                                >
                                    IP Address
                                </th>
                                <th class="px-4 py-3 text-left">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="act in activities.data"
                                :key="act.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ act.causer_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ act.causer_email }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="getConfig(act.action).class"
                                    >
                                        <component
                                            :is="getConfig(act.action).icon"
                                            class="w-3 h-3"
                                        />
                                        {{ getConfig(act.action).label }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-600 max-w-xs truncate hidden md:table-cell"
                                >
                                    {{ act.description }}
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-500 text-xs font-mono hidden lg:table-cell"
                                >
                                    {{ act.ip_address ?? "-" }}
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-500 whitespace-nowrap"
                                >
                                    <span class="hidden sm:inline">{{
                                        act.created_at
                                    }}</span>
                                    <span class="sm:hidden">{{
                                        act.time_ago
                                    }}</span>
                                </td>
                            </tr>
                            <tr v-if="activities.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-4 py-8 text-center text-gray-400"
                                >
                                    Belum ada aktivitas pengguna.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="activities.last_page > 1"
                    class="flex items-center justify-between border-t px-4 py-3"
                >
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ activities.from }}-{{
                            activities.to
                        }}
                        dari {{ activities.total }}
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in activities.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            class="px-3 py-1 text-xs rounded-md border"
                            :class="
                                link.active
                                    ? 'bg-green-600 text-white border-green-600'
                                    : 'hover:bg-gray-50'
                            "
                            v-html="link.label"
                            preserve-state
                        />
                    </div>
                </div>
            </div>
        </section>
    </LayoutDashboard>
</template>
