<!-- Guillermo Soto ============================================================================= -->

<template>

    <!-- LOGIN MODAL ================================================================================ -->

    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Login to Elite Drive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- LOGIN FORM ================================================================= -->

                    <form @submit.prevent="handleLogin">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-envelope-at me-2"></i> Email address
                            </label>
                            <input v-model="form.email" type="email" class="form-control" placeholder="name@example.com"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-key me-2"></i> Password
                            </label>
                            <input v-model="form.password" type="password" class="form-control" required>
                        </div>
                        <!-- Change appareance based on loading status -->
                        <button type="submit" class="btn bg-primary-cta w-100" :disabled="loading">
                            <i class="bi bi-person-circle me-2"></i>
                            {{ loading ? 'Signing In...' : 'Sign In' }}
                        </button>
                    </form>

                    <!-- Redirect to register modal -->
                    <div class="text-center mt-4 border-top pt-3">
                        <p class="mb-2">Don't have an account?</p>
                        <button class="btn btn-outline-light w-100" data-bs-target="#registerModal"
                            data-bs-toggle="modal">Register here</button>
                    </div>

                    <!-- Redirect to recover password modal -->
                    <div class="text-center mt-4 border-top pt-3">
                        <p class="mb-2">Forgot your password?</p>
                        <button class="btn btn-outline-light w-100" data-bs-target="#recoverPasswordModal"
                            data-bs-toggle="modal">Click here</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuth } from '../../composables/useAuth';
import { useToast } from '../../composables/useToast';
import { useRouter } from 'vue-router';

// Created used objects
const toast = useToast();
const router = useRouter();
const loading = ref(false);

// Reactive object to associate to form
const form = reactive({
    email: '',
    password: ''
});

// Get data from useAuth composable
const { login, isAdmin } = useAuth();

onMounted(() => {
    // If modal is closed, info is erased
    document.getElementById("loginModal")?.addEventListener('hidden.bs.modal', resetForm)
})

// Login method handling
const handleLogin = async () => {

    loading.value = true;
    try {
        // < !--Sergio Libros ============================================================================= -->

        // Await until composable's login method is completed
        await login(form);
        // Close modal on login
        closeModal();
        // Reset form on login
        resetForm();
        // Redirect into admin or profil page depending on role
        router.push(isAdmin.value ? "/admin" : "/profile");

        // Error handling
    } catch (error) {
        // Handle 419 (CSRF Token) error
        if (error.response && error.response.status === 419) {
            toast.warning("Session expired, please refresh the page", "Security")

            // Other error potentially means wrong credentials
        } else {
            toast.error("Invalid credentials, please try again", "Login Failed")
        }
    } finally {
        loading.value = false;
    }
};

// Close modal function
const closeModal = () => {
    const closeBtn = document.querySelector("#loginModal .btn-close");

    if (closeBtn) {
        closeBtn.click();
    }
}

// Reset form function
const resetForm = () => {
    form.email = '';
    form.password = '';
}

</script>