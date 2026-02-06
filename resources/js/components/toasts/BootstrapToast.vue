<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    toast: Object
});

const emit = defineEmits(['close']);

// Referencia al DOM de ESTE toast
const el = ref(null);
// Instancia de Bootstrap de ESTE toast
let bsToast = null;

onMounted(() => {
    if (window.bootstrap) {
        bsToast = new window.bootstrap.Toast(el.value, {
            autohide: true,
            delay: 5000
        });

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

<template>
    <div ref="el" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <div class="rounded me-2" :class="`bg-${toast.type}`" style="width: 20px; height: 20px;"></div>

            <strong class="me-auto">{{ toast.title }}</strong>
            <small class="text-muted">Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ toast.message }}
        </div>
    </div>
</template>