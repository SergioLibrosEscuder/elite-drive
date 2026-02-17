<template>
    <div class="container py-5 mt-5 mb-5">
        <div class="row justify-content-center mb-3">
            <div class="col-md-5">
                <div class="shadow-lg panel-content">
                    <div class="panel-header color-secondary border-0 p-3">
                            <h5 class="modal-title fw-bold">Set New Password</h5>
                        </div>
                    <div class="p-4">                     
                        <form @submit.prevent="handleReset">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-envelope-at me-2"></i> Email Address
                                </label>
                                <input v-model="form.email" type="email" class="form-control" required readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-key me-2"></i> New Password
                                </label>
                                <input v-model="form.password" type="password" class="form-control" required minlength="8">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-key me-2"></i> Confirm New Password
                                </label>
                                <input v-model="form.password_confirmation" type="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn bg-primary-cta w-100" :disabled="loading">
                                {{ loading ? 'Updating...' : 'Reset Password' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from '../composables/useToast';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const loading = ref(false);

const form = reactive({
    token: route.params.token,
    email: route.query.email, // El email suele venir como parámetro en la URL del mail
    password: '',
    password_confirmation: ''
});

const handleReset = async () => {
    loading.value = true;
    try {
        await axios.post('/reset-password', form);
        toast.success("Password updated! You can now login.", "Success");
        router.push('/'); // Redirigir a la home para que loguee
    } catch (error) {
        toast.error("The link expired or data is invalid.", "Error");
    } finally {
        loading.value = false;
    }
};
</script>