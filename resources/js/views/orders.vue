<script setup>
import axios from 'axios';
import { onMounted, ref, onBeforeMount } from 'vue';
import Pusher from 'pusher-js';

import { useRouter } from "vue-router";
const router = useRouter();

import { useToast } from 'vue-toastification';
const toast = useToast();

import { useStore } from 'vuex';
const store = useStore();

const orders = ref();
const foods = ref();
const id = ref();
const owner_id = ref();
const is_loading = ref(true);
const selectedOrder = ref({});
const selectedOrderContent = ref({});
const total = ref(0);
const time = ref(5000);


onBeforeMount(() => {
  if (!store.state.isLoggedIn) {
    router.push({ name: "login" });
    store.dispatch('logout');
  }
});

onMounted(() => {
  fetchinOrders();
  const pusher = new Pusher('7d71734b3bda20e881ea', {
    cluster: 'ap2'
  });
  const channel = pusher.subscribe('orders-resturant-' + owner_id.value);
  channel.bind('OrderNotification', function (data) {
    orders.value = { [data.order.id]: data.order, ...orders.value };
    toast.success("تم استلام طلب جديد", {
      timeout: 3000,
    });
  });
});

Pusher.logToConsole = false;


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
  is_loading.value = true;
  print("get orders start");
  axios.get("http://127.0.0.1:8000/api/orders", {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    }
  }
  ).then(
    function (response) {
      if (response.status == 200) {
        orders.value = response.data.orders;
        foods.value = response.data.food;
      }
    }
  ).catch(
    function (error) {
      if (error.response && error.response.status === 401) {
        console.log("Unauthorized - logging out.");
        store.dispatch('logout');
        router.push({ name: "login" });
      }
      console.log("catch error : " + error);
    }
  ).finally(function () {
    is_loading.value = false;
    print("get orders end");
  });
}

const implement = async (order) => {
  print("implement start");
  axios.post("http://127.0.0.1:8000/api/orders/implement", {
    order_id: order,
  } ,{
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    },
    
  }).then(function (response) {
    if (response.status == 200) {
      delete orders.value[order];
      toast.success('تمت المعالجة بنجاح', { timeout: time });
    } 
  }).catch(function (error) {
    if (error.response && error.response.status === 401) {
        console.log("Unauthorized - logging out.");
        store.dispatch('logout');
        router.push({ name: "login" });
      }
      toast.error('حدث خطأ ما', { timeout: time });
      console.log("catch error : " + error);
    }).then(function(){
    print("implement end");
    });
}
</script>
<template>
  <div class="container-fluid py-4">
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
          <div class="card-body pt-0" v-if="orders">
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
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody v-if="!is_loading">
                    <tr v-for="order in orders" :key="order.index">
                      <td>{{ since(order.created_at) }}</td>
                      <td>{{ order.table_number }}</td>
                      <td>
                        <button type="button" class="btn btn-success font-weight-bold" data-bs-toggle="modal"
                          data-bs-target="#content" @click="setSelectedOrder(order.id)">
                          {{ $t('preview') }}
                        </button>
                      </td>
                      <td>
                        <button type="button" class="btn btn-info font-weight-bold" @click="implement(order.id)">
                          {{ $t('processing') }}
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
  </div>
  <div class="modal fade" id="content" tabindex="-1" aria-labelledby="contentLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="contentLabel">{{ $t('order_content') }}</h1>
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
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal" aria-label="Close"
            @click="freeSelectedOrder">{{ $t('close') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>