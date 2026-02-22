<!-- Guillermo Soto -> User Data -->
<!-- Sergio Libros -> Reservation List -->

<script setup>

import { ref, onMounted, reactive, nextTick } from 'vue';
import axios from 'axios';
import { useToast } from '../composables/useToast';
import { useRoute } from 'vue-router'

// Toasts and route objects
const toast = useToast();
const route = useRoute();

// USER DATA DISPLAY FUNCTIONS ======================================================================

const isEditing = ref(false);
const showPasswordSection = ref(false);

// User reactive object
const user = reactive({
    first_name: '',
    last_name: '',
    second_last_name: '',
    dni: '',
    birth_date: '',
    email: '',
    phone: '',
    address: ''
});

// Password reactive object
const passForm = reactive({
    current_password: '', password: '', password_confirmation: ''
});

// Reservation array
const reservations = ref([]);

// Loading controller
const isLoadingReservations = ref(true);

// LOAD DATA ===============================================
onMounted(async () => {
    try {
        // Get current user
        const response = await axios.get('/user/me');
        Object.assign(user, response.data);
        // Get user reservations
        const reservationsResponse = await axios.get('/user/reservations');
        reservations.value = reservationsResponse.data;
        isLoadingReservations.value = false;

        nextTick(() => {
            // If accesed by hash, scroll to section
            if (route.hash) {
                document.querySelector(route.hash)?.scrollIntoView({ behavior: 'smooth' })
            }
        })
    } catch (error) {
        console.error("Error loading profile", error);
    }
});


// SAVE PROFILE ============================================
// const saveProfile = async () => {
//     try {
//         await axios.put('/user/profile', user);
//         isEditing.value = false;
//         toast.info("Profile updated!", "Profile info");
//     } catch (e) { toast.error("Error updating profile", "Profile Error"); }
// };

const saveProfile = async () => {
    try {
        const payload = {
            first_name: user.first_name,
            last_name: user.last_name,
            second_last_name: user.second_last_name,
            email: user.email,
            phone: user.phone,
            address: user.address
        };

        const response = await axios.put('/user/profile', payload);

        Object.assign(user, response.data.user);

        isEditing.value = false;
        toast.info("¡Perfil actualizado!", "Info");
    } catch (e) {
        if (e.response && e.response.status === 419) {
            toast.error("La sesión ha expirado. Por favor, recarga la página.", "Error de Sesión");
        } else {
            toast.error("Error al actualizar el perfil", "Error");
        }
    }
};

// CHANGE PASSWORD =========================================
const changePassword = async () => {
    try {
        await axios.put('/user/password', passForm);
        toast.info("Password updated!", "Password Info");
        showPasswordSection.value = false;
        // Reset password fields
        passForm.current_password = ''; passForm.password = ''; passForm.password_confirmation = '';
    } catch (e) { toast.error("Error: Check current password or confirmation", "Password Error"); }
};

// Function to confirm a reservation
const completeReservation = async (reservationId) => {
    try {
        await axios.put(`/reservations/${reservationId}/confirm`);
        const reservation = reservations.value.find(r => r.id === reservationId);
        if (reservation) reservation.status = 'confirmed';
        toast.success("Reservation confirmed!", "Reservation Info");
    } catch (e) {
        toast.error("Error confirming reservation", "Reservation Error");
    }
};

// Function to cancel a reservation
const cancelReservation = async (reservationId) => {
    try {
        await axios.put(`/reservations/${reservationId}/cancel`);
        const reservation = reservations.value.find(r => r.id === reservationId);
        if (reservation) reservation.status = 'cancelled';
        toast.success("Reservation cancelled!", "Reservation Info");
    } catch (e) {
        toast.error("Error cancelling reservation", "Reservation Error");
    }
};

</script>

<style scoped>
@import "../../css/admin_style.css";
</style>

