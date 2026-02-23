<!-- Guillermo Soto -> Structure + Customers Tab -->
<!-- Sergio Libros -> Vehicles Tab and Reservations Tab -->

<script setup>

import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';
import { useToast } from '../composables/useToast';

// Object toast to create custom toasts
const toast = useToast();

// USER DATA DISPLAY FUNCTIONS ======================================================================

const isEditingProfile = ref(false);
const showPasswordSection = ref(false);

// Reactive object related with form
const user = reactive({
    first_name: '', last_name: '', email: '', phone: '', address: ''
});

// Reactive object related with form
const passForm = reactive({
    current_password: '', password: '', password_confirmation: ''
});

// Reactive object related with form
const vehicle = reactive({
    license_plate: '', model: '', manufacturing_year: '', hourly_price: '', status: ''
});

// Reactive object related with form
const reservation = reactive({
    vehicle_id: '', start_date: '', end_date: '', amount: '', status: ''
});

// Reference to search fields
const searchCustomer = ref('');
const searchVehicle = ref('');
const searchReservation = ref('');

// Loading controllers
const isLoadingCustomers = ref(true);
const isLoadingVehicles = ref(true);
const isLoadingReservations = ref(true);

// Customers that coincidate with search params
const filteredCustomers = computed(() => {
    // If no search params, return all customers
    if (!searchCustomer.value) return customers.value;
    // Search by DNI, email, phone or full name
    const query = searchCustomer.value.toLowerCase();
    return customers.value.filter(c =>
        c.dni.toLowerCase().startsWith(query) ||
        c.email.toLowerCase().startsWith(query) ||
        c.phone.toLowerCase().startsWith(query) ||
        (c.first_name + ' ' + c.last_name + ' ' + (c.second_last_name || '')).toLowerCase().includes(query)
    );
});

// Vehicles that coincidate with search params
const filteredVehicles = computed(() => {
    // If no search params, return all vehicles
    if (!searchVehicle.value) return vehicles.value;
    // Search by license plate, brand, model or status
    const query = searchVehicle.value.toLowerCase();
    return vehicles.value.filter(v =>
        v.license_plate.toLowerCase().startsWith(query) ||
        v.brand.toLowerCase().startsWith(query) ||
        v.model.toLowerCase().startsWith(query) ||
        v.status.toLowerCase().startsWith(query)
    );
});

// Reservations that coincidate with search params
const filteredReservations = computed(() => {
    // If no search params, return all reservations
    if (!searchReservation.value) return reservations.value;
    // Search by user DNI, vehicle license plate or reservation status
    const query = searchReservation.value.toLowerCase();
    return reservations.value.filter(r =>
        r.user.dni.toLowerCase().startsWith(query) ||
        r.vehicle.license_plate.toLowerCase().startsWith(query) ||
        r.status.toLowerCase().startsWith(query)
    );
});

// LOAD DATA ===============================================
onMounted(async () => {
    try {
        // Get users, vehicles and reservations
        const responseUsers = await axios.get('/user/me');
        const responseCars = await axios.get('/api/cars');
        const responseReservations = await axios.get('/reservations');

        // Load each data in the corresponding object
        Object.assign(user, responseUsers.data);
        Object.assign(vehicle, responseCars.data);
        Object.assign(reservation, responseReservations.data);
    } catch (error) {
        console.error("Error loading profile", error);
    }

    // Fetch all data needed
    fetchCustomers();
    fetchVehicles();
    fetchReservations();
});

// SAVE PROFILE FUNCTION ============================================
const saveProfile = async () => {
    try {
        // Send new profile info
        await axios.put('/user/profile', user);
        isEditingProfile.value = false;
        toast.info("Profile updated!", "Profile Info");
    } catch (e) { toast.error("Error updating profile", "Profile Error"); }
};

// CHANGE PASSWORD FUNCTION =========================================
const changePassword = async () => {
    try {
        // Send new password info
        await axios.put('/user/password', passForm);
        toast.info("Password updated!", "Password Info");
        showPasswordSection.value = false;
        // Reset form fields
        passForm.current_password = ''; passForm.password = ''; passForm.password_confirmation = '';
    } catch (e) { toast.error("Error: Check current password or confirmation", "Password Info"); }
};

// ADMIN DASHBOARD FUNCTIONS ========================================================================

