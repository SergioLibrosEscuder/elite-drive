<!-- Sergio Libros -->

<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    toast: Object
});

// Define close emit
const emit = defineEmits(['close']);

// Element reference in DOM
const el = ref(null);
// Bootstrap instance
let bsToast = null;

onMounted(() => {
    // Create bootstrap toast instance
    if (window.bootstrap) {
        bsToast = new window.bootstrap.Toast(el.value, {
            autohide: true,
            delay: 5000
        });

        // Close toast method
        el.value.addEventListener('hidden.bs.toast', () => {
            emit('close', props.toast.id);
        });

        bsToast.show();
    } else {
        console.error("Bootstrap JS no está cargado");
    }
});

onBeforeUnmount(() => {
    if (bsToast) bsToast.dispose();
});
</script>

<!-- TOAST ELEMENT -->
<template>
    <!-- TOAST BODY TEMPLTE -->
    <div ref="el" class="toast toast-complete" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <!-- Change color depending on declared type -->
            <div class="rounded me-2" :class="`bg-${toast.type}`" style="width: 20px; height: 20px;"></div>

            <strong class="me-auto toast-title">{{ toast.title }}</strong>
            <small class="text-light">Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body text-light">
            {{ toast.message }}
        </div>
    </div>
</template>

<style scoped>
.toast-complete {
    border: 1px solid var(--secondary-color);

    .toast-header {
        background-color: var(--primary-color);

        & .toast-title {
            color: var(--secondary-color)
        }
    }

    .toast-body {
        background-color: var(--primary-light-color);
    }
}
</style>