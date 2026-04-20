<script setup>
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import {
    Search,
    Mail,
    Phone,
    CheckCircle,
    XCircle,
    Clock,
    RefreshCw,
    Eye,
} from "lucide-vue-next";

const props = defineProps({
    notifications: Object,
    filters: Object,
});

const flash = computed(() => usePage().props.flash ?? {});
const search = ref(props.filters?.search ?? "");
const channel = ref(props.filters?.channel ?? "");
const status = ref(props.filters?.status ?? "");
const retrying = ref(null);

let debounce = null;

function applyFilters() {
    router.get(
        route("admin.activity-log.notification"),
        {
            search: search.value || undefined,
            channel: channel.value || undefined,
            status: status.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

watch(search, () => {
    clearTimeout(debounce);
    debounce = setTimeout(applyFilters, 400);
});

watch([channel, status], applyFilters);

function retryNotification(id) {
    retrying.value = id;
    router.post(
        route("admin.activity-log.notification.retry", id),
        {},
        {
            preserveScroll: true,
            onFinish: () => (retrying.value = null),
        },
    );
}

const statusConfig = {
    sent: {
        label: "Terkirim",
        class: "bg-green-100 text-green-700",
        icon: CheckCircle,
    },
    failed: { label: "Gagal", class: "bg-red-100 text-red-700", icon: XCircle },
    pending: {
        label: "Menunggu",
        class: "bg-yellow-100 text-yellow-700",
        icon: Clock,
    },
};

const typeLabels = {
    ticket_submitted: "Tiket Dibuat",
    status_changed: "Status Berubah",
    assignment_notification: "Penugasan",
    new_ticket: "Tiket Baru",
};
</script>

<template>
    <LayoutDashboard>
        <section class="space-y-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">
                    Aktivitas Notifikasi
                </h1>
                <p class="text-sm text-slate-500">
                    Monitor notifikasi Email & WhatsApp dari N8N
                </p>
            </div>

            <!-- Flash -->
            <div
                v-if="flash.success"
                class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800"
            >
                {{ flash.success }}
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 max-w-sm">
                    <Search
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    />
                    <Input
                        v-model="search"
                        placeholder="Cari kode tiket, email, telepon..."
                        class="pl-9"
                    />
                </div>
                <Select v-model="channel">
                    <SelectTrigger class="w-full sm:w-40">
                        <SelectValue placeholder="Semua Channel" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">Semua Channel</SelectItem>
                        <SelectItem value="email">Email</SelectItem>
                        <SelectItem value="whatsapp">WhatsApp</SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="status">
                    <SelectTrigger class="w-full sm:w-40">
                        <SelectValue placeholder="Semua Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">Semua Status</SelectItem>
                        <SelectItem value="sent">Terkirim</SelectItem>
                        <SelectItem value="failed">Gagal</SelectItem>
                        <SelectItem value="pending">Menunggu</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode Tiket</th>
                                <th class="px-4 py-3 text-left">Event</th>
                                <th class="px-4 py-3 text-left">Channel</th>
                                <th class="px-4 py-3 text-left">Penerima</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="n in notifications.data"
                                :key="n.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ n.ticket_code }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ typeLabels[n.type] ?? n.type }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5"
                                    >
                                        <component
                                            :is="
                                                n.channel === 'email'
                                                    ? Mail
                                                    : Phone
                                            "
                                            class="w-3.5 h-3.5"
                                            :class="
                                                n.channel === 'email'
                                                    ? 'text-blue-500'
                                                    : 'text-green-500'
                                            "
                                        />
                                        {{
                                            n.channel === "email"
                                                ? "Email"
                                                : "WhatsApp"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-600 max-w-[180px] truncate"
                                >
                                    {{ n.recipient }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="statusConfig[n.status]?.class"
                                    >
                                        <component
                                            :is="statusConfig[n.status]?.icon"
                                            class="w-3 h-3"
                                        />
                                        {{ statusConfig[n.status]?.label }}
                                    </span>
                                    <p
                                        v-if="n.error_message"
                                        class="text-xs text-red-500 mt-1 max-w-xs truncate"
                                    >
                                        {{ n.error_message }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div
                                        class="flex items-center justify-center gap-1"
                                    >
                                        <Button
                                            v-if="
                                                n.status === 'failed' ||
                                                n.status === 'pending'
                                            "
                                            variant="destructive"
                                            size="sm"
                                            :disabled="retrying === n.id"
                                            @click="retryNotification(n.id)"
                                            class="text-xs"
                                        >
                                            <RefreshCw
                                                class="w-3.5 h-3.5"
                                                :class="{
                                                    'animate-spin':
                                                        retrying === n.id,
                                                }"
                                            />
                                            Kirim Ulang
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="notifications.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-4 py-8 text-center text-gray-400"
                                >
                                    Belum ada data notifikasi.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="notifications.last_page > 1"
                    class="flex items-center justify-between border-t px-4 py-3"
                >
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ notifications.from }}-{{
                            notifications.to
                        }}
                        dari {{ notifications.total }}
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in notifications.links"
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
