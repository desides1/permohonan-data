<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/Components/ui/dialog";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import { Progress } from "@/Components/ui/progress";
import {
    Upload,
    FileUp,
    FileCheck2,
    X,
    AlertCircle,
    CheckCircle2,
    Loader2,
    Trash2,
    File,
    FileText,
    FileSpreadsheet,
    FileImage,
    FileArchive,
} from "lucide-vue-next";

const props = defineProps({
    ticketCode: {
        type: String,
        required: true,
    },
    ticketName: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["uploaded"]);

const isOpen = ref(false);
const isDragging = ref(false);
const uploadProgress = ref(0);
const fileInputRef = ref(null);

const form = useForm({
    files: [],
    keterangan: "",
});

const maxFileSize = 50 * 1024 * 1024; // 50MB
const allowedExtensions = [
    ".pdf",
    ".doc",
    ".docx",
    ".xls",
    ".xlsx",
    ".csv",
    ".jpg",
    ".jpeg",
    ".png",
    ".zip",
    ".rar",
    ".7z",
];

const totalFileSize = computed(() => {
    return form.files.reduce((total, file) => total + file.size, 0);
});

const formattedTotalSize = computed(() => {
    return formatFileSize(totalFileSize.value);
});

const hasFiles = computed(() => form.files.length > 0);

function formatFileSize(bytes) {
    if (bytes === 0) return "0 B";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

function getFileIcon(file) {
    const ext = "." + file.name.split(".").pop().toLowerCase();
    if ([".pdf"].includes(ext)) return FileText;
    if ([".doc", ".docx"].includes(ext)) return FileText;
    if ([".xls", ".xlsx", ".csv"].includes(ext)) return FileSpreadsheet;
    if ([".jpg", ".jpeg", ".png"].includes(ext)) return FileImage;
    if ([".zip", ".rar", ".7z"].includes(ext)) return FileArchive;
    return File;
}

function getFileColor(file) {
    const ext = "." + file.name.split(".").pop().toLowerCase();
    if ([".pdf"].includes(ext)) return "text-red-500";
    if ([".doc", ".docx"].includes(ext)) return "text-blue-500";
    if ([".xls", ".xlsx", ".csv"].includes(ext)) return "text-green-500";
    if ([".jpg", ".jpeg", ".png"].includes(ext)) return "text-purple-500";
    if ([".zip", ".rar", ".7z"].includes(ext)) return "text-yellow-600";
    return "text-gray-500";
}

function validateFile(file) {
    const ext = "." + file.name.split(".").pop().toLowerCase();
    if (!allowedExtensions.includes(ext)) {
        return `Format file "${file.name}" tidak didukung.`;
    }
    if (file.size > maxFileSize) {
        return `Ukuran file "${file.name}" melebihi batas 50MB.`;
    }
    if (form.files.some((f) => f.name === file.name && f.size === file.size)) {
        return `File "${file.name}" sudah ditambahkan.`;
    }
    return null;
}

function handleFiles(fileList) {
    const errors = [];
    const validFiles = [];

    Array.from(fileList).forEach((file) => {
        const error = validateFile(file);
        if (error) {
            errors.push(error);
        } else {
            validFiles.push(file);
        }
    });

    if (errors.length > 0) {
        alert(errors.join("\n"));
    }

    form.files = [...form.files, ...validFiles];
}

function onFileChange(event) {
    handleFiles(event.target.files);
    if (fileInputRef.value) {
        fileInputRef.value.value = "";
    }
}

function removeFile(index) {
    form.files.splice(index, 1);
}

function clearAllFiles() {
    form.files = [];
}

function onDragOver(event) {
    event.preventDefault();
    isDragging.value = true;
}

function onDragLeave() {
    isDragging.value = false;
}

function onDrop(event) {
    event.preventDefault();
    isDragging.value = false;
    handleFiles(event.dataTransfer.files);
}

function openFileDialog() {
    fileInputRef.value?.click();
}

function submitUpload() {
    if (!hasFiles.value) return;

    const formData = new FormData();
    form.files.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });
    formData.append("keterangan", form.keterangan);

    form.post(route("admin.tickets.uploadHasil", props.ticketCode), {
        forceFormData: true,
        onProgress: (progress) => {
            uploadProgress.value = progress.percentage ?? 0;
        },
        onSuccess: () => {
            resetForm();
            isOpen.value = false;
            emit("uploaded");
        },
        onError: () => {
            uploadProgress.value = 0;
        },
    });
}

function resetForm() {
    form.reset();
    form.clearErrors();
    uploadProgress.value = 0;
    isDragging.value = false;
}

