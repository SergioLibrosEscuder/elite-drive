// Sergio Libros

import { ref, computed } from "vue";
import axios from "axios";

// User reference
const user = ref(null);
// Boolean to check is user is already checked
const isInitialized = ref(false);

export function useAuth() {
    // Get actual user
    const fetchUser = async () => {
        if (isInitialized.value) return;

        // Check if user is logged in
        const hasLoginFlag = localStorage.getItem("is_logged_in");

        // If not, there's no user
        if (!hasLoginFlag) {
            user.value = null;
            isInitialized.value = true;
            return;
        }

        // If there's user, get data
        try {
            const response = await axios.get("/user/me");
            user.value = response.data;
        } catch (error) {
            // If there's an error, remove user reference and logged flag
            user.value = null;
            localStorage.removeItem("is_logged_in");
        } finally {
            isInitialized.value = true;
        }
    };

    // Login method with form data
    const login = async (formData) => {
        // Get CSRF cookie before login
        await axios.get("/sanctum/csrf-cookie");
        // Post login data
        await axios.post("/login", formData);

        // Set logged flag
        localStorage.setItem("is_logged_in", "true");
        isInitialized.value = false;
        // Get logged user
        await fetchUser();
    };

    // Logout method
    const logout = async () => {
        try {
            await axios.post("/logout");
        } catch (e) {
            // The error will be handled by the axios interceptor in bootstrap.js
        }
        // Remove logged flag and user reference
        localStorage.removeItem("is_logged_in");
        user.value = null;
        isInitialized.value = true;
    };

    // Define if current user is an admin
    const isAdmin = computed(() => user.value?.role === "admin");
    // Define if current user is a customer
    const isCustomer = computed(() => user.value?.role === "customer");
    // Define if current user is authenticated (if is logged in)
    const isAuthenticated = computed(() => !!user.value); // The '!!' is to ensure the value is a boolean

    return {
        user,
        fetchUser,
        login,
        logout,
        isAdmin,
        isCustomer,
        isAuthenticated,
        isInitialized,
    };
}
