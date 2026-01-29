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
                            <input type="text" class="form-control" placeholder="12345678A" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-person me-2"></i> Name
                            </label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-person me-2"></i> Surname
                            </label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-calendar me-2"></i> Birthday
                            </label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-geo-alt me-2"></i> Address
                            </label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-phone me-2"></i> Phone
                            </label>
                            <input type="tel" class="form-control"
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
                            <input type="email" class="form-control" 
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
                            <input type="password" class="form-control"
                                minlength="12"
                                maxlength="20"
                                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@?¿!¡_-])[A-Za-z0-9$@?¿!¡_-]{8,12}$"
                                required
                            >
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-key me-2"></i> Confirm password
                            </label>
                            <input type="password" class="form-control"
                                minlength="12"
                                maxlength="20"
                                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@?¿!¡_-])[A-Za-z0-9$@?¿!¡_-]{8,12}$"
                                required
                            >
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn bg-primary-cta w-100 mt-3">Create account</button>
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

<script setup>
    const handleRegister = () => {
        // Aquí irá la lógica para conectar con el controlador de Laravel
        console.log("Registro enviado");
    }
</script>