// Sergio Libros

import { ref } from "vue";

// Reference array with all toasts
const toasts = ref([]);

export function useToast() {
    // Function to add a toast to array
    const add = (properties) => {
        const id = Date.now() + Math.random();

        toasts.value.push({
            id,
            title: properties.title || "Notification",
            message: properties.message,
            type: properties.type || "primary",
        });
    };

    // Function to remove a concrete toast from array
    const remove = (id) => {
        toasts.value = toasts.value.filter((t) => t.id !== id);
    };

    // Different types of toast to apply different styles
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
