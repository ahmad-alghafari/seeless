<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRoute } from "vue-router";

const route = useRoute();

const orders = ref();
const orderCount = ref(0);
const foods = ref();
const id = ref();
const is_loading = ref(true);
const selectedOrder = ref({});
const selectedOrderContent = ref({});
const total = ref(0);
const service_price = ref();



onMounted( () => {
    is_loading.value = true ;
    if(window.resturant_id){
      id.value = window.resturant_id;
    }
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
        return `منذ ${interval} سنة`;
    }
    
    interval = Math.floor(diffInSeconds / 2592000);
    if (interval > 1) {
        return `منذ ${interval} شهر`;
    }
    
    interval = Math.floor(diffInSeconds / 86400);
    if (interval > 1) {
        return `منذ ${interval} يوم`;
    }
    
    interval = Math.floor(diffInSeconds / 3600);
    if (interval > 1) {
        return `منذ ${interval} ساعة`;
    }
    
    interval = Math.floor(diffInSeconds / 60);
    if (interval > 1) {
        return `منذ ${interval} دقيقة`;
    }
    
    return `منذ ${diffInSeconds} ثانية`;
};

const setSelectedOrder = (id) =>{
  selectedOrder.value = orders.value[id];
  selectedOrderContent.value = selectedOrder.value.content;
  calck();
}

const freeSelectedOrder = () =>{
  selectedOrder.value = {};
  total.value = 0;
  selectedOrderContent.value = {};
}

const calck = () =>{
  selectedOrderContent.value.forEach(element => {
    total.value += foods.value[element.food_id].price * element.count;
  });
}
// fetching methods --------
const fetchinOrders = () => {
    print("get orders start");
    axios.get("http://127.0.0.1:8000/api/orders/implemented/"+id.value).then(
        function(response){
            if(response.data.status == '200'){
                orders.value = response.data.orders ;
                is_loading.value = false ;
                foods.value = response.data.food;
                service_price.value = response.data.service_price ;
                orderCount.value = Object.keys(orders.value).length;
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
</script>

<template>
    <div class="container-fluid py-4">
        <div class="row">
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">leaderboard</i>
              </div>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">سعر الخدمة لكل طلب</p>
                <h4 class="mb-0">{{ service_price }}</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">leaderboard</i>
              </div>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">إجمالي التكلفة</p>
                <h4 class="mb-0" v-if="orders">{{ orderCount * service_price }}</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3"> سجل الطلبات </h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2" v-if="orders">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead class="text-center">
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">منذ</th>
                      <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">طاولة رقم</th>
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
              <tr v-if="!is_loading" v-for="order in selectedOrderContent">
                <td class="text-center">{{ foods[order.food_id]?.name || 'Unknown Food' }}</td>
                <td class="text-center">{{ order?.count || 0 }}</td>
                <td class="text-center">{{ foods[order.food_id]?.price || 'N/A' }}</td>
                <td class="text-center">{{ (order?.count || 0) * (foods[order.food_id]?.price || 0) }}</td>
              </tr>
            </tbody>
          </table>
          <p class="text-start rtl mb-0 mt-3">قيمة الفاتورة : {{ total }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal" aria-label="Close" @click="freeSelectedOrder" >إغلاق</button>
        </div>
      </div>
    </div>
  </div>

</template>