// Reference objects
const activeTab = ref('users');
const customers = ref([]);
const vehicles = ref([]);
const reservations = ref([]);

// Editing booleans
const isEditingCustomer = ref(false);
const isEditingVehicle = ref(false);
const isEditingReservation = ref(false);

// Modal booleans
const showUserModal = ref(false);
const showVehicleModal = ref(false);
const showUploadVehicleImagesModal = ref(false);
const showReservationModal = ref(false);

// Vehicle selected for image upload
const selectVehicleForImageUpload = ref(null);

// Images reference
const thumbnailFile = ref(null);
const coverFile = ref(null);

// User form reactive object
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

// Vehicle form reactive object
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

// Reservation form reactive object
const reservationForm = reactive({
    id: null,
    user_id: null,
    vehicle_id: null,
    start_date: '',
    end_date: '',
    amount: '',
    status: ''
});

// REQUEST =================================================
// Get all customers
const fetchCustomers = async () => {
    const res = await axios.get('/admin/customers');
    customers.value = res.data;
    isLoadingCustomers.value = false;
};

// Get all vehicles
const fetchVehicles = async () => {
    const res = await axios.get('/api/cars');
    vehicles.value = res.data;
    isLoadingVehicles.value = false;
};

// Get all reservations
const fetchReservations = async () => {
    const res = await axios.get('/reservations');
    reservations.value = res.data;
    isLoadingReservations.value = false;
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

// CREATE RESERVATION MODAL ================================
const openCreateReservationModal = () => {
    isEditingReservation.value = false;
    Object.assign(reservationForm, {
        id: null,
        user_id: '',
        vehicle_id: '',
        start_date: '',
        end_date: '',
        amount: '',
        status: ''
    });
    showReservationModal.value = true;
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
    // No edit for password
    userForm.password = '';
    showUserModal.value = true;
};

// EDIT VEHICLE MODAL =====================================
const openEditVehicleModal = (vehicle) => {
    isEditingVehicle.value = true;
    Object.assign(vehicleForm, vehicle);
    showVehicleModal.value = true;
};

// EDIT RESERVATION MODAL ================================
const openEditReservationModal = (reservation) => {
    isEditingReservation.value = true;
    Object.assign(reservationForm, reservation);

    reservationForm.start_date = formatDateTimeForInput(reservation.start_date);
    reservationForm.end_date = formatDateTimeForInput(reservation.end_date);

    showReservationModal.value = true;
};

// SAVE CUSTOMER ===========================================
const saveCustomer = async () => {
    try {
        // Change HTTP method depending on editing or not
        if (isEditingCustomer.value) {
            await axios.put(`/admin/customers/${userForm.id}`, userForm);
        } else {
            await axios.post('/admin/customers', userForm);
        }
        showUserModal.value = false;
        fetchCustomers();
        toast.success('Success!', "Customer Info");
    } catch (e) { toast.error('Error saving customer', "Customer Error"); }
};

// SAVE VEHICLE ===========================================
const saveVehicle = async () => {
    try {
        // Change HTTP method depending on editing or not
        if (isEditingVehicle.value) {
            await axios.put(`/cars/${vehicleForm.id}`, vehicleForm);
        } else {
            await axios.post('/cars', vehicleForm);
        }
        showVehicleModal.value = false;
        fetchVehicles();
        toast.success('Success!', "Vehicle Info");
    } catch (e) { toast.error('Error saving vehicle', "Vehicle Error"); }
};

// SAVE RESERVATION ========================================
const saveReservation = async () => {
    try {
        // Change HTTP method depending on editing or not
        if (isEditingReservation.value) {
            await axios.put(`/reservations/${reservationForm.id}`, reservationForm);
        } else {
            await axios.post('/reservations', reservationForm);
        }
        showReservationModal.value = false;
        fetchReservations();
        toast.success('Success!', "Reservation Info");
    } catch (e) { toast.error('Error saving reservation', "Reservation Error"); }
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

// DELETE RESERVATION =======================================
const deleteReservation = async (id) => {
    if (confirm('Are you sure you want to delete this reservation?')) {
        await axios.delete(`/reservations/${id}`);
        fetchReservations();
    }
};

// AGE VALIDATION ==========================================
const age = computed(() => {
    if (!userForm.birth_date) return null;
    const birth = new Date(userForm.birth_date);
    const today = new Date();

    // Calculate age based on birthdate
    let ageCalc = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        ageCalc--;
    }
    return ageCalc;
});

// Check if user is underage based on age computed property
const isUnderage = computed(() => age.value !== null && age.value < 18);

// DETECT VEHICLE IMAGE FILES ==============================
const handleImagesFileChange = (event, type) => {
    const file = event.target.files[0];

    // Change corresponding image
    if (type === 'thumbnail') {
        thumbnailFile.value = file;
    } else if (type === 'cover') {
        coverFile.value = file;
    }
};

// UPLOAD VEHICLE IMAGES ====================================
const uploadVehicleImages = async () => {
    // Upload at least one image
    if (!thumbnailFile.value && !coverFile.value) {
        toast.warning('Please select at least one image to upload.', "Vehicle Images Validation");
        return;
    };

    try {
        const formData = new FormData();
        // Add thumbnail image if uploaded
        if (thumbnailFile.value) {
            formData.append('thumbnail', thumbnailFile.value);
        }
        // Add cover image if uploaded
        if (coverFile.value) {
            formData.append('cover', coverFile.value);
        }

        // Upload new images to storage
        await axios.post(`/cars/${selectVehicleForImageUpload.value.id}/images`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        toast.success('Images uploaded successfully!', "Vehicle Images Info");
        showUploadVehicleImagesModal.value = false;
    } catch (e) {
        console.error('Error uploading images', e);
        toast.error('Error uploading images', "Vehicle Images Error");
    }
}

// ESTIMATE RENTAL AMOUNT ===================================
const estimatedPrice = computed(() => {
    // If no vehicle nor dates, price is 0
    if (!reservationForm.vehicle_id || !reservationForm.start_date || !reservationForm.end_date) {
        return 0;
    }

    // Get selected car by id from vehicles list
    const selectedCar = vehicles.value.find(v => v.id === reservationForm.vehicle_id);
    // If no selected car, price us 0
    if (!selectedCar) return 0;

    const start = new Date(reservationForm.start_date);
    const end = new Date(reservationForm.end_date);

    const diffMs = end - start;
    let diffHours = diffMs / (1000 * 60 * 60);
    // No minutes, only hours
    diffHours = Math.ceil(diffHours);

    // No negative price
    if (diffHours <= 0) return 0;
    // Return estimated price based on hours and car price, with 2 decimals
    return (diffHours * Number(selectedCar.hourly_price)).toFixed(2);
});

// FILTER AVAILABLE VEHICLES FOR RESERVATION =================
const availableVehicles = computed(() => {
    // Only vehicles with status available or no vehicle change
    return vehicles.value.filter(v => {
        return v.status === 'available' || (isEditingReservation.value && v.id === reservationForm.vehicle_id);
    });
});

// FORMAT DATE FOR INPUT =====================================
const formatDateTimeForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);

    const year = date.getFullYear();
    // Pad month, day, hours and minutes with leading zeros if needed
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    // Date format valid for input type date
    return `${year}-${month}-${day}T${hours}:${minutes}`;
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
                    <input v-if="isEditingProfile" v-model="user.first_name" class="form-control"
                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$" title="Please enter a valid name (letters only)">
                    <p v-else class="form-control-plaintext text-white">{{ user.first_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> First surname
                    </label>
                    <input v-if="isEditingProfile" v-model="user.last_name" class="form-control"
                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$" title="Please enter a valid surname (letters only)">
                    <p v-else class="form-control-plaintext text-white">{{ user.last_name }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-person me-2"></i> Second surname
                    </label>
                    <input v-if="isEditingProfile" v-model="user.second_last_name" class="form-control"
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
                    <input v-if="isEditingProfile" v-model="user.phone" class="form-control" pattern="^\d{9}$"
                        title="Please enter a valid 9-digit phone number">
                    <p v-else class="form-control-plaintext text-white">{{ user.phone }}</p>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <label class="fw-bold">
                        <i class="bi bi-envelope-at me-2"></i> Email
                    </label>
                    <input v-if="isEditingProfile" v-model="user.email" class="form-control"
                        pattern="^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$"
                        @invalid="$event.target.setCustomValidity('Invalid email address!');"
                        @input="$event.target.setCustomValidity('')" title="Please enter a valid email address">
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

    <!-- ADMIN DASHBOARD ============================================================================ -->

    <div class="container mt-4 mb-5">

        <!-- HEADER ========================================================== -->
        <h4 class="dashboard-title mb-4 p-3 shadow-sm">Admin Dashboard</h4>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link bg-tab" :class="{ active: activeTab === 'reservations' }"
                    @click="activeTab = 'reservations'">
                    <i class="bi bi-calendar-check me-2"></i>Reservations
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link bg-tab" :class="{ active: activeTab === 'vehicles' }"
                    @click="activeTab = 'vehicles'">
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
                <!-- HEADER ================================================== -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="bi bi-car-front"></i> Reservation Management</h4>
                    <button @click="openCreateReservationModal" class="btn bg-primary-cta">
                        <i class="bi bi-bookmark-plus"></i> New Reservation
                    </button>
                </div>
                <!-- FILTERS -->
                <div class="w-25 my-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input v-model="searchReservation" type="text" class="form-control"
                            placeholder="Search (Plate, DNI, Status...)">
                    </div>
                </div>
                <!-- DATA TABLE ============================================== -->
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-car-front me-2"></i> Vehicle</th>
                                <th><i class="bi bi-person me-2"></i> Customer</th>
                                <th><i class="bi bi-calendar me-2"></i> Start Date</th>
                                <th><i class="bi bi-calendar me-2"></i> End Date</th>
                                <th><i class="bi bi-info-circle me-2"></i> Status</th>
                                <th><i class="bi bi-gear me-2"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="isLoadingReservations" class="container text-center py-5">
                                <td colspan="6" class="p-4">
                                    <div class="spinner-border text-light" role="status"></div>
                                    <p class="mt-3 fs-5">Loading Reservations...</p>
                                </td>
                            </tr>
                            <tr v-else v-for="r in filteredReservations" :key="r.id">
                                <td>{{ r.vehicle.license_plate }}</td>
                                <td>{{ r.user.dni }}</td>
                                <td>{{ new Date(r.start_date).toLocaleDateString("es-ES") }}</td>
                                <td>{{ new Date(r.end_date).toLocaleDateString("es-ES") }}</td>
                                <td>{{ r.status }}</td>
                                <td>
                                    <!-- Action buttons -->
                                    <button @click="openEditReservationModal(r)" class="btn btn-sm bg-primary-cta me-2">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button @click="deleteReservation(r.id)" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                <!-- FILTERS -->
                <div class="w-25 my-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input v-model="searchVehicle" type="text" class="form-control"
                            placeholder="Search (Plate, Brand, Model, Status...)">
                    </div>
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
                                <th><i class="bi bi-info-circle me-2"></i> Status</th>
                                <th><i class="bi bi-gear me-2"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="isLoadingVehicles" class="container text-center py-5">
                                <td colspan="6" class="p-4">
                                    <div class="spinner-border text-light" role="status"></div>
                                    <p class="mt-3 fs-5">Loading Vehicles...</p>
                                </td>
                            </tr>
                            <tr v-else v-for="v in filteredVehicles" :key="v.id">
                                <td>{{ v.license_plate }}</td>
                                <td>{{ v.brand }}</td>
                                <td>{{ v.model }}</td>
                                <td>{{ v.manufacturing_year }}</td>
                                <td>{{ v.status }}</td>
                                <td>
                                    <!-- Action Buttons -->
                                    <button @click="openUploadVehicleImagesModal(v)"
                                        class="btn btn-sm bg-primary-cta me-1">
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
                <!-- FILTERS -->
                <div class="w-25 my-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input v-model="searchCustomer" type="text" class="form-control"
                            placeholder="Search (DNI, Name, Email, Phone, ...)">
                    </div>
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
                            <tr v-if="isLoadingCustomers" class="container text-center py-5">
                                <td colspan="6" class="p-4">
                                    <div class="spinner-border text-light" role="status"></div>
                                    <p class="mt-3 fs-5">Loading Customers...</p>
                                </td>
                            </tr>
                            <tr v-else v-for="c in filteredCustomers" :key="c.id">
                                <td>{{ c.dni }}</td>
                                <td>{{ c.first_name }} {{ c.last_name }} {{ c.second_last_name }}</td>
                                <td>{{ c.email }}</td>
                                <td>{{ c.phone }}</td>
                                <td>{{ c.address }}</td>
                                <td>
                                    <!-- Action Buttons -->
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
                                    <input v-model="userForm.dni" :disabled="isEditingCustomer" class="form-control"
                                        pattern="^[0-9]{8}[a-zA-Z]$" maxlength="9" minlength="9"
                                        title="Please enter a valid DNI (8 digits + 1 letter)" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-calendar me-2"></i> Birthday
                                    </label>
                                    <input v-model="userForm.birth_date" type="date" class="form-control"
                                        :class="{ 'is-invalid': isUnderage }" required>
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
                                    <input v-model="userForm.first_name" class="form-control"
                                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$"
                                        title="Please enter a valid name (letters only)" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> First Surname
                                    </label>
                                    <input v-model="userForm.last_name" class="form-control"
                                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$"
                                        title="Please enter a valid surname (letters only)" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> Second Surname
                                    </label>
                                    <input v-model="userForm.second_last_name" class="form-control"
                                        pattern="^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$"
                                        title="Please enter a valid second surname (letters only)">
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">
                                        <i class="bi bi-envelope-at me-2"></i> Email
                                    </label>
                                    <input v-model="userForm.email" type="email" class="form-control"
                                        pattern="^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$"
                                        @invalid="$event.target.setCustomValidity('Invalid email address!');"
                                        @input="$event.target.setCustomValidity('')"
                                        title="Please enter a valid email address" required>
                                </div>
                                <div class="col-md-12" v-if="!isEditingCustomer">
                                    <label class="small fw-bold">
                                        <i class="bi bi-key me-2"></i> Password
                                    </label>
                                    <input v-model="userForm.password" type="password" class="form-control"
                                        minlength="8" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-phone me-2"></i> Phone
                                    </label>
                                    <input v-model="userForm.phone" class="form-control" minlength="9" maxlength="9"
                                        pattern="^\d{9}$" title="Please enter a valid 9-digit phone number" required>
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
                                        class="form-control" pattern="[0-9]{4}\-[A-Z]{3}"
                                        title="Please enter a valid license plate (e.g. 1234-ABC)" required>
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
                                    <input v-model="vehicleForm.manufacturing_year" type="number" minlength="4"
                                        maxlength="4" class="form-control" required>
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

    <!-- UPLOAD VEHICLE IMAGES MODAL ====================================================================== -->
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

    <!-- INSERT RESERVATION MODAL ================================================================== -->
    <div v-if="showReservationModal">
        <div class="modal-backdrop fade show"></div>
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">{{ isEditingReservation ? 'Edit' : 'Create' }} Reservation</h5>
                        <button @click="showReservationModal = false" class="btn-close"></button>
                    </div>
                    <form @submit.prevent="saveReservation">
                        <div class="modal-body">
                            <div class="row g-2">

                                <div class="col-md-12">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> Customer
                                    </label>
                                    <select v-model="reservationForm.user_id" class="form-select" required>
                                        <option value="" disabled>Select a Customer</option>
                                        <option v-for="c in customers" :key="c.id" :value="c.id">
                                            {{ c.first_name }} {{ c.last_name }} (DNI: {{ c.dni }})
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">
                                        <i class="bi bi-car-front me-2"></i> Vehicle
                                    </label>
                                    <select v-model="reservationForm.vehicle_id" class="form-select" required>
                                        <option value="" disabled>Select a Vehicle</option>
                                        <option v-for="v in availableVehicles" :key="v.id" :value="v.id">
                                            {{ v.brand }} {{ v.model }} - {{ v.license_plate }} - {{
                                                v.hourly_price }}€/h
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-calendar-event me-2"></i> Start Date & Time
                                    </label>
                                    <input v-model="reservationForm.start_date" type="datetime-local"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-calendar-event me-2"></i> End Date & Time
                                    </label>
                                    <input v-model="reservationForm.end_date" type="datetime-local" class="form-control"
                                        required>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div
                                        class="p-3 bg-light border rounded d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="small fw-bold d-block text-muted">Total Amount</span>
                                        </div>
                                        <span class="fs-4 fw-bold text-primary">
                                            {{ estimatedPrice }} €
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2" v-if="isEditingReservation">
                                    <label class="small fw-bold">Status</label>
                                    <select v-model="reservationForm.status" class="form-select">
                                        <option value="pending">pending</option>
                                        <option value="confirmed">confirmed</option>
                                        <option value="cancelled">cancelled</option>
                                        <option value="completed">completed</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="showReservationModal = false" class="btn bg-primary-cta">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn bg-primary-cta">
                                <i class="bi bi-floppy me-2"></i> Save Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>