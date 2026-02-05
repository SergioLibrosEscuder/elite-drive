import { ref } from "vue";

const toasts = ref([]);

export function useToast() {
    const add = (properties) => {
        const id = Date.now() + Math.random();

        toasts.value.push({
            id,
            title: properties.title || "Notification",
            message: properties.message,
            type: properties.type || "primary",
        });
    };

    const remove = (id) => {
        toasts.value = toasts.value.filter((t) => t.id !== id);
    };

    const success = (msg, title = "Success") =>
        add({ message: msg, type: "success", title });
    const error = (msg, title = "Error") =>
        add({ message: msg, type: "danger", title });
    const warning = (msg, title = "Warning") =>
        add({ message: msg, type: "warning", title });
    const info = (msg, title = "Info") =>
        add({ message: msg, type: "info", title });

    return { toasts, add, remove, success, error, warning, info };
}
