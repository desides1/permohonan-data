export const sidebarMenu = [
    {
        label: "Beranda",
        icon: "House",
        route: "admin.beranda",
    },
    {
        label: "Data Permohonan",
        icon: "FileText",
        route: "admin.tickets.index",
    },
    {
        label: "Catatan Aktivitas",
        icon: "Clock",
        children: [
            {
                label: "Aktivitas Permohonan Data",
                route: "admin.activity-log.request-data",
            },
            {
                label: "Aktivitas Notifikasi",
                route: "admin.activity-log.notification",
            },
            { label: "Aktivitas Pengguna", route: "admin.activity-log.user" },
        ],
    },
    {
        label: "Survei Kepuasan Masyarakat",
        icon: "Smile",
        route: "admin.feedback.index",
        roles: ["admin_tu", "pimpinan_bpkh"],
    },
    {
        label: "Admin",
        icon: "User",
        route: "profile.show",
    },
    {
        label: "Pencadangan",
        icon: "DatabaseBackup",
        route: "admin.backup.index",
        roles: ["admin_tu"],
    },
    {
        label: "Keluar",
        icon: "LogOut",
        route: "logout",
    },
];
