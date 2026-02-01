<script setup>

import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';

// USER DATA DISPLAY FUNCTIONS ======================================================================

const isEditingProfile = ref(false);
const showPasswordSection = ref(false);

const user = reactive({
    first_name: '', last_name: '', email: '', phone: '', address: ''
});

const passForm = reactive({
    current_password: '', password: '', password_confirmation: ''
});

const vehicle = reactive({
    license_plate: '', model: '', manufacturing_year: '', hourly_price: '', status: ''
});

// LOAD DATA ===============================================
onMounted(async () => {
    try {
        const responseUsers = await axios.get('/user/me');
        const responseCars = await axios.get('/api/cars');

        Object.assign(user, responseUsers.data);
        Object.assign(vehicle, responseCars.data);
    } catch (error) {
        console.error("Error loading profile", error);
    }

    fetchCustomers();
    fetchVehicles();
});

// SAVE PROFILE ============================================
const saveProfile = async () => {
    try {
        await axios.put('/user/profile', user);
        isEditingProfile.value = false;
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

// ADMIN DASHBOARD FUNCTIONS ========================================================================

const activeTab = ref('users');
const customers = ref([]);
const vehicles = ref([]);

const isEditingCustomer = ref(false);
const isEditingVehicle = ref(false);

const showUserModal = ref(false);
const showVehicleModal = ref(false);
const showUploadVehicleImagesModal = ref(false);

const selectVehicleForImageUpload = ref(null);

const thumbnailFile = ref(null);
const coverFile = ref(null);

const userForm = reactive({
    id: null,
    dni: '',
    first_name: '',
    last_name: '',
    second_last_name: '',
    birth_date: '',
    email: '',
    password: '',
    phone: '',
    address: ''
});

const vehicleForm = reactive({
    id: null,
    license_plate: '',
    brand: '',
    model: '',
    hourly_price: '',
    manufacturing_year: '',
    traction: '',
    transmission: '',
    description: '',
    engine: '',
    engine_capacity: '',
    fuel_type: '',
    doors: '',
    color: '',
    status: ''
});

// REQUEST =================================================
const fetchCustomers = async () => {
    const res = await axios.get('/admin/customers');
    customers.value = res.data;
};

const fetchVehicles = async () => {
    const res = await axios.get('/api/cars');
    vehicles.value = res.data;
};

// CREATE CUSTOMER MODAL ===================================
const openCreateUserModal = () => {
    isEditingCustomer.value = false;
    Object.assign(userForm, {
        id: null,
        dni: '',
        first_name: '',
        last_name: '',
        second_last_name: '',
        birth_date: '',
        email: '',
        password: '',
        phone: '',
        address: ''
    });
    showUserModal.value = true;
};

// CREATE VEHICLE MODAL ===================================
const openCreateVehicleModal = () => {
    isEditingVehicle.value = false;
    Object.assign(vehicleForm, {
        id: null,
        license_plate: '',
        brand: '',
        model: '',
        hourly_price: '',
        manufacturing_year: '',
        traction: '',
        transmission: '',
        description: '',
        engine: '',
        engine_capacity: '',
        fuel_type: '',
        doors: '',
        color: '',
        status: ''
    });
    showVehicleModal.value = true;
};

// UPLOAD VEHICLE IMAGES MODAL ============================
const openUploadVehicleImagesModal = (vehicle) => {
    selectVehicleForImageUpload.value = vehicle;

    // Reset files
    thumbnailFile.value = null;
    coverFile.value = null;

    showUploadVehicleImagesModal.value = true;
};

// EDIT CUSTOMER MODAL =====================================
const openEditUserModal = (customer) => {
    isEditingCustomer.value = true;
    Object.assign(userForm, customer);
    userForm.password = ''; // No editamos password aquí por simplicidad
    showUserModal.value = true;
};

// EDIT VEHICLE MODAL =====================================
const openEditVehicleModal = (vehicle) => {
    isEditingVehicle.value = true;
    Object.assign(vehicleForm, vehicle);
    showVehicleModal.value = true;
};

// SAVE CUSTOMER ===========================================
const saveCustomer = async () => {
    try {
        if (isEditingCustomer.value) {
            await axios.put(`/admin/customers/${userForm.id}`, userForm);
        } else {
            await axios.post('/admin/customers', userForm);
        }
        showUserModal.value = false;
        fetchCustomers();
        alert('Success!');
    } catch (e) { alert('Error saving customer'); }
};

// SAVE VEHICLE ===========================================
const saveVehicle = async () => {
    try {
        if (isEditingVehicle.value) {
            await axios.put(`/cars/${vehicleForm.id}`, vehicleForm);
        } else {
            await axios.post('/cars', vehicleForm);
        }
        showVehicleModal.value = false;
        fetchVehicles();
        alert('Success!');
    } catch (e) { alert('Error saving vehicle'); }
};

// DELETE CUSTOMER =========================================
const deleteCustomer = async (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        await axios.delete(`/admin/customers/${id}`);
        fetchCustomers();
    }
};

// DELETE VEHICLE ==========================================
const deleteVehicle = async (id) => {
    if (confirm('Are you sure you want to delete this vehicle?')) {
        await axios.delete(`/cars/${id}`);
        fetchVehicles();
    }
};

// AGE VALIDATION ==========================================
const age = computed(() => {
    if (!userForm.birth_date) return null;
    const birth = new Date(userForm.birth_date);
    const today = new Date();
    let ageCalc = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        ageCalc--;
    }
    return ageCalc;
});

