import {createRouter , createWebHistory} from "vue-router" ;

import main from "../views/main.vue" ;
import food from "../views/food.vue" ;
import orders from "../views/orders.vue" ;
import pastOrder from "../views/pastOrder.vue";
import profile from "../views/profile.vue";
import categories from "../views/categories.vue";
import details from "../views/food_details.vue";
import notfound from "../views/404.vue";
import addFood from "../views/add_food.vue";
import login from "../views/loging.vue";
import store from '../store';
import QRcode from '../views/QRcode.vue';
const path = "/admin";
const routes = [
    {
        path: path + '/dashboard',
        name:'main',
        component: main ,
        meta: { requiresAuth: true }
    },
    {
        path:path +  '/dashboard/food',
        name:'food',
        component: food,
        meta: { requiresAuth: true }

    },
    {
        path:path +  '/dashboard/food/add',
        name:'addFood',
        component: addFood,
        meta: { requiresAuth: true }

    },
    {
        path:path + '/dashboard/orders',
        name:'orders',
        component: orders,
        meta: { requiresAuth: true }

    },
    {
        path : path + '/dashboard/pastOrders' ,
        name : 'pastOrders' ,
        component : pastOrder,
        meta: { requiresAuth: true }

    }
    ,{
        path :path +  '/dashboard/profile' ,
        name : 'profile' ,
        component : profile,
        meta: { requiresAuth: true }

    }
    ,{
        path :path +  '/dashboard/food/categories' ,
        name : 'categories' ,
        component : categories,
        meta: { requiresAuth: true }

    }
    ,{
        path:path +  '/dashboard/food/details/:id',
        name: 'details',
        component: details,
        meta: { requiresAuth: true }

    }
    ,{
        path:path +  '/dashboard/QRcode',
        name: 'QRcode',
        component: QRcode,
        meta: { requiresAuth: true }

    }
    ,{
        path:   '/:catchAll(.*)',
        name: 'notfound',
        component: notfound,
        meta: { requiresAuth: false }

    },
    {
        path: path +  '/login',
        component: login, 
        name : "login",
        meta: { requiresAuth: false }
    }
]

const router = createRouter({
    history:createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    console.log("log is : " + store.state.isLoggedIn);
    if (to.meta.requiresAuth && store.state.isLoggedIn == false) {
      next('/admin/login'); // Redirect to login if not authenticated
    } else if (to.path === '/login' && store.state.isLoggedIn) {
      next('/'); // Prevent accessing login page if already logged in
    } else {
      next(); // Proceed normally
    }
  });

export default router