function onOpenChange(val) {
    isOpen.value = val;
    if (!val) {
        resetForm();
    }
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="onOpenChange">
        <DialogTrigger as-child>
            <Button
                variant="default"
                size="sm"
                class="gap-2"
                @click="isOpen = true"
            >
                <Upload class="w-4 h-4" />
                <span class="hidden sm:inline">Upload Hasil Data</span>
                <span class="sm:hidden">Upload</span>
            </Button>
        </DialogTrigger>

        <DialogContent
            class="w-[95vw] max-w-2xl max-h-[90vh] overflow-y-auto p-4 sm:p-6"
        >
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2 text-lg sm:text-xl">
                    <div
                        class="flex items-center justify-center w-9 h-9 rounded-full bg-primary/10"
                    >
                        <FileUp class="w-5 h-5 text-primary" />
                    </div>
                    Upload Hasil Permohonan Data
                </DialogTitle>
                <DialogDescription class="text-sm">
                    Upload file hasil permohonan data untuk tiket
                    <span class="font-semibold text-foreground">
                        {{ ticketCode }}
                    </span>
                    <span v-if="ticketName"> — {{ ticketName }}</span>
                </DialogDescription>
            </DialogHeader>

            <div class="mt-4 space-y-5">
                <!-- Drag & Drop Area -->
                <div>
                    <Label class="text-sm font-medium mb-2 block">
                        File Hasil Data <span class="text-destructive">*</span>
                    </Label>

                    <div
                        @dragover="onDragOver"
                        @dragleave="onDragLeave"
                        @drop="onDrop"
                        @click="openFileDialog"
                        :class="[
                            'relative flex flex-col items-center justify-center w-full rounded-xl border-2 border-dashed cursor-pointer transition-all duration-300 p-6 sm:p-8',
                            isDragging
                                ? 'border-primary bg-primary/5'
                                : 'border-muted-foreground/25 hover:border-primary/50 hover:bg-muted/50',
                        ]"
                    >
                        <div
                            class="flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-3"
                        >
                            <Upload class="w-6 h-6 text-muted-foreground" />
                        </div>

                        <p class="text-sm sm:text-base font-medium text-center">
                            Klik atau seret file ke sini
                        </p>

                        <p
                            class="text-xs text-muted-foreground mt-1.5 text-center"
                        >
                            PDF, DOC, XLS, JPG, PNG, ZIP — Maks. 50MB per file
                        </p>

                        <input
                            ref="fileInputRef"
                            type="file"
                            multiple
                            :accept="allowedExtensions.join(',')"
                            class="hidden"
                            @change="onFileChange"
                        />
                    </div>

                    <p
                        v-if="form.errors.files"
                        class="flex items-center gap-1.5 text-sm text-destructive mt-2"
                    >
                        <AlertCircle class="w-4 h-4 flex-shrink-0" />
                        {{ form.errors.files }}
                    </p>
                </div>

                <!-- File List -->
                <div v-if="hasFiles" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <Label class="text-sm font-medium">
                            {{ form.files.length }} file ({{
                                formattedTotalSize
                            }})
                        </Label>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-destructive hover:text-destructive"
                            @click="clearAllFiles"
                        >
                            <Trash2 class="w-4 h-4 mr-1" />
                            Hapus Semua
                        </Button>
                    </div>

                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <div
                            v-for="(file, index) in form.files"
                            :key="index"
                            class="flex items-center gap-3 rounded-lg border bg-card p-2.5 sm:p-3 group hover:bg-muted/50 transition-colors"
                        >
                            <component
                                :is="getFileIcon(file)"
                                class="w-5 h-5 flex-shrink-0"
                                :class="getFileColor(file)"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">
                                    {{ file.name }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatFileSize(file.size) }}
                                </p>
                            </div>
                            <Button
                                variant="ghost"
                                size="sm"
                                class="opacity-0 group-hover:opacity-100 transition-opacity text-destructive hover:text-destructive h-8 w-8 p-0"
                                @click="removeFile(index)"
                            >
                                <X class="w-4 h-4" />
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="space-y-2">
                    <Label for="keterangan" class="text-sm font-medium">
                        Keterangan
                        <span class="text-muted-foreground font-normal"
                            >(Opsional)</span
                        >
                    </Label>
                    <Textarea
                        id="keterangan"
                        v-model="form.keterangan"
                        placeholder="Tambahkan catatan atau keterangan terkait file yang diupload..."
                        class="min-h-[80px] resize-none text-sm"
                        :disabled="form.processing"
                    />
                    <p
                        v-if="form.errors.keterangan"
                        class="flex items-center gap-1.5 text-sm text-destructive"
                    >
                        <AlertCircle class="w-4 h-4 flex-shrink-0" />
                        {{ form.errors.keterangan }}
                    </p>
                </div>

                <!-- Upload Progress -->
                <div v-if="form.processing" class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-primary">
                            <Loader2 class="w-4 h-4 animate-spin" />
                            Mengupload file...
                        </span>
                        <span class="font-medium">{{ uploadProgress }}%</span>
                    </div>
                    <Progress :model-value="uploadProgress" class="h-2" />
                </div>
            </div>

            <DialogFooter
                class="mt-6 flex flex-col-reverse sm:flex-row gap-2 sm:gap-3"
            >
                <Button
                    variant="outline"
                    class="w-full sm:w-auto"
                    :disabled="form.processing"
                    @click="isOpen = false"
                >
                    <X class="w-4 h-4 mr-2" />
                    Batal
                </Button>

                <Button
                    class="w-full sm:w-auto gap-2"
                    :disabled="!hasFiles || form.processing"
                    @click="submitUpload"
                >
                    <Loader2
                        v-if="form.processing"
                        class="w-4 h-4 animate-spin"
                    />
                    <CheckCircle2 v-else class="w-4 h-4" />
                    {{ form.processing ? "Mengupload..." : "Upload File" }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
