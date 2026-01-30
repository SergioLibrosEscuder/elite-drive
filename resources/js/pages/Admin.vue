<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

// Mostrar y editar perfil de usuario
// Control de la pestaña activa
const activeTab = ref('users'); // Por defecto mostramos usuarios

const isEditingProfile = ref(false);
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
        isEditingProfile.value = false;
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


    // ADMIN DASHBOARD ==================================================================================

    const customers = ref([]);
    const isEditingCustomer = ref(false);
    const showModal = ref(false);

    const form = reactive({
        id: null, dni: '', first_name: '', last_name: '', email: '', password: '', phone: '', address: ''
    });

    const fetchCustomers = async () => {
        const res = await axios.get('/admin/customers');
        customers.value = res.data;
    };

    const openCreateModal = () => {
        isEditingCustomer.value = false;
        Object.assign(form, { id: null, dni: '', first_name: '', last_name: '', email: '', password: '', phone: '', address: '' });
        showModal.value = true;
    };

    const openEditModal = (customer) => {
        isEditingCustomer.value = true;
        Object.assign(form, customer);
        form.password = ''; // No editamos password aquí por simplicidad
        showModal.value = true;
    };

    const saveCustomer = async () => {
        try {
            if (isEditingCustomer.value) {
                await axios.put(`/admin/customers/${form.id}`, form);
            } else {
                await axios.post('/admin/customers', form);
            }
            showModal.value = false;
            fetchCustomers();
            alert('Success!');
        } catch (e) { alert('Error saving customer'); }
    };

    const deleteCustomer = async (id) => {
        if (confirm('Are you sure you want to delete this customer?')) {
            await axios.delete(`/admin/customers/${id}`);
            fetchCustomers();
        }
    };

    onMounted(fetchCustomers);
    
</script>

<template>

    <!-- USER DATA DISPLAY ========================================================================== -->

    <div class="container mt-5">
        <div class="card shadow">

            <!-- HEADER ========================================================== -->

            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">My Profile</h4>
                <button @click="isEditingProfile = !isEditingProfile" class="btn btn-sm btn-outline-light">
                    {{ isEditingProfile ? 'Cancel' : 'Edit Profile' }}
                </button>
            </div>

            <!-- DATA ============================================================ -->

            <div class="card-body">

                <!-- DATA ============================================================ -->
                <div class="row g-3">
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Name</label>
                        <input v-if="isEditingProfile" v-model="user.first_name" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.first_name }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">First surname</label>
                        <input v-if="isEditingProfile" v-model="user.last_name" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.last_name }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Second surname</label>
                        <input v-if="isEditingProfile" v-model="user.second_last_name" class="form-control">
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
                        <input v-if="isEditingProfile" v-model="user.phone" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.phone }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Email</label>
                        <input v-if="isEditingProfile" v-model="user.email" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.email }}</p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <label class="fw-bold">Address</label>
                        <input v-if="isEditingProfile" v-model="user.address" class="form-control">
                        <p v-else class="form-control-plaintext">{{ user.address }}</p>
                    </div>
                </div>

                <div v-if="isEditingProfile" class="mt-4">
                    <button @click="saveProfile" class="btn btn-success">Save Changes</button>
                </div>

                <hr class="my-4">
                
                <!-- PASSWORD ======================================================== -->
                <button @click="showPasswordSection = !showPasswordSection" class="btn btn-outline-danger">
                    <i class="bi bi-key me-2"></i>Change Password
                </button>

                <!-- Detalle pendiente cambiar texto del botón pero mantener icono y estilos -->
                <!-- <button @click="showPasswordSection = !showPasswordSection" class="btn btn-sm btn-outline-light">
                    {{ showPasswordSection ? 'Cancel' : 'Change Password' }}
                </button> -->

                <div v-if="showPasswordSection" class="card mt-3 bg-light">
                    <div class="card-body">
                        <div class="mb-2"><input type="password" v-model="passForm.current_password" placeholder="Current Password" class="form-control"></div>
                        <div class="mb-2"><input type="password" v-model="passForm.password" placeholder="New Password" class="form-control"></div>
                        <div class="mb-2"><input type="password" v-model="passForm.password_confirmation" placeholder="Confirm New Password" class="form-control"></div>
                        <button @click="changePassword" class="btn btn-danger">Update Password</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ADMIN DASHBOARD ============================================================================ -->

    <div class="container mt-4 mb-5">
        
        <!-- HEADER ========================================================== -->
        <h4 class="mb-4 text-white bg-dark p-3 rounded shadow-sm">Admin Dashboard</h4>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link" :class="{ active: activeTab === 'reservations' }" @click="activeTab = 'reservations'">
                    <i class="bi bi-calendar-check me-2"></i>Reservations
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: activeTab === 'vehicles' }" @click="activeTab = 'vehicles'">
                    <i class="bi bi-car-front me-2"></i>Vehicles
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: activeTab === 'users' }" @click="activeTab = 'users'">
                    <i class="bi bi-people me-2"></i>Customers
                </button>
            </li>
        </ul>
        
        <!-- TAB ============================================================= -->
        <div class="tab-content border p-4 bg-white rounded shadow-sm min-vh-50">
            
            <!-- RESERVATIONS ================================================ -->
            <div v-if="activeTab === 'reservations'">
                <h4><i class="bi bi-calendar-check"></i> Reservation Management</h4>
                <p class="text-muted">Proximamente: Gestión de reservas.</p>
            </div>
            
            <!-- VEHICLES ==================================================== -->
            <div v-if="activeTab === 'vehicles'">
                <h4><i class="bi bi-car-front"></i> Vehicle Fleet</h4>
                <p class="text-muted">Proximamente: Gestión de flota.</p>
            </div>
            
            <!-- CUSTOMERS =================================================== -->
            <div v-if="activeTab === 'users'">
                <!-- HEADER ================================================== -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="bi bi-people"></i> Customer Directory</h4>
                    <button @click="openCreateModal" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>New Customer
                    </button>
                </div>
                <!-- DATA TABLE ============================================== -->
                <table class="table table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th>DNI</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Actions</th>
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
                                <button @click="openEditModal(c)" class="btn btn-sm btn-warning me-2"><i class="bi bi-pencil"></i></button>
                                <button @click="deleteCustomer(c.id)" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- INSERT CUSTOMER MODAL ====================================================================== -->

    <div v-if="showModal">
        <div class="modal-backdrop fade show"></div>
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditingCustomer ? 'Edit' : 'Create' }} Customer</h5>
                        <button @click="showModal = false" class="btn-close"></button>
                    </div>
                    <form @submit.prevent="saveCustomer">
                        <div class="modal-body">
                            <input v-model="form.dni" :disabled="isEditingCustomer" placeholder="DNI" class="form-control mb-2" required>
                            <input v-model="form.first_name" placeholder="First Name" class="form-control mb-2" required>
                            <input v-model="form.last_name" placeholder="Last Name" class="form-control mb-2" required>
                            <input v-model="form.email" type="email" placeholder="Email" class="form-control mb-2" required>
                            <input v-if="!isEditingCustomer" v-model="form.password" type="password" placeholder="Password" class="form-control mb-2" required>
                            <input v-model="form.phone" placeholder="Phone" class="form-control mb-2">
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="showModal = false" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>