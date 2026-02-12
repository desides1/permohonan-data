<template>
    <div class="min-h-screen flex bg-slate-50 text-slate-800">
        <!-- Sidebar -->
        <aside
            class="hidden md:flex md:flex-col w-56 bg-slate-900 text-slate-100 p-5"
        >
            <div class="text-lg font-semibold mb-4">BPKH Admin</div>
            <nav class="flex-1 flex flex-col gap-2">
                <a
                    href="#"
                    class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-3"
                >
                    <svg class="w-5 h-5 text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 21V10.5h14V21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Beranda</span>
                </a>

                <a
                    href="#"
                    class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-3"
                >
                    <svg class="w-5 h-5 text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12h6M9 16h6M5 8h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    <span>Data Permohonan</span>
                </a>

                <details class="group">
                    <summary
                        class="flex items-center justify-between px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white cursor-pointer"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-300" viewBox="0 0 24 24" fill="none">
                                <path d="M12 8v4l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Catatan Aktivitas</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none">
                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </summary>

                    <div class="mt-1 ml-3 flex flex-col gap-1">
                        <a href="#" class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white text-sm">
                            Aktivitas Permohonan Data
                        </a>
                        <a href="#" class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white text-sm">
                            Aktivitas Notifikasi
                        </a>
                        <a href="#" class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white text-sm">
                            Aktivitas Pengguna
                        </a>
                    </div>
                </details>

                <a
                    href="#"
                    class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-3"
                >
                    <svg class="w-5 h-5 text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 21v-1a4 4 0 014-4h8a4 4 0 014 4v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Admin</span>
                </a>

                <a
                    href="#"
                    class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-3"
                >
                    <svg class="w-5 h-5 text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M16 16v4H8v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 12v8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 7v6a4 4 0 01-4 4H8a4 4 0 01-4-4V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Pencadangan</span>
                </a>

                <a
                    href="#"
                    class="px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-3"
                >
                    <svg class="w-5 h-5 text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M15 3h4a1 1 0 011 1v16a1 1 0 01-1 1h-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 17L15 12 10 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 12H3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Keluar</span>
                </a>
            </nav>
            <div class="text-sm text-slate-400 mt-4">© BPKH</div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header
                class="h-16 flex items-center justify-between px-4 md:px-6 bg-white shadow-sm"
            >
                <div class="flex items-center gap-3">
                    <button
                        class="md:hidden p-2 rounded-md hover:bg-slate-100"
                        @click="$emit('toggle-sidebar')"
                        aria-label="Toggle menu"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M4 6h16M4 12h16M4 18h16"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold">
                        <slot name="title">Dashboard</slot>
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="relative p-2 rounded-md hover:bg-slate-100"
                        aria-label="Notifications"
                    >
                        <svg
                            class="w-5 h-5 text-slate-600"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M15 17H9"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                            />
                            <path
                                d="M12 3v1"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                            />
                            <path
                                d="M18 8a6 6 0 10-12 0v4l-1 1v1h16v-1l-1-1V8"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        <span
                            v-if="notificationsCount > 0"
                            class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs px-2 py-0.5 rounded-full"
                            >{{ notificationsCount }}</span
                        >
                    </button>

                    <div class="relative" ref="profileRef">
                        <button
                            class="flex items-center gap-3 px-2 py-1 rounded-md hover:bg-slate-100"
                            @click="toggleDropdown"
                            :aria-expanded="dropdownOpen"
                            aria-haspopup="true"
                        >
                            <div
                                class="w-8 h-8 rounded-full bg-slate-200 text-slate-900 flex items-center justify-center font-semibold"
                            >
                                {{ initials }}
                            </div>
                            <div class="hidden sm:block">
                                <div class="text-sm font-medium">
                                    {{ adminName }}
                                </div>
                            </div>
                            <svg
                                class="w-3 h-3 text-slate-500"
                                viewBox="0 0 24 24"
                                fill="none"
                            >
                                <path
                                    d="M6 9l6 6 6-6"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>

                        <transition name="fade">
                            <div
                                v-if="dropdownOpen"
                                class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg ring-1 ring-slate-100 z-50"
                            >
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-slate-700 hover:bg-slate-50"
                                    >Profil</a
                                >
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-slate-700 hover:bg-slate-50"
                                    >Pengaturan</a
                                >
                                <div class="border-t border-slate-100"></div>
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-rose-600 hover:bg-slate-50"
                                    >Keluar</a
                                >
                            </div>
                        </transition>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-auto p-6 bg-slate-50">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

export default {
    name: "LayoutDashboard",
    props: {
        adminName: { type: String, default: "Admin" },
        notificationsCount: { type: Number, default: 0 },
    },
    setup(props, { emit }) {
        const dropdownOpen = ref(false);
        const profileRef = ref(null);

        const initials = computed(() =>
            props.adminName
                .split(" ")
                .map((s) => s.charAt(0).toUpperCase())
                .slice(0, 2)
                .join(""),
        );

        const toggleDropdown = () => {
            dropdownOpen.value = !dropdownOpen.value;
        };

        const onClickOutside = (e) => {
            if (!profileRef.value) return;
            if (!profileRef.value.contains(e.target))
                dropdownOpen.value = false;
        };

        onMounted(() => document.addEventListener("click", onClickOutside));
        onBeforeUnmount(() =>
            document.removeEventListener("click", onClickOutside),
        );

        return { dropdownOpen, toggleDropdown, profileRef, initials };
    },
};
</script>
