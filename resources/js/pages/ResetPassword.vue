<!-- Guillermo Soto ============================================================================= -->

<template>

    <!-- DIVIDER ============================================================== -->

    <section class="mt-5 mb-5">
        <div class="container">
            <div class="d-flex justify-content-center">
                <img :src="'/images/decorations/divider.png'" alt="Divider" class="w-50">
            </div>
        </div>
    </section>

    <!-- SET NEW PASSWORD FORM ================================================ -->

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
                                <input v-model="form.password" type="password" id="password" class="form-control"
                                    required minlength="8">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-key me-2"></i> Confirm New Password
                                </label>
                                <input v-model="form.password_confirmation" type="password" class="form-control"
                                    minlength="8"
                                    @input="$event.target.setCustomValidity($event.target.value != form.password ? 'Passwords do not match.' : '')"
                                    title="Passwords must match" required>
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

    <!-- DIVIDER ============================================================== -->

    <section class="mb-5">
        <div class="container">
            <div class="d-flex justify-content-center">
                <img :src="'/images/decorations/divider.png'" alt="Divider" class="w-50">
            </div>
        </div>
    </section>

</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from '../composables/useToast';
import axios from 'axios';

// State and utilities for password reset
const route = useRoute();
const router = useRouter();
const toast = useToast();
const loading = ref(false);

// Form data for password reset
const form = reactive({
    token: route.params.token,
    email: route.query.email,
    password: '',
    password_confirmation: ''
});

// Function to handle password reset submission
const handleReset = async () => {
    loading.value = true;
    try {
        // Send reset request to API
        await axios.post('/reset-password', form);
        toast.success("Password updated! You can now login.", "Success");
        // Redirect to home page after successful reset
        router.push('/');
    } catch (error) {
        toast.error("The link expired or data is invalid.", "Error");
    } finally {
        loading.value = false;
    }
};
</script>