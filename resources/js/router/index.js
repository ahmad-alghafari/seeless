import {createRouter , createWebHistory} from "vue-router"

const routes = [
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
