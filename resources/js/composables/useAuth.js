import { ref, computed } from "vue";
import axios from "axios";

const user = ref(null);
const isInitialized = ref(false);

export function useAuth() {
    const fetchUser = async () => {
        if (isInitialized.value) return;

        try {
            const response = await axios.get("/user/me");
            user.value = response.data;
        } catch (error) {
            user.value = null;
        } finally {
            isInitialized.value = true;
        }
    };

    const login = async (formData) => {
        await axios.post("/login", formData);
        await fetchUser();
    };

    const logout = async () => {
        try {
            await axios.post("/logout");
        } catch (e) {
            /* ignorar error */
        }

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
