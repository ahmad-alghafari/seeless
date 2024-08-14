import {createRouter , createWebHistory} from "vue-router" ;

import dashBoard from "../views/main.vue" ;
import food from "../views/food.vue" ;


const routes = [
    {
        path:'/dashboard',
        name:'dashboard',
        component: dashBoard
    },
    {
        path:'/dashboard/food/:id',
        name:'food',
        component: food,
    }
]

const router = createRouter({
    history:createWebHistory(),
    routes,
})

export default router
