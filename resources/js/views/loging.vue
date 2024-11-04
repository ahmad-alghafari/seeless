<script setup>
import axios from 'axios';
import { ref } from 'vue';

import store from "../store";


import { useToast } from 'vue-toastification';
const toast = useToast();

import { useRouter } from "vue-router";
const router = useRouter();

const password = ref();
const email = ref();
const signin = () => {
  axios.post("http://127.0.0.1:8000/api/login" , {
    email : email.value , 
    password : password.value ,
  }).then(function(response){
    if(response.data.status && response.data.status == "200"){

      const token = response.data.token;
      console.log("resturant id from axios :  " + token);

      const resturant_id = response.data.resturant_id;
      console.log("resturant id from axios :  " + resturant_id);

      const name = response.data.name;
      const saved_orders = response.data.saved_orders;
      const order = response.data.order;
      const service_type = response.data.service_type;
      const id = response.data.id;

      store.dispatch('login',{
         token ,
          resturant_id,
          name ,
          saved_orders ,
          order , 
          service_type ,
          id 
        });
      console.log("Login successful, token stored:", token);
      router.push({name : "main"});
    }else{
      toast.error('login feild!', { timeout: 3000 });
    }
  }).catch(function(error){
    console.log("Login error", error);
  });
}
</script>
<template>
    <div class="container-fluid">
        <div class="row min-vh-100 flex-center g-0">
          <div class="col-lg-8 col-xxl-5 py-3 position-relative">
            <img class="bg-auth-circle-shape" :src="`/assets/img/icons/spot-illustrations/bg-shape.png`" alt="" width="250">
            <img class="bg-auth-circle-shape-2" :src="`/assets/img/icons/spot-illustrations/shape-1.png`" alt="" width="150">
            <div class="card overflow-hidden z-index-1">
              <div class="card-body p-0">
                <div class="row g-0 h-100">
                  <div class="col-md-5 text-center bg-card-gradient">
                    <div class="position-relative p-4 pt-md-5 pb-md-7 light">
                      <div class="bg-holder bg-auth-card-shape" style="background-image:url('/assets/img/icons/spot-illustrations/half-circle.png');">
                      </div>
                      <div class="z-index-1 position-relative"><a class="link-light mb-4 font-sans-serif fs-4 d-inline-block fw-bolder" href="../../../index.html">{{ $t('seless') }}</a>
                        <p class="opacity-75 text-white">{{ $t('login_message') }}</p>
                      </div>
                    </div>
                    <div class="mt-3 mb-4 mt-md-4 mb-md-5 light">
                      <p class="text-white">Don't have an account?<br><a class="text-decoration-underline link-light" href="../../../pages/authentication/card/register.html">Get started!</a></p>
                      <p class="mb-0 mt-4 mt-md-5 fs--1 fw-semi-bold text-white opacity-75">Read our <a class="text-decoration-underline text-white" href="#!">terms</a> and <a class="text-decoration-underline text-white" href="#!">conditions </a></p>
                    </div>
                  </div>
                  <div class="col-md-7 d-flex flex-center">
                    <div class="p-4 p-md-5 flex-grow-1">
                      <div class="row flex-between-center">
                        <div class="col-auto">
                          <h3>{{ $t('account_login') }}</h3>
                        </div>
                      </div>
                      <form >
                        <div class="mb-3">
                          <label class="form-label" for="card-email">{{ $t('email_address') }}</label>
                          <input  v-model="email" class="form-control " id="card-email" type="email" name="email" value="" required autocomplete="email" autofocus/>
                            <!-- <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span> -->
                        </div>

                        <div class="mb-3">
                          <div class="d-flex justify-content-between">
                            <label class="form-label" for="card-password">{{ $t('password') }}</label>
                          </div>
                          <input v-model="password" class="form-control " id="card-password" type="password" name="password" required autocomplete="current-password"/>
                        </div>
                        <div class="row flex-between-center">
                          <div class="col-auto">
                            <div class="form-check mb-0">
                              <input class="form-check-input" type="checkbox" id="card-checkbox" checked="checked" />
                              <label class="form-check-label mb-0" for="card-checkbox">{{ $t('remember_me') }}</label>
                            </div>
                          </div>
                          <div class="col-auto"><a class="fs--1" href="../../../pages/authentication/card/forgot-password.html">{{ $t('forget_password') }}</a></div>
                        </div>
                        <div class="mb-3">
                          <button class="btn btn-primary d-block w-100 mt-3" type="button" name="submit" @click="signin">{{ $t('login') }}</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</template>