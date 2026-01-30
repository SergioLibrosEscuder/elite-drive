<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

const isEditing = ref(false);
const showPasswordSection = ref(false);
const user = reactive({
    first_name: '', last_name: '', email: '', phone: '', address: ''
});

const passForm = reactive({
    current_password: '', password: '', password_confirmation: ''
});

// Cargar datos al iniciar
onMounted(async () => {
    try {
        const response = await axios.get('/user/me');
        Object.assign(user, response.data);
    } catch (error) {
        console.error("Error loading profile", error);
    }
});

const saveProfile = async () => {
    try {
        await axios.put('/user/profile', user);
        isEditing.value = false;
        alert("Profile updated!");
    } catch (e) { alert("Error updating profile"); }
};

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
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">My Profile</h3>
                <button @click="isEditing = !isEditing" class="btn btn-sm btn-outline-light">
                    {{ isEditing ? 'Cancel' : 'Edit Profile' }}
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold">First Name</label>
                        <input v-if="isEditing" v-model="user.first_name" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.first_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">DNI (Identity Number)</label>
                        <p class="form-control-plaintext text-muted">{{ user.dni }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Email</label>
                        <input v-if="isEditing" v-model="user.email" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.email }}</p>
                    </div>
                    </div>

                <div v-if="isEditing" class="mt-4">
                    <button @click="saveProfile" class="btn btn-success">Save Changes</button>
                </div>

                <hr class="my-4">

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