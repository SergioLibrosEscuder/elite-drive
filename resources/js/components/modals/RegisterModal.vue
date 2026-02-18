<!-- Guillermo Soto -->

<script setup>
import { reactive, computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useToast } from '../../composables/useToast';
import { useRouter } from 'vue-router';

// Created used objects
const toast = useToast();
const router = useRouter();
const loading = ref(false);

const form = reactive({
    dni: '',
    first_name: '',
    last_name: '',
    second_last_name: '',
    birth_date: '',
    address: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: ''
});

// Age validation
const age = computed(() => {
    if (!form.birth_date) return null;
    const birth = new Date(form.birth_date);
    const today = new Date();
    // Calculate age based on birthdate
    let ageCalc = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) {
        ageCalc--;
    }
    return ageCalc;
});

const isUnderage = computed(() => age.value !== null && age.value < 18);

onMounted(() => {
    // Reset register form if modal is closed
    document.getElementById("registerModal")?.addEventListener('hidden.bs.modal', resetForm)
})

// Registration method handling
const handleRegister = async () => {
    loading.value = true;
    // Make sure user isn't under legal age of conduction
    if (isUnderage.value) {
        toast.warning("You must be 18+ to register.", "Age Validation");
        return;
    }

    try {
        // Create user based on registration info
        const response = await axios.post('/register', form);
        toast.success("Account created! Please check your email to verify it.", "Verification Sent");
        // Close modal on registration
        closeModal();
        // Redirect to home page
        router.push("/")

    } catch (e) {
        // Handle errors
        if (e.response && e.response.data.errors) {
            toast.error("Error: " + Object.values(e.response.data.errors).flat().join(", "), "Registration Error");
        } else {
            toast.error("An error occurred during registration.", "Registration Error");
        }
    } finally {
        loading.value = false;
    }
}
// Close modal function
const closeModal = () => {
    const closeBtn = document.querySelector("#registerModal .btn-close");
    if (closeBtn) {
        closeBtn.click();
    }
}

// Reset modal function
const resetForm = () => {
    form.dni = '';
    form.first_name = '';
    form.last_name = '';
    form.second_last_name = '';
    form.birth_date = '';
    form.address = '';
    form.phone = '';
    form.email = '';
    form.password = '';
    form.password_confirmation = '';
}
</script>

<template>

    <!-- REGISTER MODAL ============================================================================= -->

    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Join Elite Drive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- REGISTER FORM ============================================================= -->

                    <form class="row g-3" @submit.prevent="handleRegister">
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-card-text me-2"></i> DNI
                            </label>
                            <input type="text" v-model="form.dni" class="form-control" placeholder="12345678A" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-person me-2"></i> Name
                            </label>
                            <input type="text" v-model="form.first_name" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-person me-2"></i> First surname
                            </label>
                            <input type="text" v-model="form.last_name" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-person me-2"></i> Second surname
                            </label>
                            <input type="text" v-model="form.second_last_name" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-calendar me-2"></i> Birthday
                            </label>
                            <input type="date" v-model="form.birth_date" class="form-control"
                                :class="{ 'is-invalid': isUnderage }" required>
                            <!-- Message if user isn't at least 18yo -->
                            <div v-if="isUnderage" class="invalid-feedback">You must be at least 18 years old.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-geo-alt me-2"></i> Address
                            </label>
                            <input type="text" v-model="form.address" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-phone me-2"></i> Phone
                            </label>
                            <input type="tel" v-model="form.phone" class="form-control" minlength="9" maxlength="9"
                                pattern="^\d{9}$" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-envelope-at me-2"></i> Email
                            </label>
                            <input type="email" v-model="form.email" class="form-control"
                                pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                oninvalid="this.setCustomValidity('Invalid email address!');"
                                oninput="this.setCustomValidity('')" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-key me-2"></i> Password
                            </label>
                            <input type="password" v-model="form.password" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-key me-2"></i> Confirm password
                            </label>
                            <input type="password" v-model="form.password_confirmation" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" :disabled="isUnderage || loading"
                                class="btn bg-primary-cta w-100 mt-3">{{ loading ? 'Creating Account...'
                                    : 'Create Account' }}</button>

                        </div>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        <p class="mb-2">Already have an account?</p>
                        <button class="btn btn-outline-light w-100" data-bs-target="#loginModal" data-bs-toggle="modal">
                            <i class="bi bi-person-circle me-2"></i> Login here
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>