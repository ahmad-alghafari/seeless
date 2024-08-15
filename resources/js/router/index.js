import {createRouter , createWebHistory} from "vue-router" ;

import dashBoard from "../views/main.vue" ;
import food from "../views/food.vue" ;
import orders from "../views/orders.vue" ;



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
    },
    {
        path:'/dashboard/orders/:id',
        name:'orders',
        component: orders,
    }
]

const router = createRouter({
    history:createWebHistory(),
    routes,
})

export default router
