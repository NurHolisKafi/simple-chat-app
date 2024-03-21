<script setup>
import { ref, onMounted, watch, inject } from "vue";
import userStore from "../store/UserStore.js";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import { DateTime } from "luxon";

const pageloading = ref(false);
const form = ref({
    message: "",
});
const router = useRouter();
const store = userStore();
const authUser = ref({});
const selectedPerson = ref({
    data: {},
    message: [],
    isloading: false,
});
const listPerson = ref({
    data: [],
    choose: false,
});

watch(
    () => selectedPerson.value.data,
    async (newValue) => {
        try {
            selectedPerson.value.isloading = true;
            selectedPerson.value.message = await store.getMessages(newValue.id);
        } finally {
            selectedPerson.value.isloading = false;
        }
    },
    { deep: true }
);

const time = (data) => {
    let sekarang = DateTime.fromFormat(data, "yyyy-MM-dd HH:mm:ss").setLocale(
        "id"
    );

    // Format waktu sesuai dengan format yang diinginkan
    const waktuFormat = sekarang.toLocaleString({
        hour: "numeric",
        minute: "2-digit",
        day: "numeric",
        month: "long",
    });
    const newFormat = waktuFormat.replace("pukul", "");

    return newFormat;
};

const viewPerson = (data) => {
    selectedPerson.value.data = data;
    if (!listPerson.value.choose) {
        listPerson.value.choose = true;
    }
};

const sendMessage = () => {
    let body = {
        user1: authUser.value.id,
        user2: selectedPerson.value.data.id,
        message: form.value.message,
        time: DateTime.now().toFormat("yyyy-MM-dd HH:mm:ss"),
    };
    selectedPerson.value.message.push(body);
    form.value.message = "";
    store
        .sendMessages(body)
        .then((response) => console.log(response))
        .catch((err) => console.log(err));
};

const logout = () => {
    Swal.fire({
        title: "Confirmation",
        text: "Apakah Kamu Yakin Ingin Keluar",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Keluar",
        cancelButtonText: "Cancel",
        cancelButtonColor: "#DC3545",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            store.logout(authUser.value.id).then(() => {
                router.push({ name: "login" });
            });
        }
    });
};

onMounted(async () => {
    try {
        pageloading.value = true;
        authUser.value = await store.getUser();
        listPerson.value.data = await store.getListPerson();
        window.Echo.private("My-app." + authUser.value.id).listen(
            ".my-app",
            function (data) {
                selectedPerson.value.message.push(data);
            }
        );
    } catch (err) {
        console.log(err);
    } finally {
        pageloading.value = false;
    }
});
</script>

<template>
    <div v-if="pageloading" class="vl-parent">
        <loading
            :active="pageloading"
            :is-full-page="true"
            :height="70"
            :width="70"
            loader="dots"
            color="#A077FF"
        />
    </div>
    <div class="container" v-else>
        <div class="row justify-content-between mb-2">
            <div class="col-3">
                <p>Login as {{ authUser.name }}</p>
            </div>
            <div class="col-2">
                <button class="btn btn-secondary" @click="logout">
                    Logout
                </button>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card chat-app">
                    <div id="plist" class="people-list">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"
                                    ><i class="fa fa-search"></i
                                ></span>
                            </div>
                            <input
                                type="text"
                                class="form-control shadow-none"
                                placeholder="Search..."
                            />
                        </div>
                        <ul class="list-unstyled chat-list mt-2 mb-0">
                            <li
                                class="clearfix"
                                v-for="data in listPerson.data"
                                :key="data.id"
                                @click="viewPerson(data)"
                            >
                                <img :src="data.avatar" alt="avatar" />
                                <div class="about">
                                    <div class="name">{{ data.name }}</div>
                                    <div class="status">
                                        <i class="fa fa-circle online"></i>
                                        online
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="chat">
                        <div
                            class="chat-header clearfix"
                            :class="{ 'chat-border': listPerson.choose }"
                        >
                            <div class="row">
                                <div
                                    class="col-lg-6"
                                    v-show="listPerson.choose"
                                >
                                    <a href="javascript:void(0);">
                                        <img
                                            :src="selectedPerson.data.avatar"
                                            alt="avatar"
                                        />
                                    </a>
                                    <div class="chat-about">
                                        <h6 class="m-b-0">
                                            {{ selectedPerson.data.name }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="chat-history vl-parent"
                            style="height: 72vh; box-sizing: border-box"
                            :class="{
                                scroll:
                                    listPerson.choose &&
                                    selectedPerson.message.length > 0,
                            }"
                        >
                            <loading
                                v-if="selectedPerson.isloading"
                                :active="selectedPerson.isloading"
                                :is-full-page="false"
                                height="70"
                                width="70"
                                loader="spinner"
                                color="#A077FF"
                            />
                            <div
                                v-else-if="
                                    listPerson.choose &&
                                    selectedPerson.message.length > 0
                                "
                            >
                                <ul class="m-b-0">
                                    <li
                                        class="clearfix"
                                        v-for="data in selectedPerson.message"
                                    >
                                        <div
                                            class="message-data"
                                            :class="{
                                                'text-right':
                                                    data.user2 == authUser.id,
                                            }"
                                        >
                                            <span class="message-data-time">{{
                                                time(data.time)
                                            }}</span>
                                        </div>
                                        <div
                                            class="message"
                                            :class="{
                                                'float-right other-message':
                                                    data.user2 == authUser.id,
                                                'my-message':
                                                    data.user2 != authUser.id,
                                            }"
                                        >
                                            {{ data.message }}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div
                                v-else-if="
                                    listPerson.choose &&
                                    selectedPerson.message.length == 0
                                "
                                class="text-center"
                                style="
                                    position: absolute;
                                    bottom: 50%;
                                    left: 40%;
                                "
                            >
                                <h5>Send a Massage</h5>
                            </div>
                            <div
                                style="position: absolute; left: 35%; top: 50%"
                                v-else="listPerson.choose"
                            >
                                <h4>Welcome to Chat App</h4>
                            </div>
                        </div>
                        <div class="px-3 py-3">
                            <form @submit.prevent="sendMessage">
                                <div
                                    class="input-group mb-0"
                                    v-show="listPerson.choose"
                                >
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                            ><i class="fa fa-send"></i
                                        ></span>
                                    </div>
                                    <input
                                        type="text"
                                        v-model="form.message"
                                        class="form-control shadow-none"
                                        placeholder="Enter text here..."
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scroll {
    overflow-x: hidden;
    overflow-y: scroll;
}
</style>
