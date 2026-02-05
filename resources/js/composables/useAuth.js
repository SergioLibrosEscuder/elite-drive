import { ref, computed } from "vue";
import axios from "axios";

const user = ref(null);
const isInitialized = ref(false);

export function useAuth() {
    const fetchUser = async () => {
        if (isInitialized.value) return;

        const hasLoginFlag = localStorage.getItem("is_logged_in");
        if (!hasLoginFlag) {
            user.value = null;
            isInitialized.value = true;
            return;
        }

        try {
            const response = await axios.get("/user/me");
            user.value = response.data;
        } catch (error) {
            user.value = null;
            localStorage.removeItem("is_logged_in");
        } finally {
            isInitialized.value = true;
        }
    };

    const login = async (formData) => {
        await axios.post("/login", formData);

        localStorage.setItem("is_logged_in", "true");
        isInitialized.value = false;
        await fetchUser();
    };

    const logout = async () => {
        try {
            await axios.post("/logout");
        } catch (e) {
            /* Ignorar error */
        }
        localStorage.removeItem("is_logged_in");
        user.value = null;
        isInitialized.value = true;
    };

    const isAdmin = computed(() => user.value?.role === "admin");
    const isCustomer = computed(() => user.value?.role === "customer");
    const isAuthenticated = computed(() => !!user.value);

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
