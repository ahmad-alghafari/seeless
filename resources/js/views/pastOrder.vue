<script setup>
import axios from 'axios';
import { onMounted, ref ,onBeforeMount} from 'vue';

import { useRouter } from "vue-router";
const route = useRouter();

import { useStore } from 'vuex';
const store = useStore();

import { useToast } from 'vue-toastification';
const toast = useToast();

const orders = ref();
const orderCount = ref(0);
const foods = ref();
const is_loading = ref(true);
const selectedOrder = ref({});
const selectedOrderContent = ref({});
const total = ref(0);
const service_price = ref(0);
const time = ref(5000);



onBeforeMount(() => {
  if (!store.state.isLoggedIn) {
    route.push({ name: "login" });
    store.dispatch('logout');
  }
});

onMounted(() => {
  fetchinOrders();
});

const dd = (obj) => {
  console.log("obj val : ", JSON.stringify(obj.value, null, 2));
}
const print = (variable) => {
  console.log(variable);
}

const log = (string = "", variable) => {
  console.log(string + " : " + variable);
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

const setSelectedOrder = (id) => {
  selectedOrder.value = orders.value[id];
  selectedOrderContent.value = selectedOrder.value.content;
  calck();
}

const freeSelectedOrder = () => {
  selectedOrder.value = {};
  total.value = 0;
  selectedOrderContent.value = {};
}

const calck = () => {
  selectedOrderContent.value.forEach(element => {
    total.value += foods.value[element.food_id].price * element.count;
  });
}

// fetching methods --------
const fetchinOrders = () => {
  print("get orders start");
  is_loading.value = true;
  axios.get("http://127.0.0.1:8000/api/orders/implemented"  , {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    }
  }).then(
    function (response) {
      if (response.status == 200) {
        orders.value = response.data.orders;
        foods.value = response.data.food;
        service_price.value = response.data.service_price;
        orderCount.value = Object.keys(orders.value).length;
      } 
    }
  ).catch(
    function (error) {
      if (error.response && error.response.status === 401) {
        console.log("Unauthorized - logging out.");
        store.dispatch('logout');
        router.push({ name: "login" });
      }
      toast.error('حدث خطأ ما', { timeout: time });
      console.log("catch error : " + error);
    }).then(function(){
      is_loading.value = false ;
      print("get orders end");
      
    });
}
</script>

