<template>
    <div>
        <h1 class="text-3xl font-bold underline w-96 mx-auto text-center">
            My app!
        </h1>
        <div class="flex justify-between p-8 w-96 mx-auto">
            <router-link v-if="!token" :to="{ name: 'user.login'}">Login</router-link>
            <router-link v-if="token" :to="{ name: 'user.index'}">Users</router-link>
            <router-link v-if="token" :to="{ name: 'user.feed'}">Feed</router-link>
            <router-link v-if="token" :to="{ name: 'user.personal'}">Personal</router-link>
            <router-link v-if="!token" :to="{ name: 'user.registration'}">Registration</router-link>
            <a v-if="token" @click.prevent="logout" href="#">Logout</a>
        </div>
        <router-view></router-view>
    </div>
</template>
<script>
export default {
    name: "App.vue",
    data(){
        return {
            token:null,

        }
    },

    // updated() {
    //     this.getToken()
    // },

    watch:{
        $route(to, from){
            this.getToken()
        }
    },

    mounted() {
        this.getToken()
    },

    methods:{
        logout(){
            axios.post('/logout')
                .then(res =>{
                    localStorage.removeItem('x_xsrf_token')
                    this.$router.push({name: 'user.login'})
                })
        },
        getToken(){
            this.token = localStorage.getItem('x_xsrf_token')
        }
    }
}
</script>

<style scoped>

</style>
