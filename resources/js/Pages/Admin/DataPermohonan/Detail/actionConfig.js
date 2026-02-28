export const actionConfig = {
    verify: {
        title: "Verifikasi Permohonan",
        message:
            "Apakah Anda yakin ingin memverifikasi dan meneruskan ke Pimpinan BPKH?",
        color: "green",
        needsReason: false,
    },
    approve: {
        title: "Setujui Permohonan",
        message:
            "Apakah Anda yakin ingin menyetujui dan meneruskan ke Pimpinan PPKH?",
        color: "green",
        needsReason: false,
    },
    markReady: {
        title: "Tandai Data Siap",
        message: "Apakah Anda yakin data sudah siap untuk direview?",
        color: "green",
        needsReason: false,
    },
    reviewPpkh: {
        title: "Review Data",
        message: "Mulai review data permohonan ini?",
        color: "blue",
        needsReason: false,
    },
    forwardToBpkh: {
        title: "Teruskan ke Pimpinan BPKH",
        message:
            "Data sudah direview. Teruskan ke Pimpinan BPKH untuk persetujuan final?",
        color: "green",
        needsReason: false,
    },
    requestRevision: {
        title: "Minta Revisi",
        message: "Jelaskan alasan revisi:",
        color: "orange",
        needsReason: true,
    },
    finalApprove: {
        title: "Persetujuan Final",
        message: "Apakah Anda yakin memberikan persetujuan final?",
        color: "green",
        needsReason: false,
    },
    finalize: {
        title: "Selesaikan Permohonan",
        message: "Apakah Anda yakin ingin menyelesaikan permohonan ini?",
        color: "green",
        needsReason: false,
    },
    reject: {
        title: "Tolak Permohonan",
        message: "Jelaskan alasan penolakan:",
        color: "red",
        needsReason: true,
    },
};

export const routeMap = {
    verify: "admin.tickets.verify",
    approve: "admin.tickets.approve",
    assign: "admin.tickets.assign",
    markReady: "admin.tickets.markReady",
    reviewPpkh: "admin.tickets.reviewPpkh",
    forwardToBpkh: "admin.tickets.forwardToBpkh",
    requestRevision: "admin.tickets.requestRevision",
    finalApprove: "admin.tickets.finalApprove",
    finalize: "admin.tickets.finalize",
    reject: "admin.tickets.reject",
};
