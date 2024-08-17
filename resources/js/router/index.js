import {createRouter , createWebHistory} from "vue-router" ;

import dashBoard from "../views/main.vue" ;
import food from "../views/food.vue" ;
import orders from "../views/orders.vue" ;
import pastOrder from "../views/pastOrder.vue";



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
    },
    {
        path : '/dashboard/pastOrders/:id' ,
        name : 'pastOrders' ,
        component : pastOrder,
    }
]

const router = createRouter({
    history:createWebHistory(),
    routes,
})

export default router
