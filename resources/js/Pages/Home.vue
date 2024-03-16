<script setup>
import { ref, onMounted } from "vue";
import userStore from "../store/UserStore.js";
import { useRouter } from "vue-router";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import Swal from "sweetalert2";

const isloading = ref(false);
const router = useRouter();
const store = userStore();
const authUser = ref({});
const selectedPerson = ref({});
const listPerson = ref({
    data: [],
    message: [],
    choose: false,
});

const viewPerson = (data) => {
    selectedPerson.value = data;
    if (!listPerson.value.choose) {
        listPerson.value.choose = true;
    }
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
            store.logout().then(() => {
                router.push({ name: "login" });
            });
        }
    });
};

onMounted(async () => {
    try {
        isloading.value = true;
        authUser.value = await store.getUser();
        listPerson.value.data = await store.getListPerson();
        window.Echo.channel("My-app.", authUser.value.id).listen(
            ".my-app",
            function (data) {
                alert(data.message);
            }
        );
    } catch (err) {
        console.log(err);
    } finally {
        isloading.value = false;
    }
});
</script>

<template>
    <div v-if="isloading" class="vl-parent">
        <loading
            :active="isloading"
            is-full-page="fullPage"
            height="70"
            width="70"
            loader="bars"
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
                                            :src="selectedPerson.avatar"
                                            alt="avatar"
                                        />
                                    </a>
                                    <div class="chat-about">
                                        <h6 class="m-b-0">
                                            {{ selectedPerson.name }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chat-history" style="min-height: 70vh">
                            <div
                                v-if="
                                    listPerson.choose &&
                                    listPerson.message.length > 0
                                "
                            >
                                <ul class="m-b-0">
                                    <li class="clearfix">
                                        <div class="message-data text-right">
                                            <span class="message-data-time"
                                                >10:10 AM, Today</span
                                            >
                                        </div>
                                        <div
                                            class="message other-message float-right"
                                        >
                                            Hi Aiden, how are you? How is the
                                            project coming along?
                                        </div>
                                    </li>
                                    <li class="clearfix">
                                        <div class="message-data">
                                            <span class="message-data-time"
                                                >10:12 AM, Today</span
                                            >
                                        </div>
                                        <div class="message my-message">
                                            Are we meeting today?
                                        </div>
                                    </li>
                                    <li class="clearfix">
                                        <div class="message-data">
                                            <span class="message-data-time"
                                                >10:15 AM, Today</span
                                            >
                                        </div>
                                        <div class="message my-message">
                                            Project has been already finished
                                            and I have results to show you.
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div
                                v-else-if="
                                    listPerson.choose &&
                                    listPerson.message.length == 0
                                "
                                class="text-center"
                                style="
                                    position: absolute;
                                    bottom: 50%;
                                    left: 55%;
                                "
                            >
                                <h5>Send a Massage</h5>
                            </div>
                            <div
                                class="d-flex justify-content-center align-items-center"
                                style="height: 70vh"
                                v-else="listPerson.choose"
                            >
                                <h4>Welcome to Chat App</h4>
                            </div>
                        </div>
                        <div class="chat-message clearfix">
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
                                    class="form-control shadow-none"
                                    placeholder="Enter text here..."
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
