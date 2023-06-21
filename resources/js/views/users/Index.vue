<template>
    <div class="w-96 mx-auto">
      <div v-if="users">
        <div class="flex justify-between items-center mb-6 pb-6 border-b border-gray-500" v-for="user in users">
            <router-link :to="{name:'user.show', params:{id: user.id}}">
                <p>{{user.id}}</p>
                <p>{{user.name}}</p>
                <p>{{user.email}}</p>
            </router-link>
            <div>
                <a @click.prevent="toggleFollowing(user)" href="#"
                   :class="['block p-2 w-32 rounded-3xl text-center',
                   user.is_followed ? 'bg-red-500 text-white hover:bg-white hover:border hover:border-red-500 hover:text-red-500' : 'bg-blue-500 text-white hover:bg-white hover:border hover:border-blue-500 hover:text-blue-500' ]">{{user.is_followed ? 'Unfollow' : 'Follow'}}</a>
            </div>
        </div>
      </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Index",

    data() {
        return {
            users: []
        }
    },

    mounted() {
      this.getUsers()
    },

    methods: {
        getUsers(){
            axios.get('/api/users')
                .then(res => {
                    this.users = res.data.data
                })
        },

        toggleFollowing(user){
            axios.post(`/api/users/${user.id}/toggle_following`)
                .then(res =>{
                    user.is_followed = res.data.is_followed
                    console.log(res.data.is_followed)
                })
        },
    }
}
</script>

<style scoped>

</style>
