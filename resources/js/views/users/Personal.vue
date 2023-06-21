<template>
    <div class="w-96 mx-auto">
        <Stat :stat="stat"></Stat>
        <div class="mb-4">
            <div class="mb-2">
                <input v-model="title" type="text" placeholder="title" class=" w-96 rounded-3xl border p-2 border-slate-300">
                <div v-if="errors.title">
                    <p v-for="error in errors.title" class="text-red-500 text-sm mb-4 mt-2 ml-3" >{{error}}</p>
                </div>
            </div>
            <div class="mb-2">
                <textarea v-model="content" placeholder="content" class=" w-96 rounded-3xl border p-2 border-slate-300"></textarea>
                <div v-if="errors.content">
                    <p v-for="error in errors.content" class="text-red-500 text-sm mb-4 mr-4 ml-3">{{error}}</p>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <input @change="storeImage" ref="file" type="file" class="hidden">
                    <a href="#" class="block p-2 w-16 mb-3 bg-sky-500  rounded-3xl text-center text-white
                    hover:bg-white hover:border text-sm hover:text-sky-500 hover:border-sky-500 box-border" @click.prevent="selectFile()">Image</a>
                </div>
                <div class="">
                    <a @click.prevent="image=null" href="#" class="block border p-2 w-16 mb-3 bg-red-500  rounded-3xl text-center text-white
                    hover:bg-white hover:border text-sm hover:text-red-500 hover:border-red-500 box-border">Cancel</a>
                </div>
            </div>
            <div v-if="image" class="mb-3">
                <img :src="image.url" alt="image">
            </div>
            <div >
                <a href="#" @click.prevent="store" class="block p-2 w-32 bg-green-600 rounded-3xl text-center text-white
                    hover:bg-white hover:border hover:text-green-600 hover:border-green-600 box-border ml-auto">Publish</a>
            </div>
        </div>
        <div v-if="posts">
            <h1 class="mb-8 text-3xl pb-8 border-b border-gray-500">Posts</h1>
            <Post v-for="post in posts" :post="post"></Post>
        </div>
    </div>
</template>
<script>

import axios from "axios";
import Post from "../../components/Post.vue";
import Stat from "../../components/Stat.vue";

export default {
    name: "Personal",

    data(){
        return {
            title: '',
            content: '',
            image: null,
            posts: [],
            errors:[],
            stat: []
        }
    },
    components:{
        Stat,
      Post
    },

    mounted() {
        this.getPosts()
        this.getStat()
    },

    methods: {
        getPosts(){
          axios.get('/api/posts')
              .then(res =>{
                  console.log(res.data.data)
                  this.posts = res.data.data
              })
        },
        store(){
            const id = this.image ? this.image.id : null
          axios.post('/api/posts', {title: this.title, content: this.content, image_id: id})
              .then(res => {
                  this.image = null
                  this.title = ''
                  this.content = ''
                  this.posts.unshift(res.data.data)
              })
              .catch(e => {
                  this.errors = e.response.data.errors
              })
        },
        selectFile() {
            this.fileInput = this.$refs.file;
            this.fileInput.click();
        },
        storeImage(e){
            const file = e.target.files[0];
            const formData = new FormData()
            formData.append('image', file)

            axios.post('/api/post_images', formData)
                .then(res =>{
                    this.image = res.data.data
                })
        },

        getStat(){
            axios.post('/api/users/stat')
                .then(res => {
                    console.log(res.data.data)
                    this.stat = res.data.data
                })
        }
    },

}
</script>

<style scoped>

</style>