const isUnderage = computed(() => age.value !== null && age.value < 18);

// DETECT VEHICLE IMAGE FILES ==============================
const handleImagesFileChange = (event, type) => {
    const file = event.target.files[0];
    if (type === 'thumbnail') {
        thumbnailFile.value = file;
    } else if (type === 'cover') {
        coverFile.value = file;
    }
};

// UPLOAD VEHICLE IMAGES ====================================
const uploadVehicleImages = async () => {
    if (!thumbnailFile.value && !coverFile.value) {
        alert('Please select at least one image to upload.');
        return;
    };

    try {
        const formData = new FormData();
        if (thumbnailFile.value) {
            formData.append('thumbnail', thumbnailFile.value);
        }
        if (coverFile.value) {
            formData.append('cover', coverFile.value);
        }

        await axios.post(`/cars/${selectVehicleForImageUpload.value.id}/images`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        alert('Images uploaded successfully!');
        showUploadVehicleImagesModal.value = false;
    } catch (e) {
        console.error('Error uploading images', e);
        alert('Error uploading images');
    }
}
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
            <button @click="isEditingProfile = !isEditingProfile" class="btn bg-primary-cta">
                <i class="bi bi-pen me-2"></i>
                {{ isEditingProfile ? 'Cancel' : 'Edit Profile' }}
            </button>
        </div>
        
        <!-- DATA ============================================================ -->

        <div class="custom-panel mb-4 p-4 shadow-sm">
            
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> Name
                    </label>
                    <input v-if="isEditingProfile" v-model="user.first_name" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.first_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> First surname
                    </label>
                    <input v-if="isEditingProfile" v-model="user.last_name" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.last_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> Second surname
                    </label>
                    <input v-if="isEditingProfile" v-model="user.second_last_name" class="form-control">
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
                    <input v-if="isEditingProfile" v-model="user.phone" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.phone }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-envelope-at me-2"></i> Email
                    </label>
                    <input v-if="isEditingProfile" v-model="user.email" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.email }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-geo-alt me-2"></i> Address
                    </label>
                    <input v-if="isEditingProfile" v-model="user.address" class="form-control">
                    <p v-else class="form-control-plaintext text-white">{{ user.address }}</p>
                </div>

                <div v-if="isEditingProfile" class="mt-4">
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

    <!-- ADMIN DASHBOARD ============================================================================ -->

    <div class="container mt-4 mb-5">

        <!-- HEADER ========================================================== -->
        <h4 class="dashboard-title mb-4 p-3 shadow-sm">Admin Dashboard</h4>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link bg-tab" :class="{ active: activeTab === 'reservations' }" @click="activeTab = 'reservations'">
                    <i class="bi bi-calendar-check me-2"></i>Reservations
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link bg-tab" :class="{ active: activeTab === 'vehicles' }" @click="activeTab = 'vehicles'">
                    <i class="bi bi-car-front me-2"></i>Vehicles
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link bg-tab" :class="{ active: activeTab === 'users' }" @click="activeTab = 'users'">
                    <i class="bi bi-people me-2"></i> Customers
                </button>
            </li>
        </ul>

        <!-- TAB ============================================================= -->
        <div class="custom-panel tab-content p-4 shadow-sm min-vh-50">
            
            <!-- RESERVATIONS ================================================ -->
            <div v-if="activeTab === 'reservations'">
                <h4><i class="bi bi-calendar-check"></i> Reservation Management</h4>
                <p class="text-muted">Proximamente: Gestión de reservas.</p>
            </div>

            <!-- VEHICLES ==================================================== -->
            <div v-if="activeTab === 'vehicles'">
                <!-- HEADER ================================================== -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="bi bi-car-front"></i> Vehicle Fleet</h4>
                    <button @click="openCreateVehicleModal" class="btn bg-primary-cta">
                        <i class="bi bi-plus-circle me-2"></i>New Vehicle
                    </button>
                </div>
                <!-- DATA TABLE ============================================== -->
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-card-text me-2"></i> License Plate</th>
                                <th><i class="bi bi-car-front me-2"></i> Brand</th>
                                <th><i class="bi bi-car-front me-2"></i> Model</th>
                                <th><i class="bi bi-calendar me-2"></i> Manufacturing Year</th>
                                <th><i class="bi bi-info-circle me-2"></i> ActionsStatus</th>
                                <th><i class="bi bi-gear me-2"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="v in vehicles" :key="v.id">
                                <td>{{ v.license_plate }}</td>
                                <td>{{ v.brand }}</td>
                                <td>{{ v.model }}</td>
                                <td>{{ v.manufacturing_year }}</td>
                                <td>{{ v.status }}</td>
                                <td>
                                    <button @click="openUploadVehicleImagesModal(v)" class="btn btn-sm bg-primary-cta me-1">
                                        <i class="bi bi-camera"></i>
                                    </button>
                                    <button @click="openEditVehicleModal(v)" class="btn btn-sm bg-primary-cta me-2">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button @click="deleteVehicle(v.id)" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CUSTOMERS =================================================== -->
            <div v-if="activeTab === 'users'">
                <!-- HEADER ================================================== -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="bi bi-people"></i> Customer Directory</h4>
                    <button @click="openCreateUserModal" class="btn bg-primary-cta">
                        <i class="bi bi-person-plus me-2"></i>New Customer
                    </button>
                </div>
                <!-- DATA TABLE ============================================== -->
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-card-text me-2"></i> DNI</th>
                                <th><i class="bi bi-person me-2"></i> Name</th>
                                <th><i class="bi bi-envelope-at me-2"></i> Email</th>
                                <th><i class="bi bi-phone me-2"></i> Phone</th>
                                <th><i class="bi bi-geo-alt me-2"></i> Address</th>
                                <th><i class="bi bi-gear me-2"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="c in customers" :key="c.id">
                                <td>{{ c.dni }}</td>
                                <td>{{ c.first_name }} {{ c.last_name }} {{ c.second_last_name }}</td>
                                <td>{{ c.email }}</td>
                                <td>{{ c.phone }}</td>
                                <td>{{ c.address }}</td>
                                <td>
                                    <button @click="openEditUserModal(c)" class="btn btn-sm bg-primary-cta me-2">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button @click="deleteCustomer(c.id)" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- INSERT CUSTOMER MODAL ====================================================================== -->

    <div v-if="showUserModal">
        <div class="modal-backdrop fade show"></div>
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">{{ isEditingCustomer ? 'Edit' : 'Create' }} Customer</h5>
                        <button @click="showUserModal = false" class="btn-close"></button>
                    </div>
                    <form @submit.prevent="saveCustomer">
                        <div class="modal-body">
                            <div class="row g-2">

                                <!-- DATA ============================================================ -->
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-card-text me-2"></i> DNI
                                    </label>
                                    <input v-model="userForm.dni" :disabled="isEditingCustomer" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-calendar me-2"></i> Birthday
                                    </label>
                                    <input 
                                        v-model="userForm.birth_date" 
                                        type="date" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': isUnderage }"
                                        required
                                    >
                                    <div v-if="isUnderage" class="invalid-feedback">
                                        Must be at least 18 years old.
                                    </div>
                                    <div v-else-if="age !== null" class="small text-success">
                                        Age: {{ age }} years.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> First Name
                                    </label>
                                    <input v-model="userForm.first_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> First Surname
                                    </label>
                                    <input v-model="userForm.last_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> Second Surname
                                    </label>
                                    <input v-model="userForm.second_last_name" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">
                                        <i class="bi bi-envelope-at me-2"></i> Email
                                    </label>
                                    <input v-model="userForm.email" type="email" class="form-control" required>
                                </div>
                                <div class="col-md-12" v-if="!isEditingCustomer">
                                    <label class="small fw-bold">
                                        <i class="bi bi-key me-2"></i> Password
                                    </label>
                                    <input v-model="userForm.password" type="password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-phone me-2"></i> Phone
                                    </label>
                                    <input v-model="userForm.phone" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-geo-alt me-2"></i> Address
                                    </label>
                                    <input v-model="userForm.address" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="showUserModal = false" class="btn bg-primary-cta">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn bg-primary-cta" :disabled="isUnderage">
                                <i class="bi bi-floppy me-2"></i> Save Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- INSERT VEHICLE MODAL ====================================================================== -->
    <div v-if="showVehicleModal">
        <div class="modal-backdrop fade show"></div>
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">{{ isEditingVehicle ? 'Edit' : 'Create' }} Vehicle</h5>
                        <button @click="showVehicleModal = false" class="btn-close"></button>
                    </div>
                    <form @submit.prevent="saveVehicle">
                        <div class="modal-body">
                            <div class="row g-2">
                                <!-- DATA ============================================================ -->
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-card-text me-2"></i> License Plate
                                    </label>
                                    <input v-model="vehicleForm.license_plate" :disabled="isEditingVehicle"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-cash me-2"></i> Hourly Price (€)
                                    </label>
                                    <input v-model="vehicleForm.hourly_price" type="number" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-car-front me-2"></i> Brand
                                    </label>
                                    <input v-model="vehicleForm.brand" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-car-front me-2"></i> Model
                                    </label>
                                    <input v-model="vehicleForm.model" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">
                                        <i class="bi bi-list-columns-reverse me-2"></i> Description
                                    </label>
                                    <textarea v-model="vehicleForm.description" class="form-control"
                                        required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-calendar me-2"></i> Year
                                    </label>
                                    <input v-model="vehicleForm.manufacturing_year" type="number" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-palette me-2"></i> Color
                                    </label>
                                    <input v-model="vehicleForm.color" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">
                                        <i class="bi bi-gear me-2"></i> Engine
                                    </label>
                                    <input v-model="vehicleForm.engine" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-gear me-2"></i> Engine Capacity (cc)
                                    </label>
                                    <input v-model="vehicleForm.engine_capacity" type="number" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-fuel-pump me-2"></i> Fuel Type
                                    </label>
                                    <input v-model="vehicleForm.fuel_type" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-gear me-2"></i> Traction
                                    </label>
                                    <input v-model="vehicleForm.traction" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-gear me-2"></i> Transmission
                                    </label>
                                    <input v-model="vehicleForm.transmission" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-door-closed me-2"></i> Doors
                                    </label>
                                    <input v-model="vehicleForm.doors" type="number" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-info-circle me-2"></i> Status
                                    </label>
                                    <select v-model="vehicleForm.status" class="form-select" required>
                                        <option value="available">available</option>
                                        <option value="reserved">reserved</option>
                                        <option value="maintenance">maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="showVehicleModal = false" class="btn bg-primary-cta">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn bg-primary-cta">
                                <i class="bi bi-floppy me-2"></i> Save Vehicle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- UPLOAD VEHICLE IMAGES ====================================================================== -->

    <div v-if="showUploadVehicleImagesModal">
        <div class="modal-backdrop fade show"></div>
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">Manage Images (ID: {{ selectVehicleForImageUpload.id }})</h5>
                        <button @click="showUploadVehicleImagesModal = false" class="btn-close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-light border">
                            <small class="text-muted">
                                Images will be saved as: <br>
                                <code>{{ selectVehicleForImageUpload.id }}-thm.webp</code> (Thumbnail)<br>
                                <code>{{ selectVehicleForImageUpload.id }}-cvr.webp</code> (Cover)
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thumbnail</label>
                            <input type="file" class="form-control" accept="image/*"
                                @change="handleImagesFileChange($event, 'thumbnail')">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Cover</label>
                            <input type="file" class="form-control" accept="image/*"
                                @change="handleImagesFileChange($event, 'cover')">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="showUploadVehicleImagesModal = false" class="btn bg-primary-cta">
                            <i class="bi bi-x-circle me-2"></i> Cancel
                        </button>
                        <button type="button" @click="uploadVehicleImages" class="btn bg-primary-cta">
                            <i class="bi bi-cloud-upload me-2"></i> Upload
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>