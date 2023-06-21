import {createRouter, createWebHistory} from "vue-router";

const router = createRouter({
    history:createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {path:'/users/login', component: ()=> import('../views/users/Login.vue'), name:'user.login'},
        {path:'/users/index', component: ()=> import('../views/users/Index.vue'), name:'user.index'},
        {path:'/users/:id/show', component: ()=> import('../views/users/Show.vue'), name:'user.show'},
        {path:'/users/feed', component: ()=> import('../views/users/Feed.vue'), name:'user.feed'},

        {path:'/users/registration', component: ()=> import('../views/users/Registration.vue'), name:'user.registration'},
        {path:'/users/personal', component: ()=> import('../views/users/Personal.vue'), name:'user.personal'},

    ]
})

router.beforeEach((to, from, next)=>{

    axios.get('/api/user')
        .catch(e=>{
            if(e.response.status === 401){
                localStorage.key('x_xsrf_token') ? localStorage.removeItem('x_xsrf_token') : ''
            }
        })

    const token = localStorage.getItem('x_xsrf_token')

    if(!token){
        if(to.name==='user.login' || to.name==='user.registration'){
            return next()
        }else{
            return next({
                name:'user.login'
            })
        }
    }

    if(to.name==='user.login' || to.name==='user.registration' && token){
        return next({
            user:'user.personal'
        })
    }
    next()
})

export default router
