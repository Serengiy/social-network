<template>
    <div class="w-96 mx-auto">
        <div v-if="posts">
            <h1 class="mb-8 text-3xl pb-8 border-b border-gray-500">Posts</h1>
            <Post v-for="post in posts" :post="post"></Post>
        </div>
    </div>
</template>
<script>

import axios from "axios";
import Post from "../../components/Post.vue";

export default {
    name: "Feed",
    components: {Post},

    data(){
        return {
            posts: [],
        }
    },

    mounted() {
        this.getPosts()
    },

    methods: {
        getPosts(){
          axios.get(`/api/users/following_posts`)
              .then(res =>{
                  // console.log(res)
                  this.posts = res.data.data
              })
        },
    },

}
</script>

<style scoped>

</style>