<template>

    <!-- USER DATA DISPLAY ========================================================================== -->

    <div class="container mt-5">

        <!-- HEADER ========================================================== -->

        <div class="dashboard-title mb-4 p-3 shadow-sm d-flex justify-content-between align-items-center">
            <h4 class="mb-0">My Profile</h4>
            <button @click="isEditing = !isEditing" class="btn bg-primary-cta">
                <i class="bi bi-pen me-2"></i>
                {{ isEditing ? 'Cancel' : 'Edit Profile' }}
            </button>
        </div>

        <!-- DATA ============================================================ -->

        <div class="custom-panel mb-4 p-4 shadow-sm">

            <!-- DATA ============================================================ -->
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> Name
                    </label>
                    <input v-if="isEditing" v-model="user.first_name" class="form-control"
                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$" title="Please enter a valid name (letters only)">
                    <p v-else class="form-control-plaintext text-white">{{ user.first_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> First surname
                    </label>
                    <input v-if="isEditing" v-model="user.last_name" class="form-control"
                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$" title="Please enter a valid surname (letters only)">
                    <p v-else class="form-control-plaintext text-white">{{ user.last_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> Second surname
                    </label>
                    <input v-if="isEditing" v-model="user.second_last_name" class="form-control"
                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$"
                        title="Please enter a valid second surname (letters only)">
                    <p v-else class="form-control-plaintext text-white">{{ user.second_last_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-card-text me-2"></i> DNI
                    </label>
                    <p class="form-control-plaintext text-white">{{ user.dni }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-calendar me-2"></i> Birth date
                    </label>
                    <p class="form-control-plaintext text-white">{{ user.birth_date }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-calendar me-2"></i> Register date
                    </label>
                    <p class="form-control-plaintext text-white">{{ user.registration_date }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-phone me-2"></i> Phone
                    </label>
                    <input v-if="isEditing" v-model="user.phone" class="form-control" minlength="9" maxlength="9"
                        pattern="^\d{9}$" title="Please enter a valid 9-digit phone number">
                    <p v-else class="form-control-plaintext text-white">{{ user.phone }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-envelope-at me-2"></i> Email
                    </label>
                    <input v-if="isEditing" v-model="user.email" class="form-control"
                        pattern="^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$"
                        @invalid="$event.target.setCustomValidity('Invalid email address!');"
                        @input="$event.target.setCustomValidity('')" title="Please enter a valid email address">
                    <p v-else class="form-control-plaintext text-white">{{ user.email }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-geo-alt me-2"></i> Address
                    </label>
                    <input v-if="isEditing" v-model="user.address" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.address }}</p>
                </div>

                <div v-if="isEditing" class="mt-4">
                    <button @click="saveProfile" class="btn bg-primary-cta">
                        <i class="bi bi-floppy me-2"></i> Save Changes
                    </button>
                </div>

                <!-- PASSWORD ======================================================== -->

                <hr class="my-2">

                <div class="d-flex justify-content-start align-items-center mb-3">
                    <button @click="showPasswordSection = !showPasswordSection" class="btn bg-primary-cta">
                        <i class="bi bi-key me-2"></i>
                        {{ showPasswordSection ? 'Cancel' : 'Change Password' }}
                    </button>
                </div>

                <div v-if="showPasswordSection" class="custom-panel p-4 shadow-sm">
                    <div class="mb-2"><input type="password" v-model="passForm.current_password"
                            placeholder="Current Password" class="form-control"></div>
                    <div class="mb-2"><input type="password" v-model="passForm.password" placeholder="New Password"
                            class="form-control" minlength="8" required></div>
                    <div class="mb-2"><input type="password" v-model="passForm.password_confirmation"
                            placeholder="Confirm New Password" class="form-control" minlength="8" required></div>
                    <button @click="changePassword" class="btn bg-primary-cta">
                        <i class="bi bi-floppy me-2"></i> Update Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DIVIDER ============================================================== -->

    <section class="mt-3 mb-4">
        <div class="container">
            <div class="d-flex justify-content-center">
                <img :src="'/images/decorations/divider.png'" alt="Divider" class="w-50">
            </div>
        </div>
    </section>

    <!-- RESERVATIONS DISPLAY ======================================================================= -->

    <div class="container" id="reservation-list">

        <!-- HEADER ========================================================== -->

        <div class="dashboard-title mb-4 p-3 shadow-sm d-flex justify-content-between align-items-start">
            <h4 class="mb-0">Reservations</h4>
        </div>

        <!-- DATA ============================================================ -->

        <div class="custom-panel tab-content p-4 shadow-sm min-vh-50 mb-4">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th scope="col"><i class="bi bi-car-front me-2"></i> Vehicle</th>
                            <th scope="col"><i class="bi bi-calendar me-2"></i> Start Date</th>
                            <th scope="col"><i class="bi bi-calendar me-2"></i> End Date</th>
                            <th scope="col"><i class="bi bi-cash me-2"></i> Total Price</th>
                            <th scope="col"><i class="bi bi-info-circle me-2"></i> Status</th>
                            <th scope="col"><i class="bi bi-gear me-2"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoadingReservations" class="container text-center py-5">
                            <td colspan="6" class="p-4">
                                <div class="spinner-border text-light" role="status"></div>
                                <p class="mt-3 fs-5">Loading Reservations...</p>
                            </td>
                        </tr>
                        <tr v-else v-for="reservation in reservations" :key="reservation.id">
                            <td>
                                <span v-if="reservation.vehicle">{{ reservation.vehicle.brand }} {{
                                    reservation.vehicle.model }}</span>
                                <span v-else>No Vehicle Available</span>
                            </td>
                            <td>{{ new Date(reservation.start_date).toLocaleDateString('es-ES') }}</td>
                            <td>{{ new Date(reservation.end_date).toLocaleDateString('es-ES') }}</td>
                            <td>{{ Number(reservation.amount).toLocaleString('es-ES') }} €</td>
                            <td>{{ reservation.status }}</td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-sm btn-success" v-if="reservation.status === 'pending'"
                                    @click="completeReservation(reservation.id)">
                                    <i class="bi bi-check-circle"></i> Confirm
                                </button>
                                <button class="btn btn-sm btn-danger"
                                    v-if="reservation.status === 'confirmed' || reservation.status === 'pending'"
                                    @click="cancelReservation(reservation.id)">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>
                                <div class="text-danger"
                                    v-if="reservation.status === 'cancelled' || reservation.status === 'completed'">No
                                    Actions for this reservation</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>