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
                        <button type="submit" class="btn bg-primary-cta w-100" :disabled="loading">
                            <i class="bi bi-person-circle me-2"></i>
                            {{ loading ? 'Signing In...' : 'Sign In' }}
                        </button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        <p class="mb-2">Don't have an account?</p>
                        <button class="btn btn-outline-light w-100" data-bs-target="#registerModal"
                            data-bs-toggle="modal">Register here</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuth } from '../../composables/useAuth';
import { useToast } from '../../composables/useToast';
import { useRouter } from 'vue-router';

const toast = useToast();
const router = useRouter();
const loading = ref(false);

// Creamos un objeto reactivo para los datos del formulario
const form = reactive({
    email: '',
    password: ''
});

const { login, isAdmin } = useAuth();

const handleLogin = async () => {
    loading.value = true;
    try {
        await login(form);
        closeModal();
        router.push(isAdmin.value ? "/admin" : "/profile");
    } catch (error) {
        // Si Laravel devuelve 419, es que el token CSRF ha fallado
        if (error.response && error.response.status === 419) {
            toast.warning("Session expired, please refresh the page", "Security")
        } else {
            toast.error("Invalid credentials, please try again", "Login Failed")
        }
    } finally {
        loading.value = false;
    }
};

const closeModal = () => {
    const closeBtn = document.querySelector("#loginModal .btn-close");

    if (closeBtn) {
        closeBtn.click();
    }
}

</script>