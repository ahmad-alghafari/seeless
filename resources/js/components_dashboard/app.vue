<script setup>
import settings from "../components_dashboard/settings.vue";
import foter from "./footer.vue";
import navbar from "../components_dashboard/navbar.vue";
import {ref , onMounted} from "vue" ; 
const resturant_id = ref();
const service_type = ref();
const order = ref();
const name = ref();
const saved_orders = ref();
onMounted(()=>{
  if(window.resturant_id){
    resturant_id.value = window.resturant_id;
  }service_type
  if(window.service_type){
    service_type.value = window.service_type;
  }
  if(window.order){
    order.value = window.order;
  }
  if(window.name){
    name.value = window.name;
  }
  if(window.saved_orders){
    saved_orders.value = window.saved_orders;
  }
});
</script>

<template>
  
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-end me-3 rotate-caret  bg-gradient-dark" id="sidenav-main">
      <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute start-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 text-center " href="#" target="_blank" >
          <!-- <img src="layouts/dashboard/assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo"> -->
          <span class="me-1 font-weight-bold text-white text-center">{{name}}</span>
        </a>
      </div>
      
      <hr class="horizontal light mt-0 mb-2">
      <div class="collapse navbar-collapse px-0 w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link
            
            to="/dashboard" class="nav-link">
              <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i class="material-icons-round opacity-10">dashboard</i>
              </div>
              <span class="nav-link-text me-1">لوحة القيادة</span>
            </router-link>
          </li>
          <li class="nav-item">
                <router-link 
                v-if="resturant_id"
                :to="{name : 'food' , params : { id : resturant_id }}" class="nav-link">
                  <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons-round opacity-10">table_view</i>
                  </div>
                  <span class="nav-link-text me-1">الأطعمة و الأصناف</span>
               </router-link>
          </li>
          <li class="nav-item" v-if="order == 'true'">
                <router-link 
                
                :to="{name : 'orders' , params : {id : resturant_id}}" class="nav-link">
                  <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons-round opacity-10">table_view</i>
                  </div>
                  <span class="nav-link-text me-1">طلبات قيد الإنتظار</span>
              </router-link>
          </li>

          <li class="nav-item" v-if="order == 'true' && saved_orders == 'true'">
            <router-link 
            class="nav-link"
            :to="{name : 'pastOrders' , params : {id : resturant_id}}">
              <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i class="material-icons-round opacity-10">table_view</i>
              </div>
              <span class="nav-link-text me-1">سجلّ الطلبات</span>
            </router-link>
          </li>
          
          <li class="nav-item">
            <a class="nav-link " href="../pages/billing.html">
              <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i class="material-icons-round opacity-10">receipt_long</i>
              </div>
              <span class="nav-link-text me-1">الفواتير</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/notifications.html">
              <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">notifications</i>
              </div>
              <span class="nav-link-text me-1">إشعارات</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/profile.html">
              <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i class="material-icons-round opacity-10">person</i>
              </div>
              <span class="nav-link-text me-1">حساب تعريفي</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/sign-in.html">
              <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i class="material-icons-round opacity-10">login</i>
              </div>
              <span class="nav-link-text me-1">تسجيل الدخول</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/sign-up.html">
              <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i class="material-icons-round opacity-10">assignment</i>
              </div>
              <span class="nav-link-text me-1">اشتراك</span>
            </a>
          </li>
        </ul>
      </div>
    </aside>
    
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg overflow-x-hidden">
    <navbar/>
    <router-view/>
    </main>

    <!-- <settings/> -->
    <!-- <foter/> -->
</template>