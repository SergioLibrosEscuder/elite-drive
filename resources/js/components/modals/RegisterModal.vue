<script setup>
    import { reactive, computed } from 'vue';
    import axios from 'axios';

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
        password_confirmation: '' // Importante para la validación de Laravel
    });

    // Validación de Edad (Igual que en Admin)
    const age = computed(() => {
        if (!form.birth_date) return null;
        const birth = new Date(form.birth_date);
        const today = new Date();
        let ageCalc = today.getFullYear() - birth.getFullYear();
        if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) {
            ageCalc--;
        }
        return ageCalc;
    });

    const isUnderage = computed(() => age.value !== null && age.value < 18);

    const handleRegister = async () => {
        if (isUnderage.value) {
            alert("You must be 18+ to register.");
            return;
        }

        try {
            const response = await axios.post('/register', form);
            alert("Registration successful! You can now login.");
            // Opcional: Cerrar modal y limpiar form
            location.reload(); 
        } catch (e) {
            if (e.response && e.response.data.errors) {
                alert("Error: " + Object.values(e.response.data.errors).flat().join(", "));
            } else {
                alert("An error occurred during registration.");
            }
        }
    }
</script>

<template>

    <!-- REGISTER MODAL ============================================================================= -->

    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
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
                            <input type="date" v-model="form.birth_date" class="form-control" :class="{'is-invalid': isUnderage}" required>
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
                            <input type="tel" v-model="form.phone" class="form-control"
                                minlength="9"
                                maxlength="9"
                                pattern="^\d{9}$"
                                required
                            >
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-envelope-at me-2"></i> Email
                            </label>
                            <input type="email" v-model="form.email" class="form-control" 
                            pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                            oninvalid="this.setCustomValidity('Invalid email address!');"
                            oninput="this.setCustomValidity('')"
                            required
                        >
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
                            <button type="submit" :disabled="isUnderage" class="btn bg-primary-cta w-100 mt-3">Create account</button>
                        </div>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        <p class="mb-2">Already have an account?</p>
                        <button class="btn btn-outline-dark w-100" data-bs-target="#loginModal" data-bs-toggle="modal">
                            <i class="bi bi-person-circle me-2"></i> Login here
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>