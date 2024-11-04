<script setup>
import { onMounted, ref, computed, onBeforeMount } from 'vue';
import axios, { Axios } from 'axios';

import { useRouter } from "vue-router";
const router = useRouter();

import { useToast } from 'vue-toastification';
const toast = useToast();

import { useStore } from 'vuex';
const store = useStore();

const foods = ref({});
const categories = ref({});
const isLoading = ref();
const foodFucas = ref({});
const file = ref(null);
const time = ref(5000);

const foodCount = computed(() => Object.keys(foods.value).length);


const availablefoodCount = computed(() => {
  return Object.values(foods.value).filter(food => food.availability === "availble").length;
});

onBeforeMount(() => {
  if (!store.state.isLoggedIn) {
    router.push({ name: "login" });
    store.dispatch('logout');
  }
});

onMounted(async () => {
  fetching();
});

const dd = (str = "", variable) => {
  console.log(str + " : " + variable);
}
const print = (variable) => {
  console.log(variable);
}


const uploadfileshange = (event) => {
  file.value = event.target.files[0];
  console.log("file : " + file.value);
}

const oppisit = (id) => {
  if (foods.value[id].availability == "availble") {
    return "not_available";
  } else {
    return "availble";
  }
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

// fetching methods ------
const fetching = async () => {
  isLoading.value = true;

  print("fetching : start");

  axios.get('http://127.0.0.1:8000/api/food',
    {
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + store.state.token
      }
    }
  ).then(
    function (response) {
      if (response.status == 200) {
        foods.value = response.data.food;
        categories.value = response.data.categories;
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
    }
  ).finally(function () {
    isLoading.value = false;
    print("fetching : end");
  });
}

const submit = async () => {
  print("add food start");
  const formData = new FormData();
  formData.append('name', foodFucas.value.name);
  formData.append('category_id', foodFucas.value.category_id);
  formData.append('price', foodFucas.value.price);
  formData.append('description', foodFucas.value.description);

  // Append the file only if it's selected
  if (file.value) {
    formData.append('file', file.value);
  }

  axios.post("http://127.0.0.1:8000/api/food/add", formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + store.state.token
      },
    }).then(function (response) {
      if (response.status == 200) {
        foodFucas.value = response.data.food;
        foods.value[foodFucas.value.id] = foodFucas.value;
        toast.success('تم إضافة الطعام بنجاح', { timeout: time });
        foodFucas.value = {};
      }
    }).catch(function (error) {
      if (error.response && error.response.status === 401) {
        console.log("Unauthorized - logging out.");
        store.dispatch('logout');
        router.push({ name: "login" });
      }
      console.log("catch error : " + error);
      toast.error('حدث خطأ ما', { timeout: time });
    }).then(function () {
      print("add food end");
    });

}



const postChangStatus = async (id) => {
  print("change status of food start");
  let temp = oppisit(id);
  axios.post("http://127.0.0.1:8000/api/food/status", {
    status: oppisit(id),
    id: id
  }, {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    },
  }).then(function (response) {
    if (response.data.status == 200) {
      foods.value[id].availability = temp;
      foods.value[id].updated_at = response.data.updated_at;
      toast.success('تم تغيير حالة الطعام بنجاح', { timeout: time });
    }
  }).catch(
    function (error) {
      if (error.response && error.response.status === 401) {
        console.log("Unauthorized - logging out.");
        store.dispatch('logout');
        router.push({ name: "login" });
      }
      toast.error('حدث خطأ ما', { timeout: time });
      console.log("catch error : " + error);
    }
  ).finally(function () {
    print("change status of food end");
  });
}

