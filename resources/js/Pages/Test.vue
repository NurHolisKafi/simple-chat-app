<template>
    <div class="home">
        <form @submit.prevent="logIn">
            <loading
                :active="isLoading"
                :is-full-page="fullPage"
                :loader="loader"
            />
            <label for="name" class="label">Name</label>
            <input type="text" name="name" class="input__field" id="name" />
            <label for="email" class="label">Email Address</label>
            <input type="email" name="email" class="input__field" id="email" />
            <button type="submit" class="submit__button">Submit</button>
        </form>
    </div>
</template>
<script>
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
export default {
    name: "Home",
    data() {
        return {
            isLoading: false,
            fullPage: false,
            loader: "bars",
        };
    },
    components: {
        Loading,
    },
    methods: {
        logIn() {
            this.isLoading = true;
            setTimeout(() => {
                this.isLoading = false;
            }, 3000);
        },
    },
};
</script>
<style scoped>
.home {
    width: 500px;
    margin: auto;
    height: 500px;
    text-align: left;
}
form {
    width: 100%;
    max-width: 300px;
    margin: auto;
    /* important if you do not want fullscreen loader */
    position: relative;
}
.label {
    display: block;
    width: 100%;
    margin-bottom: 10px;
}
.input__field {
    margin-bottom: 10px;
    height: 40px;
    border: 1px solid #e4e4e4;
    border-radius: 5px;
    padding-left: 10px;
    width: 250px;
}
.submit__button {
    width: 80px;
    height: 40px;
    display: block;
    margin: 20px auto;
    background-color: #fff;
    border: 1px solid #e4e4e4;
    box-shadow: 1px 2px 4px 1px #e4e4e4;
}
</style>
