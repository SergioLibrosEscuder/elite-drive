<script setup>
import { useToast } from '../../composables/useToast';
import BootstrapToast from './BootstrapToast.vue';

const { toasts, remove } = useToast();
</script>

<template>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">

        <BootstrapToast v-for="item in toasts" :key="item.id" :toast="item" @close="remove" />

    </div>
</template>