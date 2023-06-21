<template>
    <div class="mb-8 pb-8 border-b border-gray-500">
        <h1 class="text-xl"> {{ post.title }} </h1>
        <img class="my-4 mx-auto" v-if="post.image" :src="post.image" :alt="post.title">
        <p>{{post.content}}</p>
        <div v-if="post.reposted_posts" class="bg-gray-200 p-4 my-4 border border-gray-300" >
            <h1 class="text-xl"> {{ post.reposted_posts.title }} </h1>
            <img class="my-4 mx-auto" v-if="post.reposted_posts.image" :src="post.reposted_posts.image" :alt="post.reposted_posts.title">
            <p>{{post.reposted_posts.content}}</p>
        </div>

        <div class="flex justify-between items-center">
            <div class="flex">
                <div class="flex">
                    <svg @click.prevent="toggleLike(post)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         :class="['w-6 h-6 text-red-400 cursor-pointer', post.is_liked ? 'fill-red-500' : 'fill-white']">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                    <p class="ml-2 text-slate-600">{{post.likes_count}}</p>
                </div>
                <div class="flex ml-3">
                    <svg @click.prevent="openRepost()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         :class="['w-6 h-6 text-sky-400 cursor-pointer fill-white']">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                    <p class="ml-2 text-slate-600">0</p>
                </div>
            </div>
            <p class="text-slate-500 text-right text-sm">{{post.date}}</p>
        </div>
<!--        Open Repost Form -->
        <div v-if="is_repost" class="mt-5">
            <div>
                <input v-model="title" type="text" placeholder="title" class="mb-3 w-96 rounded-3xl border p-2 border-slate-300">
                <div v-if="errors.title">
                    <p v-for="error in errors.title" class="text-red-500 text-sm mb-4 mr-4 ml-3">{{error}}</p>
                </div>
            </div>
            <div>
                <textarea v-model="content" placeholder="content" class=" mb-3 w-96 rounded-3xl border p-2 border-slate-300"></textarea>
                <div v-if="errors.content">
                    <p v-for="error in errors.content" class="text-red-500 text-sm mb-4 mr-4 ml-3">{{error}}</p>
                </div>
            </div>
            <div >
                <a href="#" @click.prevent="repost(post)" class="block p-2 w-32 bg-green-600 rounded-3xl text-center text-white
                    hover:bg-white hover:border hover:text-green-600 hover:border-green-600 box-border ml-auto">Repost</a>
            </div>
        </div>
<!--        Comments Area -->
        <div v-if="post.comments_count > 0" class="mt-4">
            <a v-if="!is_showed" href="#" @click.prevent="getComments(post)" class="underline text-gray-500">Show {{post.comments_count }} comments</a>
            <a v-if="is_showed" href="#" @click.prevent="is_showed=false" class="underline text-gray-500">Close</a>
            <div v-if="comments && is_showed">
                <div v-for="comment in comments" class="mt-4 pt-4 border-t border-gray-300">
                    <div class="flex mb-2 justify-between">
<!--                        reply to comment-->
<!--                        <p class="mr-2 text-sm">{{comment.user.name}}</p>-->
                        <router-link :to="{name:'user.show', params: {id: comment.user.id}}" class="text-sky-500 text-sm mr-2">{{comment.user.name}}</router-link>
                        <p @click="setParentId(comment)" class="text-sky-500 text-sm cursor-pointer">Reply</p>
                    </div>
                    <p> <router-link :to="{name:'user.show', params: {id: comment.user.id}}" v-if="comment.reply_for_user" class="text-sky-500">{{comment.reply_for_user}}, </router-link>{{comment.body}}</p>
                    <p class="text-right text-sm text-gray-500">{{comment.date}}</p>
                </div>
            </div>
        </div>
<!--        Leave comment area    -->
        <div class="mt-4">
            <div class="mb-3">
                <div class="flex items-center mb-2 text-sm justify-between">
                    <p v-if="comment">Reply to {{comment.user.name}}</p>
                    <p v-if="comment" @click="comment=null" class="cursor-pointer underline text-sky-500 ml-2">Cancel</p>
                </div>
                <input v-model="body" type="text" placeholder="comment" class="mb-3 w-96 rounded-3xl border p-2 border-slate-300">
            </div>
            <div v-if="errors.body">
                <p v-for="error in errors.body" class="text-red-500 text-sm mb-4 mr-4 ml-3">{{error}}</p>
            </div>
            <a href="#" @click.prevent="storeComment(post)" class="block p-2 w-32 bg-green-600 rounded-3xl text-center text-white
                    hover:bg-white hover:border hover:text-green-600 hover:border-green-600 box-border ml-auto">Comment</a>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Post",
    data(){
      return{
          is_repost: false,
          repostedId: null,
          title:'',
          content:'',
          body:'',
          errors:[],
          comments:[],
          is_showed: false,
          comment:null,

      }
    },
    props:[
      'post'
    ],
    methods:{
        toggleLike(post){
            axios.post(`/api/posts/${post.id}/toggle_like`)
                .then(res =>{
                    this.post.is_liked = res.data.is_liked
                    this.post.likes_count = res.data.likes_count
                });
        },

        openRepost(){
            if(this.$route.name === 'user.personal') return
            this.is_repost = !this.is_repost
        },

        repost(post){
            axios.post(`/api/posts/${post.id}/repost`, {title: this.title, content:this.content, reposted_id: post.id})
                .then(res =>{
                    this.title = ''
                    this.content = ''
                })
                .catch(e => {
                    this.errors = e.response.data.errors
                })
        },
        storeComment(post){
            axios.post(`/api/posts/${post.id}/comment`, {body: this.body, parent_id: this.comment ? this.comment.id: null  })
                .then(res =>{
                    this.body = ''
                    this.is_showed = true
                    this.comments.unshift(res.data.data)
                    this.parentId=null
                    post.comments_count++
                })
                .catch(e => {
                    this.errors = e.response.data.errors
                })
        },
        getComments(post){
            axios.get(`/api/posts/${post.id}/comment`)
                .then(res => {
                    this.comments=res.data.data
                    this.is_showed = true
                })
        },
        setParentId(comment){
            console.log(comment)
            this.comment = comment
        }
    }
}
</script>

<style scoped>

</style>
