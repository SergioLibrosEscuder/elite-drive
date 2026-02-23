// Set axios configuration
import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// Axios interceptor to handle 419 errors globally
window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        // Store the original request configuration
        const originalRequest = error.config;

        // Verify if the error is a 419 and we haven't already tried to refresh the token
        if (
            error.response &&
            error.response.status === 419 &&
            !originalRequest._retry &&
            originalRequest.url !== "/sanctum/csrf-cookie"
        ) {
            // Mark the original request to avoid infinite loops
            originalRequest._retry = true;

            try {
                // Get a new CSRF token
                await window.axios.get("/sanctum/csrf-cookie");

                // Extract the new CSRF token from cookies
                const value = `; ${document.cookie}`;
                const parts = value.split(`; XSRF-TOKEN=`);

                // Check if the token was found in the cookies
                if (parts.length === 2) {
                    // Decode the token value and remove any trailing semicolons
                    const token = decodeURIComponent(
                        parts.pop().split(";").shift(),
                    );

                    // Update the original request's headers with the new CSRF token
                    originalRequest.headers["X-XSRF-TOKEN"] = token;
                }

                // Retry the original request with the new CSRF token
                return window.axios(originalRequest);
            } catch (retryError) {
                // If the retry also fails, reject the promise with the retry error
                return Promise.reject(retryError);
            }
        }
        // For other errors, reject the promise with the original error
        return Promise.reject(error);
    },
);