<template>
  <div class="container-fluid py-4">
    <div class="card mb-3">
      <div class="card-body px-xxl-0 pt-4">
        <div class="row g-0">
          <div class="col-xxl-3 col-md-6 px-3 text-center border-md-end  border-xxl-bottom-0 pb-3 p-xxl-0 ps-md-0">
            <div class="icon-circle icon-circle-warning">
              <span class="fs-2 fas fa-dollar-sign text-warning"></span>
            </div>
            <h4 class="mb-1 font-sans-serif">
              <span class="text-700 mx-2"
                :data-countup="`{ endValue: ${(orderCount || 0) * (service_price || 0)} }`">{{(orderCount || 0) * (service_price || 0)}}</span>
              <span class="fw-normal text-600"></span>
            </h4>
            <p class="fs--1 fw-semi-bold mb-0">{{ $t('total_sales') }}</p>
          </div>

          <div
            class="col-xxl-3 col-md-6 px-3 text-center border-xxl-end  border-xxl-0 pb-3 pt-4 pt-md-0 pe-md-0 p-xxl-0">

            <div class="icon-circle icon-circle-info">
              <span class="fs-2 fas fa-chalkboard-teacher text-info"></span>
            </div>
            <h4 class="mb-1 font-sans-serif">
              <span class="text-700 mx-2" 
                :data-countup="`{ endValue: ${orderCount} }`">{{ orderCount }}</span>
              <span class="fw-normal text-600"></span>
            </h4>
            <p class="fs--1 fw-semi-bold mb-0">{{ $t('total_orders') }}</p>
          </div>


        </div>
      </div>
    </div>
    <!-- <div class="row">
      <div class="col-md-6 col-xxl-3">
        <div class="card h-md-100 ecommerce-card-min-width">
          <div class="card-header pb-0">
            <h6 class="mb-0 mt-2 d-flex align-items-center">{{ $t('total_sales') }}<span class="ms-1 text-400"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Calculated according to last week's sales"><span
                  class="far fa-question-circle" data-fa-transform="shrink-1"></span></span></h6>
          </div>
          <div class="card-body d-flex flex-column justify-content-end">
            <div class="row">
              <div class="col">
                <p class="font-sans-serif lh-1 mb-1 fs-4">${{ (orderCount || 0) * (service_price || 0) }}</p><span
                  class="badge badge-soft-success rounded-pill fs--2">+3.5%</span>
              </div>
              <div class="col-auto ps-0">
                <div class="echart-bar-weekly-sales h-100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xxl-3">
        <div class="card h-md-100">
          <div class="card-header pb-0">
            <h6 class="mb-0 mt-2">{{ $t('total_orders') }}</h6>
          </div>
          <div class="card-body d-flex flex-column justify-content-end">
            <div class="row justify-content-between">
              <div class="col-auto align-self-end">
                <div class="fs-4 fw-normal font-sans-serif text-700 lh-1 mb-1">{{ orderCount }}</div><span
                  class="badge rounded-pill fs--2 bg-200 text-primary"><span
                    class="fas fa-caret-up me-1"></span>13.6%</span>
              </div>
              <div class="col-auto ps-0 mt-n4">
                <div class="echart-default-total-order"
                  data-echarts='{"tooltip":{"trigger":"axis","formatter":"{b0} : {c0}"},"xAxis":{"data":["Week 4","Week 5","Week 6","Week 7"]},"series":[{"type":"line","data":[20,40,100,120],"smooth":true,"lineStyle":{"width":3}}],"grid":{"bottom":"2%","top":"2%","right":"10px","left":"10px"}}'
                  data-echart-responsive="true"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->
    <br>
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header border-bottom">
            <div class="row flex-between-end">
              <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">{{ $t('orders_history') }}</h5>
              </div>
            </div>
          </div>
          <div class="card-body pt-0" v-if="orderCount !== 0">
            <div class="tab-content">
              <div class="tab-pane preview-tab-pane active" role="tabpanel"
                aria-labelledby="tab-dom-c7756704-0691-4570-a9db-6730c60c4b23"
                id="dom-c7756704-0691-4570-a9db-6730c60c4b23">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">{{ $t('name') }}</th>
                      <th scope="col">{{ $t('table_number') }}</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody v-if="!is_loading">
                    <tr v-for="order in orders" :key="order.index">
                      <td>{{ since(order.created_at) }}</td>
                      <td>{{ order.table_number }}</td>
                      <td><button type="button" class="btn btn-success font-weight-bold" data-bs-toggle="modal"
                          data-bs-target="#content" @click="setSelectedOrder(order.id)">
                          {{ $t('preview') }}
                        </button></td>
                    </tr>
                  </tbody>
                </table>
              </div>
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
          <h1 class="modal-title fs-5" id="contentLabel">{{ $t('order_content') }} </h1>
        </div>
        <div class="modal-body">
          <table class="table table-striped">
            <thead>
              <tr>
                <th class="text-center">{{ $t('order') }}</th>
                <th class="text-center">{{ $t('quantity') }}</th>
                <th class="text-center">{{ $t('price') }}</th>
                <th class="text-center">{{ $t('total') }}</th>
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
          <p class="text-start rtl mb-0 mt-3">{{ $t('invoice_value') }} : {{ total }}</p>
          <p class="text-start rtl mb-0 mt-3">{{ $t('total_value') }} : {{ total }}</p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close"
            @click="freeSelectedOrder">{{ $t('close') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>