const deleteFood = async (id) => {
  print("delete food start");
  axios.delete("http://127.0.0.1:8000/api/food/delete", {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    },
    id: id
  }).then(
    function (response) {
      if (response.status == 200) {
        delete foods.value[id];
        toast.success('تم حذف الطعام بنجاح', { timeout: time });
      }
    })
    .catch(
      function (error) {
        if (error.response && error.response.status === 401) {
          console.log("Unauthorized - logging out.");
          store.dispatch('logout');
          router.push({ name: "login" });
        }
        toast.error('حدث خطأ ما', { timeout: time });
        console.log("catch error : " + error);
      }
    ).then(function () {
      print("delete food end");
    });
}
</script>
<template>
  
  
  <div class="card mb-3">
    <div class="card-header border-bottom">
      <div class="row flex-between-end">
        <div class="col-auto align-self-center d-flex flex-column flex-sm-row">


          <h5 class="mb-0" data-anchor="data-anchor">{{ $t('dish_list') }} <span
            class="badge rounded-pill ms-2 bg-200 text-primary">{{ foodCount }}</span></h5>
          
            
        </div>
        <div class="col-auto ms-auto">
          <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
            <button class="btn btn-sm active" data-bs-toggle="pill"
              data-bs-target="#dom-5d6d5a1a-8f17-4831-9c08-cc44c955d933" type="button" role="tab"
              aria-controls="dom-5d6d5a1a-8f17-4831-9c08-cc44c955d933" aria-selected="true"
              id="tab-dom-5d6d5a1a-8f17-4831-9c08-cc44c955d933">{{ $t('show') }}</button>
            <button class="btn btn-sm" data-bs-toggle="pill" data-bs-target="#dom-2009e079-9665-4774-a835-f9e4414e0cd4"
              type="button" role="tab" aria-controls="dom-2009e079-9665-4774-a835-f9e4414e0cd4" aria-selected="false"
              id="tab-dom-2009e079-9665-4774-a835-f9e4414e0cd4">{{ $t('explane') }}</button>
          </div>
        </div>

      </div>
    </div>
    <div class="card-body pt-0">
      <div class="tab-content">
        <div class="tab-pane preview-tab-pane active" role="tabpanel"
          aria-labelledby="tab-dom-5d6d5a1a-8f17-4831-9c08-cc44c955d933" id="dom-5d6d5a1a-8f17-4831-9c08-cc44c955d933">
          <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped overflow-hidden">
              <thead>
                <tr>
                  <th scope="col">{{ $t('name') }}</th>
                  <th scope="col">{{ $t('category') }}</th>
                  <th scope="col">{{ $t('availability') }} <span
                    class="badge rounded-pill ms-2 bg-200 text-success">{{ availablefoodCount }}</span></th>
                  <th scope="col">{{ $t('price') }}</th>
                  <th scope="col"> {{ $t('last_modified') }} </th>
                </tr>
              </thead>
              <tbody v-show="isLoading == false">
                <tr class="align-middle" v-for="food in foods" :key="food.id">

                  <td class="text-nowrap">
                    <router-link :to="{ name: 'details', params: { id: food.id } }" class="nav-link">

                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl">
                          <img class="rounded-circle" :src="`/${food.path}`" alt="صورة الطبق" />
                        </div>
                        <div class="ms-2">{{ food.name }}</div>
                      </div>
                    </router-link>
                  </td>
                  <td class="text-nowrap">{{ categories[food.category_id] }}</td>
                  <td>
                    <button v-if="food.availability == 'availble'" @click="postChangStatus(food.id)" type="button"
                      class="inherit-btn-spis">
                      <span class="badge badge rounded-pill d-block p-2 badge-soft-success">
                        {{ $t('available') }}
                        <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                      </span>
                    </button>
                    <button v-else @click="postChangStatus(food.id)" type="button" class="inherit-btn-spis">
                      <span class="badge badge rounded-pill d-block p-2 badge-soft-danger">
                        {{ $t('unavailable') }}
                        <span class="ms-1 fas fa-ban" data-fa-transform="shrink-2"></span>
                      </span>
                    </button>
                  </td>
                  <td class="text-start">
                    <router-link :to="{ name: 'details', params: { id: food.id } }" class="nav-link">

                      ${{ food.price }}
                    </router-link>

                  </td>
                  <td class="text-nowrap">
                    <router-link :to="{ name: 'details', params: { id: food.id } }" class="nav-link">

                      {{ food.updated_at.split('T')[0] }} - {{ since(food.updated_at) }}
                    </router-link>

                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
      </div>
    </div>
  </div>


  <!-- Modal -->

</template>
<style>
.inherit-btn-spis {
  background-color: inherit;
  border: none;
}
</style>