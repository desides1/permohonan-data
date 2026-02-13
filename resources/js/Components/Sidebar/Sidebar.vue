<script setup>
import { inject } from "vue";
import {
    House,
    FileText,
    Clock,
    User,
    DatabaseBackup,
    LogOut,
} from "lucide-vue-next";
import { sidebarMenu } from "./SidebarMenu.js";

const closeSidebar = inject("closeSidebar", null);
const icons = {
    House,
    FileText,
    Clock,
    User,
    DatabaseBackup,
    LogOut,
};
</script>

<template>
    <aside
        class="h-full flex flex-col w-56 bg-sidebar text-sidebar-foreground border-r border-sidebar-border px-4 py-5"
    >
        <div class="text-base font-semibold mb-5 tracking-wide">BPKH Admin</div>

        <nav class="flex-1 flex flex-col gap-1.5">
            <template v-for="item in sidebarMenu" :key="item.label">
                <!-- MENU DENGAN SUBMENU -->
                <details v-if="item.children" class="group">
                    <summary class="dropdown-trigger">
                        <div class="flex items-center gap-3">
                            <component :is="icons[item.icon]" class="icon" />
                            <span class="text-sm font-medium">
                                {{ item.label }}
                            </span>
                        </div>

                        <svg
                            class="w-4 h-4 text-sidebar-foreground/60 transition-transform duration-200 group-open:rotate-180"
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
                    </summary>

                    <div class="submenu-line">
                        <a
                            v-for="child in item.children"
                            :key="child.label"
                            :href="child.href"
                            class="submenu-item"
                            @click="closeSidebar && closeSidebar()"
                        >
                            {{ child.label }}
                        </a>
                    </div>
                </details>

                <!-- MENU TANPA SUBMENU -->
                <a
                    v-else
                    :href="item.href"
                    class="menu-item"
                    @click="closeSidebar && closeSidebar()"
                >
                    <component :is="icons[item.icon]" class="icon" />
                    <span class="text-sm font-medium">
                        {{ item.label }}
                    </span>
                </a>
            </template>
        </nav>

        <div class="mt-6 text-xs text-sidebar-foreground/50">© BPKH</div>
    </aside>
</template>

<style scoped>
/* ===============================
   MENU UTAMA
   =============================== */
.menu-item {
    @apply flex items-center gap-3 px-3 py-2.5 rounded-md
           text-sidebar-foreground/70
           hover:text-sidebar-foreground
           hover:bg-sidebar-accent/10
           transition-colors;
}

/* ===============================
   DROPDOWN TRIGGER
   =============================== */
.dropdown-trigger {
    @apply flex items-center justify-between px-3 py-2.5 rounded-md
           text-sidebar-foreground/70
           hover:text-sidebar-foreground
           hover:bg-sidebar-accent/10
           cursor-pointer transition-colors;
}

/* ===============================
   ICON
   =============================== */
.icon {
    @apply w-5 h-5 text-sidebar-foreground/60;
}

.menu-item:hover .icon,
.dropdown-trigger:hover .icon,
.group-open .icon {
    color: #aedf33;
}

/* ===============================
   SUBMENU
   =============================== */
.submenu-line {
    @apply ml-5 pl-4 mt-1 space-y-1
           border-l border-sidebar-border;
}

.submenu-item {
    @apply block py-1.5 text-sm
           text-sidebar-foreground/60
           hover:text-sidebar-foreground
           transition-colors;
}
</style>
