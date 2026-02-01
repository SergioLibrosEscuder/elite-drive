<script setup>

    import { ref, onMounted, reactive } from 'vue';
    import axios from 'axios';

    // USER DATA DISPLAY FUNCTIONS ======================================================================

    const isEditing = ref(false);
    const showPasswordSection = ref(false);

    const user = reactive({
        first_name: '', last_name: '', email: '', phone: '', address: ''
    });

    const passForm = reactive({
        current_password: '', password: '', password_confirmation: ''
    });

    // LOAD DATA ===============================================
    onMounted(async () => {
        try {
            const response = await axios.get('/user/me');
            Object.assign(user, response.data);
        } catch (error) {
            console.error("Error loading profile", error);
        }
    });

    // SAVE PROFILE ============================================
    const saveProfile = async () => {
        try {
            await axios.put('/user/profile', user);
            isEditing.value = false;
            alert("Profile updated!");
        } catch (e) { alert("Error updating profile"); }
    };

    // CHANGE PASSWORD =========================================
    const changePassword = async () => {
        try {
            await axios.put('/user/password', passForm);
            alert("Password updated!");
            showPasswordSection.value = false;
            passForm.current_password = ''; passForm.password = ''; passForm.password_confirmation = '';
        } catch (e) { alert("Error: Check current password or confirmation"); }
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
                    <input v-if="isEditing" v-model="user.first_name" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.first_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> First surname
                    </label>
                    <input v-if="isEditing" v-model="user.last_name" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.last_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> Second surname
                    </label>
                    <input v-if="isEditing" v-model="user.second_last_name" class="form-control">
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
                    <input v-if="isEditing" v-model="user.phone" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.phone }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-envelope-at me-2"></i> Email
                    </label>
                    <input v-if="isEditing" v-model="user.email" class="form-control">
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
                    <div class="mb-2"><input type="password" v-model="passForm.current_password" placeholder="Current Password" class="form-control"></div>
                    <div class="mb-2"><input type="password" v-model="passForm.password" placeholder="New Password" class="form-control"></div>
                    <div class="mb-2"><input type="password" v-model="passForm.password_confirmation" placeholder="Confirm New Password" class="form-control"></div>
                    <button @click="changePassword" class="btn bg-primary-cta">
                        <i class="bi bi-floppy me-2"></i> Update Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- RESERVATIONS DISPLAY ======================================================================= -->

    <div class="container">

        <!-- HEADER ========================================================== -->

        <div class="dashboard-title mb-4 p-3 shadow-sm d-flex justify-content-between align-items-start">
            <h4 class="mb-0">Reservations</h4>
        </div>

        <!-- DATA ============================================================ -->

        <div class="custom-panel mb-4 p-4 shadow-sm">
                
        </div>
    </div>

</template>