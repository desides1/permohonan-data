export const sidebarMenu = [
    {
        label: "Beranda",
        icon: "House",
        route: "admin.beranda",
    },
    {
        label: "Data Permohonan",
        icon: "FileText",
        href: "#",
    },
    {
        label: "Catatan Aktivitas",
        icon: "Clock",
        children: [
            {
                label: "Aktivitas Permohonan Data",
                href: "#",
            },
            {
                label: "Aktivitas Notifikasi",
                href: "#",
            },
            { label: "Aktivitas Pengguna", href: "#" },
        ],
    },
    {
        label: "Admin",
        icon: "User",
        href: "#",
    },
    {
        label: "Pencadangan",
        icon: "DatabaseBackup",
        href: "#",
    },
    {
        label: "Keluar",
        icon: "LogOut",
        href: "#",
    },
];
