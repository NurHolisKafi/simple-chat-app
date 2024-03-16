import axios from "axios";
import { defineStore } from "pinia";

const userStore = defineStore("auth", {
    state: () => ({
        auth: localStorage.getItem("auth") ?? false,
        token: localStorage.getItem("token"),
    }),
    actions: {
        async getUser() {
            if (this.token) {
                const response = await axios.get("/api/user/", {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                });
                return response.data;
            } else {
                return null;
            }
        },
        async getListPerson(id) {
            if (this.token) {
                const response = await axios.get("/api/persons/", {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                });

                return response.data.data;
            } else {
                return null;
            }
        },

        async logout() {
            try {
                await axios.get("/api/logout", {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                });
                localStorage.removeItem("token");
                localStorage.removeItem("auth");
                this.$patch({
                    auth: false,
                    token: null,
                });
            } catch (err) {
                console.log(err);
            }
        },
    },
});

export default userStore;
