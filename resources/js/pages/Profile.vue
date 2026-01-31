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

<template>

    <!-- USER DATA DISPLAY ========================================================================== -->

    <div class="container mt-5">
        <div class="card shadow">

            <!-- HEADER ========================================================== -->

            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">My Profile</h4>
                <button @click="isEditing = !isEditing" class="btn btn-sm btn-outline-light">
                    {{ isEditing ? 'Cancel' : 'Edit Profile' }}
                </button>
            </div>

            <!-- DATA ============================================================ -->

            <div class="card-body">

                <!-- DATA ============================================================ -->
                <div class="row g-3">
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Name</label>
                        <input v-if="isEditing" v-model="user.first_name" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.first_name }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">First surname</label>
                        <input v-if="isEditing" v-model="user.last_name" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.last_name }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Second surname</label>
                        <input v-if="isEditing" v-model="user.second_last_name" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.second_last_name }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">DNI</label>
                        <p class="form-control-plaintext text-muted">{{ user.dni }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Birth date</label>
                        <p class="form-control-plaintext text-muted">{{ user.birth_date }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Register date</label>
                        <p class="form-control-plaintext text-muted">{{ user.registration_date }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Phone</label>
                        <input v-if="isEditing" v-model="user.phone" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.phone }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Email</label>
                        <input v-if="isEditing" v-model="user.email" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.email }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Address</label>
                        <input v-if="isEditing" v-model="user.address" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.address }}</p>
                    </div>
                </div>

                <div v-if="isEditing" class="mt-4">
                    <button @click="saveProfile" class="btn btn-success">Save Changes</button>
                </div>

                <hr class="my-4">

                <!-- PASSWORD ======================================================== -->
                <button @click="showPasswordSection = !showPasswordSection" class="btn btn-outline-danger">
                    <i class="bi bi-key me-2"></i>Change Password
                </button>

                <div v-if="showPasswordSection" class="card mt-3 bg-light">
                    <div class="card-body">
                        <div class="mb-2">
                            <input type="password" v-model="passForm.current_password" placeholder="Current Password" class="form-control">
                        </div>
                        <div class="mb-2">
                            <input type="password" v-model="passForm.password" placeholder="New Password" class="form-control">
                        </div>
                        <div class="mb-2">
                            <input type="password" v-model="passForm.password_confirmation" placeholder="Confirm New Password" class="form-control">
                        </div>
                        <button @click="changePassword" class="btn btn-danger">Update Password</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>