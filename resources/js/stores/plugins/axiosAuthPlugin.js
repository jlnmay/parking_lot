import axios from "axios";

export function axiosAuthPlugin({ store }) {
    if (store.$id !== "auth") {
        return;
    }

    axios.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error.response?.status === 401) {
                store.user = null;
            }
            return Promise.reject(error);
        },
    );
}
