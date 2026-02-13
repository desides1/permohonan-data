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
                <a href="/profile" class="dropdown-item"> Profil </a>

                <a href="/settings" class="dropdown-item"> Pengaturan </a>

                <div class="border-t border-slate-100"></div>

                <a href="/logout" class="dropdown-item text-rose-600">
                    Keluar
                </a>
            </div>
        </transition>
    </div>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

export default {
    name: "ProfileDropdown",
    props: {
        adminName: {
            type: String,
            default: "Admin",
        },
    },
    setup(props) {
        const open = ref(false);
        const dropdownRef = ref(null);

        const initials = computed(() =>
            props.adminName
                .split(" ")
                .map((w) => w[0])
                .slice(0, 2)
                .join("")
                .toUpperCase(),
        );

        const toggle = () => {
            open.value = !open.value;
        };

        const handleClickOutside = (e) => {
            if (!dropdownRef.value) return;
            if (!dropdownRef.value.contains(e.target)) {
                open.value = false;
            }
        };

        onMounted(() => {
            document.addEventListener("click", handleClickOutside);
        });

        onBeforeUnmount(() => {
            document.removeEventListener("click", handleClickOutside);
        });

        return {
            open,
            toggle,
            dropdownRef,
            initials,
        };
    },
};
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
