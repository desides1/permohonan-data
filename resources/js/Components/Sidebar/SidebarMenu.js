export const sidebarMenu = [
    {
        label: "Beranda",
        icon: "House",
        href: "/admin",
    },
    {
        label: "Data Permohonan",
        icon: "FileText",
        href: "/admin/permohonan",
    },
    {
        label: "Catatan Aktivitas",
        icon: "Clock",
        children: [
            { label: "Aktivitas Permohonan Data", href: "#" },
            { label: "Aktivitas Notifikasi", href: "#" },
            { label: "Aktivitas Pengguna", href: "#" },
        ],
    },
    {
        label: "Admin",
        icon: "User",
        href: "/admin/users",
    },
    {
        label: "Pencadangan",
        icon: "DatabaseBackup",
        href: "/admin/backup",
    },
    {
        label: "Keluar",
        icon: "LogOut",
        href: "/admin/logout",
    },
];
