import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { actionConfig, routeMap } from "./actionConfig";

export function useActionDialog(getProgressId) {
    const confirmOpen = ref(false);
    const disposisiOpen = ref(false);
    const actionType = ref("");
    const actionReason = ref("");
    const isSubmitting = ref(false);

    const currentConfig = computed(() => actionConfig[actionType.value] || {});

    function openDialog(type) {
        actionType.value = type;
        actionReason.value = "";

        if (type === "assign") {
            disposisiOpen.value = true;
        } else {
            confirmOpen.value = true;
        }
    }

    function closeAllDialogs() {
        confirmOpen.value = false;
        disposisiOpen.value = false;
        isSubmitting.value = false;
    }

    function submitConfirm() {
        if (isSubmitting.value) return;
        isSubmitting.value = true;

        const id = getProgressId();
        const routeName = routeMap[actionType.value];
        if (!routeName) return;

        const data = {};
        if (
            actionType.value === "reject" ||
            actionType.value === "requestRevision"
        ) {
            data.reason = actionReason.value;
        }

        router.post(route(routeName, id), data, {
            onFinish: () => closeAllDialogs(),
        });
    }

    function submitDisposisi(payload) {
        if (isSubmitting.value) return;
        isSubmitting.value = true;

        const id = getProgressId();

        router.post(
            route(routeMap.assign, id),
            {
                petugas_id: payload.petugas_id,
                notes: payload.notes,
            },
            {
                onFinish: () => closeAllDialogs(),
            },
        );
    }

    return {
        confirmOpen,
        disposisiOpen,
        actionType,
        actionReason,
        isSubmitting,
        currentConfig,
        openDialog,
        closeAllDialogs,
        submitConfirm,
        submitDisposisi,
    };
}
