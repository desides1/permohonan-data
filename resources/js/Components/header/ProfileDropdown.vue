<template>
    <div class="relative" ref="dropdownRef">
        <button
            @click="toggle"
            class="flex items-center gap-3 px-2 py-1 rounded-md hover:bg-slate-100"
            :aria-expanded="open"
            aria-haspopup="true"
        >
            <!-- Avatar -->
            <div
                class="w-8 h-8 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center font-semibold text-sm"
            >
                {{ initials }}
            </div>

            <!-- Name -->
            <span class="hidden sm:block text-sm font-medium">
                {{ adminName }}
            </span>

            <!-- Arrow -->
            <svg class="w-3 h-3 text-slate-500" viewBox="0 0 24 24" fill="none">
                <path
                    d="M6 9l6 6 6-6"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </button>

        <!-- Dropdown -->
        <transition name="fade">
            <div
                v-if="open"
                class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg ring-1 ring-slate-100 z-50"
            >
                <a href="/profile" class="dropdown-item">Profil</a>
                <a href="/settings" class="dropdown-item">Pengaturan</a>

                <div class="border-t border-slate-100"></div>

                <a
                    href="#"
                    @click.prevent="showLogoutConfirm"
                    class="dropdown-item text-rose-600"
                >
                    Keluar
                </a>

                <!-- Logout Confirmation Dialog -->
                <div
                    v-if="logoutConfirm"
                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                >
                    <div class="bg-white rounded-lg p-6 max-w-sm">
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">
                            Konfirmasi Keluar
                        </h3>
                        <p class="text-slate-600 mb-6">
                            Apakah Anda yakin ingin keluar?
                        </p>
                        <div class="flex gap-3 justify-end">
                            <button
                                @click="logoutConfirm = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-md"
                            >
                                Batal
                            </button>
                            <button
                                @click="logout"
                                class="px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-md"
                            >
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { router } from "@inertiajs/vue3";

/* =====================
   Props
===================== */
const props = defineProps({
    adminName: {
        type: String,
        default: "Admin",
    },
});

/* =====================
   State
===================== */
const open = ref(false);
const logoutConfirm = ref(false);
const dropdownRef = ref(null);

/* =====================
   Computed
===================== */
const initials = computed(() =>
    props.adminName
        .split(" ")
        .map((word) => word[0])
        .slice(0, 2)
        .join("")
        .toUpperCase(),
);

/* =====================
   Methods
===================== */
const toggle = () => {
    open.value = !open.value;
};

const showLogoutConfirm = () => {
    logoutConfirm.value = true;
};

const logout = () => {
    router.post(
        route("logout"),
        {},
        {
            preserveScroll: false,
            onFinish: () => {
                logoutConfirm.value = false;
                open.value = false;
            },
        },
    );
};

const handleClickOutside = (event) => {
    if (!dropdownRef.value) return;
    if (!dropdownRef.value.contains(event.target)) {
        open.value = false;
    }
};

/* =====================
   Lifecycle
===================== */
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
.dropdown-item {
    @apply block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
