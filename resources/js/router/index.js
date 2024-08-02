import {createRouter , createWebHistory} from "vue-router"

import home from "../components/homePage.vue";
import about from "../components/about.vue";
import contactAndSupport from "../components/contactAndSupport.vue";
import notFound from "../components/notFound.vue";



const routes = [
    {
        path: '/',
        component: home 
    },
    {
        path:'/about',
        component:about
    },
    {
        path:'/contact',
        component:contactAndSupport
    },
    {
        path:'/:pathMatch(.*)*',
        component: notFound
    },
]

const router = createRouter({
    history:createWebHistory(),
    routes,
})

export default router
