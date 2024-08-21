import {createRouter , createWebHistory} from "vue-router" ;

import dashBoard from "../views/main.vue" ;
import food from "../views/food.vue" ;
import orders from "../views/orders.vue" ;
import pastOrder from "../views/pastOrder.vue";
import profile from "../views/profile.vue";



const routes = [
    {
        path:'/dashboard',
        name:'dashboard',
        component: dashBoard
    },
    {
        path:'/dashboard/food',
        name:'food',
        component: food,
    },
    {
        path:'/dashboard/orders',
        name:'orders',
        component: orders,
    },
    {
        path : '/dashboard/pastOrders' ,
        name : 'pastOrders' ,
        component : pastOrder,
    },{
        path : '/dashboard/profile' ,
        name : 'profile' ,
        component : profile,
    }
]

const router = createRouter({
    history:createWebHistory(),
    routes,
})

export default router
