<!-- Guillermo Soto ============================================================================= -->

<template>

    <!-- RECOVER PASSWORD MODAL ===================================================================== -->

    <div class="modal fade" id="recoverPasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Restore your password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- RECOVER PASSWORD FORM ====================================================== -->

                    <form @submit.prevent="handleRecover">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-envelope-at me-2"></i> Email address
                            </label>
                            <input v-model="form.email" type="email" class="form-control" placeholder="name@example.com"
                                pattern="^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$"
                                @invalid="$event.target.setCustomValidity('Invalid email address!');"
                                @input="$event.target.setCustomValidity('')" title="Please enter a valid email address"
                                required>
                        </div>

                        <button type="submit" class="btn bg-primary-cta w-100" :disabled="loading">
                            {{ loading ? 'Sending...' : 'Send' }}
                        </button>
                    </form>

                    <!-- Redirect to login modal -->
                    <div class="text-center mt-4 border-top pt-3">
                        <p class="mb-2">Have you remembered?</p>
                        <button class="btn btn-outline-light w-100" data-bs-target="#loginModal" data-bs-toggle="modal">
                            <i class="bi bi-person-circle me-2"></i> Login here
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useToast } from '../../composables/useToast';
import axios from 'axios';

// Created used objects
const toast = useToast();
const loading = ref(false);
const form = reactive({ email: '' });

onMounted(() => {
    // If modal is closed, info is erased
    document.getElementById("recoverPasswordModal")?.addEventListener('hidden.bs.modal', resetForm)
})

// Handle recover password function
const handleRecover = async () => {
    loading.value = true;
    try {
        // Send recover password request to server
        const response = await axios.post('/forgot-password', { email: form.email });
        // Show success message and close modal
        toast.success(response.data.message, "Request Sent");
        closeModal();
    } catch (error) {
        // Show error message
        toast.error("User not found or server error", "Error");
    } finally {
        loading.value = false;
    }
};

// Close modal function
const closeModal = () => {
    const closeBtn = document.querySelector("#recoverPasswordModal .btn-close");

    if (closeBtn) {
        closeBtn.click();
    }
}

// Reset form function
const resetForm = () => {
    form.email = '';
}

</script>