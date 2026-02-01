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
    const isEditingCustomer = ref(false);
    const showModal = ref(false);

    const form = reactive({
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

    // REQUEST =================================================
    const fetchCustomers = async () => {
        const res = await axios.get('/admin/customers');
        customers.value = res.data;
    };

    // LOAD DATA ===============================================
    onMounted(fetchCustomers);

    // CREATE CUSTOMER MODAL ===================================
    const openCreateModal = () => {
        isEditingCustomer.value = false;
        Object.assign(form, { 
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
        showModal.value = true;
    };

    // EDIT CUSTOMER MODAL =====================================
    const openEditModal = (customer) => {
        isEditingCustomer.value = true;
        Object.assign(form, customer);
        form.password = ''; // No editamos password aquí por simplicidad
        showModal.value = true;
    };

    // SAVE CUSTOMER ===========================================
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

    // DELETE CUSTOMER =========================================
    const deleteCustomer = async (id) => {
        if (confirm('Are you sure you want to delete this customer?')) {
            await axios.delete(`/admin/customers/${id}`);
            fetchCustomers();
        }
    };

    // AGE VALIDATION ==========================================
    const age = computed(() => {
        if (!form.birth_date) return null;
        const birth = new Date(form.birth_date);
        const today = new Date();
        let ageCalc = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            ageCalc--;
        }
        return ageCalc;
    });

    const isUnderage = computed(() => age.value !== null && age.value < 18);

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
                <h4><i class="bi bi-car-front"></i> Vehicle Fleet</h4>
                <p class="text-muted">Proximamente: Gestión de flota.</p>
            </div>
            
            <!-- CUSTOMERS =================================================== -->
            <div v-if="activeTab === 'users'">
                <!-- HEADER ================================================== -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="bi bi-people"></i> Customer Directory</h4>
                    <button @click="openCreateModal" class="btn bg-primary-cta">
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
                                    <button @click="openEditModal(c)" class="btn btn-sm bg-primary-cta me-2">
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

    <div v-if="showModal">
        <div class="modal-backdrop fade show"></div>
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">{{ isEditingCustomer ? 'Edit' : 'Create' }} Customer</h5>
                        <button @click="showModal = false" class="btn-close"></button>
                    </div>
                    <form @submit.prevent="saveCustomer">
                        <div class="modal-body">
                            <div class="row g-2">
                                
                                <!-- DATA ============================================================ -->
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-card-text me-2"></i> DNI
                                    </label>
                                    <input v-model="form.dni" :disabled="isEditingCustomer" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-calendar me-2"></i> Birthday
                                    </label>
                                    <input 
                                        v-model="form.birth_date" 
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
                                    <input v-model="form.first_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> First Surname
                                    </label>
                                    <input v-model="form.last_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        <i class="bi bi-person me-2"></i> Second Surname
                                    </label>
                                    <input v-model="form.second_last_name" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">
                                        <i class="bi bi-envelope-at me-2"></i> Email
                                    </label>
                                    <input v-model="form.email" type="email" class="form-control" required>
                                </div>
                                <div class="col-md-12" v-if="!isEditingCustomer">
                                    <label class="small fw-bold">
                                        <i class="bi bi-key me-2"></i> Password
                                    </label>
                                    <input v-model="form.password" type="password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-phone me-2"></i> Phone
                                    </label>
                                    <input v-model="form.phone" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">
                                        <i class="bi bi-geo-alt me-2"></i> Address
                                    </label>
                                    <input v-model="form.address" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="showModal = false" class="btn bg-primary-cta">
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
</template>