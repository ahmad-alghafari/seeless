<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRoute } from "vue-router";

const route = useRoute();

const orders = ref();
const foods = ref();
const id = ref();
const is_loading = ref(true);
const selectedOrder = ref({});

onMounted( () => {
    is_loading.value = true ;
    id.value = route.params.id;
    fetchinOrders();
});

const dd = (obj) =>{
  console.log("obj val : ", JSON.stringify(obj.value, null, 2));
} 
const print = (variable) =>{
  console.log(variable);
} 

const log = (string = "" ,variable) => {
  console.log(string +" : "+ variable);
} 



const since = (date) => {
    const currentDate = new Date();
    const pastDate = new Date(date);
    const diffInSeconds = Math.floor((currentDate - pastDate) / 1000);

    let interval = Math.floor(diffInSeconds / 31536000);
    if (interval > 1) {
        return `since ${interval} years`;
    }
    
    interval = Math.floor(diffInSeconds / 2592000);
    if (interval > 1) {
        return `since ${interval} months`;
    }
    
    interval = Math.floor(diffInSeconds / 86400);
    if (interval > 1) {
        return `since ${interval} days`;
    }
    
    interval = Math.floor(diffInSeconds / 3600);
    if (interval > 1) {
        return `since ${interval} hours`;
    }
    
    interval = Math.floor(diffInSeconds / 60);
    if (interval > 1) {
        return `since ${interval} minutes`;
    }
    
    return `since ${diffInSeconds} seconds`;
};

const setSelectedOrder = (id) =>{
  selectedOrder.value = orders.value[id].content[0];
}

const freeSelectedOrder = () =>{
  selectedOrder.value = {};
}
// fetching methods --------

const fetchinOrders = () => {
    print("get orders start");
    axios.get("http://127.0.0.1:8000/api/orders/"+id.value).then(
        function(response){
            if(response.data.status == '200'){
                orders.value = response.data.orders ;
                is_loading.value = false ;
                foods.value = response.data.food;
            }else{
                log("server error" , response.data.message)
            }
            print("get orders end");
        }
    ).catch(
        function(error){
            log("error" , error);
            print("get orders end");
        }
    );
}

const implement = async (order) =>{
  print("implement start");
  axios.post("http://127.0.0.1:8000/api/orders/implement" , {
    order_id : order  ,
    id : id.value
  }).then(function(response){
    if(response.data.status == '200'){
      delete orders.value[order];
    }else{
      log("server error",response.data.message);
    }
    print("implement end");
  }).catch(function(error){
    log("local error" , error);
    print("implement end");
  });
}
</script>
<template>
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">الطلبات الحالية</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2" v-if="orders">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead class="text-center">
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">منذ</th>
                      <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">طاولة</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody v-if="!is_loading"> 
                    <tr v-for="order in orders" :key="order.index" >
                      <td>
                        <p class="text-center text-xs text-secondary ">{{ since(order.created_at) }}</p>
                      </td>
                      <td>
                        <p class="text-center text-xs text-secondary ">{{ order.table_number }}</p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <button type="button" class="btn bg-gradient-secondary" @click="implement(order.id)">
                          تمت المعالجة
                        </button>
                      </td>
                      <td class="align-middle">
                        <button type="button" class="btn btn-success font-weight-bold" data-bs-toggle="modal" data-bs-target="#content" @click="setSelectedOrder(order.id)">
                          معاينة
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <div class="modal fade" id="content" tabindex="-1" aria-labelledby="contentLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="contentLabel">محتويات الطلب</h1>
        </div>
        <div class="modal-body">
          <table  class="table table-striped">
            <thead>
              <tr>
                <th class="text-center">الطلب</th>
                <th class="text-center">العدد</th>
                <th class="text-center">السعر</th>
                <th class="text-center">المجموع</th>
              </tr>
            </thead>
            <tbody>
              <!-- <tr v-if="selectedOrder">
                <td class="text-center">{{ foods[selectedOrder.food_id].name }}</td>
                <td class="text-center">{{ selectedOrder.count }}</td>
                <td class="text-center">{{ foods[selectedOrder.food_id].price }}</td>
                <td class="text-center">{{ selectedOrder.count * foods[selectedOrder.food_id].price }}</td>
               </tr>  -->
            </tbody>
          </table>
          <p>قيمة الفاتورة : </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal" aria-label="Close" @click="freeSelectedOrder" >إغلاق</button>
        </div>
      </div>
    </div>
  </div>
</template>