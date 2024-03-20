<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";
import userStore from "../store/UserStore.js";

const store = userStore();
const isLoading = ref(false);
const router = useRouter();

const alert = (type, title, text, route = null) => {
    Swal.fire({
        title: title,
        text: text,
        icon: type,
        confirmButtonText: "OK",
    }).then((result) => {
        if (result.isConfirmed && route != null) {
            router.push(route);
        }
    });
};
let form = ref({
    username: "",
    password: "",
});

const postData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.post("/api/user/auth", form.value, {
            headers: {
                Accept: "application/json",
            },
        });
        store.$patch({
            auth: true,
            token: response.data.token,
        });
        localStorage.setItem("auth", true);
        localStorage.setItem("token", response.data.token);
        console.log(Window.Echo);
        alert("success", "Berhasil", "Login Berhasil", "/");
    } catch (error) {
        console.log(error);
        alert("error", "Gagal", "Username Tidak Ditemukan");
    } finally {
        isLoading.value = false;
    }
};
</script>
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-5">
                <div class="card px-3 py-5">
                    <h3 class="card-title text-center m-0">Login Page</h3>
                    <img
                        class="mx-auto"
                        src="/icon/user-login.png"
                        alt="icon user"
                        width="50%"
                    />
                    <div class="card-body m-0">
                        <form @submit.prevent="postData">
                            <div class="form-group">
                                <label>Username</label>
                                <input
                                    class="form-control shadow-none"
                                    type="text"
                                    v-model="form.username"
                                    placeholder="username"
                                    autocomplete="name"
                                    required
                                    oninvalid="this.setCustomValidity('Username tidak boleh kosong')"
                                    oninput="this.setCustomValidity('')"
                                />
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input
                                    class="form-control shadow-none"
                                    type="text"
                                    v-model="form.password"
                                    placeholder="password"
                                    required
                                    oninvalid="this.setCustomValidity('Password tidak boleh kosong')"
                                    oninput="this.setCustomValidity('')"
                                />
                            </div>
                            <button
                                class="btn btn-primary form-control shadow-none mt-3"
                                type="submit"
                                :disabled="isLoading"
                            >
                                <div
                                    v-if="isLoading"
                                    class="spinner-border"
                                    style="width: 1.3rem; height: 1.3rem"
                                    role="status"
                                ></div>
                                <div v-else>Log in</div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
