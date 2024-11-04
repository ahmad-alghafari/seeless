<script setup>
import { onMounted, ref, computed, onBeforeMount } from 'vue';
import axios, { Axios } from 'axios';

import { useToast } from 'vue-toastification';
const toast = useToast();

import { useStore } from 'vuex';
const store = useStore();

import { useRouter } from "vue-router";
const router = useRouter();


const categories = ref({});
const isLoading = ref();
const categoryFucas = ref({});
const time = ref(5000);

const categoriesCount = computed(() => Object.keys(categories.value).length);

onBeforeMount(() => {
  if (!store.state.isLoggedIn) {
    router.push({ name: "login" });
    store.dispatch('logout');
  }
});

onMounted(async () => {
  fetching();
});

const print = (variable) => {
  console.log(variable);
}

const getCategory = (id) => {
  categoryFucas.value.id = id;
  categoryFucas.value.name = categories.value[id].name;
}

const freeCategoryFuces = () => {
  categoryFucas.value = {};
}



// fetching methods ------
const fetching = async () => {
  print("fetching : start");
  isLoading.value = true;

  axios.get('http://127.0.0.1:8000/api/category', {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    },
  }).then(
    function (response) {
      if (response.status == 200) {
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
      console.log("catch error : " + error);
    }
  ).then(function () {
    isLoading.value = false;
    print("fetching : end");
  });
}

const submitchangecategory = async () => {
  print("change name of category start");
  axios.post("http://127.0.0.1:8000/api/category/edit", {
    name: categoryFucas.value.name,
    id: categoryFucas.value.id
  } , {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    }
  }).then(
    function (response) {
      if (response.status == 200) {
        categories.value[categoryFucas.value.id] = {
          name: categoryFucas.value.name,
          updated_at: getCurrentISODate()
        };
        toast.success('تم تعديل الصنف بنجاح', { timeout: 1000 });
        freeCategoryFuces();
      }
    }
  ).catch(
    function (error) {
      if (error.response && error.response.status === 401) {
        console.log("Unauthorized - logging out.");
        store.dispatch('logout');
        router.push({ name: "login" });
      }
      toast.error('حدث خطأ ما', { timeout: 1000 });
      console.log("catch error : " + error);
    }
  ).then(function () {
    print("change name of category end");
  });
}

const deletecategory = async () => {
  print("delete category start");
  axios.delete("http://127.0.0.1:8000/api/category/delete/" + categoryFucas.value.id, {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    },
    // id :  categoryFucas.value.id
  }
  ).then(function (response) {
    if (response.status == 200) {
      delete categories.value[categoryFucas.value.id];
      toast.success('تم حذف الصنف بنجاح', { timeout: 1000 });
      freeCategoryFuces();
    }
  }).catch(
    function (error) {
      if (error.response && error.response.status === 401) {
        console.log("Unauthorized - logging out.");
        store.dispatch('logout');
        router.push({ name: "login" });
      }
      toast.error('حدث خطأ ما', { timeout: 1000 });
      console.log("catch error : " + error);
    }
  ).then(function () {
    print("delete category end");
  });
}

const addcategory = async () => {
  print("add category start");
  axios.post("http://127.0.0.1:8000/api/category/create", {
    name: categoryFucas.value.name,
  }, {
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + store.state.token
    }
  }
  ).then(
    function (response) {
      if (response.status == 200) {
        categories.value[response.data.id] = {
          name: categoryFucas.value.name,
          updated_at: getCurrentISODate()
        };
        toast.success('تم إضافة الصنف بنجاح', { timeout: time });
        freeCategoryFuces();
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
  ).then(function () {
    print("add category end");
  });
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

function getCurrentISODate() {
    const date = new Date();

    // Get individual components
    const year = date.getUTCFullYear();
    const month = String(date.getUTCMonth() + 1).padStart(2, '0'); // Months are zero-indexed
    const day = String(date.getUTCDate()).padStart(2, '0');
    const hours = String(date.getUTCHours()).padStart(2, '0');
    const minutes = String(date.getUTCMinutes()).padStart(2, '0');
    const seconds = String(date.getUTCSeconds()).padStart(2, '0');
    const milliseconds = String(date.getUTCMilliseconds()).padStart(3, '0');

    // Return the ISO 8601 formatted string
    return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}.${milliseconds}Z`;
}
</script>
<template>
  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header">
          <div class="row flex-between-end">
            <div class="col-auto align-self-center">
              <h5 class="mb-0" data-anchor="data-anchor">{{ $t('categories') }} - {{ categoriesCount }}</h5>
            </div>
            <div class="col-auto ms-auto">
              <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                <button class="btn btn-sm active" data-bs-toggle="pill"
                  data-bs-target="#dom-701c8864-6a8f-4052-a697-f9036f6f4228" type="button" role="tab"
                  aria-controls="dom-701c8864-6a8f-4052-a697-f9036f6f4228" aria-selected="true"
                  id="tab-dom-701c8864-6a8f-4052-a697-f9036f6f4228">{{ $t('show') }}</button>
                <button class="btn btn-sm" data-bs-toggle="pill"
                  data-bs-target="#dom-f21a2651-c13d-4816-bdbf-a5bcc70a2d13" type="button" role="tab"
                  aria-controls="dom-f21a2651-c13d-4816-bdbf-a5bcc70a2d13" aria-selected="false"
                  id="tab-dom-f21a2651-c13d-4816-bdbf-a5bcc70a2d13">{{ $t('explane') }}</button>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="tab-content">
            <div class="tab-pane preview-tab-pane active" role="tabpanel"
              aria-labelledby="tab-dom-701c8864-6a8f-4052-a697-f9036f6f4228"
              id="dom-701c8864-6a8f-4052-a697-f9036f6f4228">
              <div class="table-responsive scrollbar">
                <table class="table table-hover">
                  <colgroup>
                    <col class="bg-soft-primary" />
                    <col />
                    <col />
                  </colgroup>
                  <thead>
                    <tr class="btn-reveal-trigger">
                      <th scope="col">{{ $t('name') }}</th>
                      <th scope="col"> </th>

                      <th class="text-center" scope="col">{{ $t('last_update') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="hover-actions-trigger" v-for="id in Object.keys(categories)" :key="id">
                      <td class="align-middle text-nowrap">
                        <div class="d-flex align-items-center">
                          <div class="ms-2">{{ categories[id].name }}</div>
                        </div>
                      </td>
                      <td class="w-auto">
                        <div class="btn-group btn-group hover-actions end-0 me-4">
                          <button @click="getCategory(id)" class="btn btn-light pe-2" type="button"
                            data-bs-toggle="modal" data-bs-placement="top" :title="$t('edit')"
                            data-bs-target="#editcategory"><span class="fas fa-edit"></span></button>
                          <button @click="getCategory(id)" class="btn btn-light ps-2" type="button"
                            data-bs-toggle="modal" data-bs-placement="top" :title="$t('delete')"
                            data-bs-target="#deletecategory"><span class="fas fa-trash-alt"></span></button>
                        </div>
                      </td>
                      <td class="align-start text-nowrap">{{ since(categories[id].updated_at) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane code-tab-pane" role="tabpanel"
              aria-labelledby="tab-dom-f21a2651-c13d-4816-bdbf-a5bcc70a2d13"
              id="dom-f21a2651-c13d-4816-bdbf-a5bcc70a2d13">
              {{ $t('categories_alert') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header">
          <div class="row flex-between-end">
            <div class="col-auto align-self-center">
              <h5 class="mb-0" data-anchor="data-anchor">{{ $t('add_category') }}</h5>
            </div>
            <div class="col-auto ms-auto">
              <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                <button class="btn btn-sm active" data-bs-toggle="pill"
                  data-bs-target="#dom-160a4566-7e94-45a2-bf04-b36ef49d954f" type="button" role="tab"
                  aria-controls="dom-160a4566-7e94-45a2-bf04-b36ef49d954f" aria-selected="true"
                  id="tab-dom-160a4566-7e94-45a2-bf04-b36ef49d954f">{{ $t('add') }}</button>
                <button class="btn btn-sm" data-bs-toggle="pill"
                  data-bs-target="#dom-c8dc7b6b-3f3c-4f91-b4e1-8f1879554e4f" type="button" role="tab"
                  aria-controls="dom-c8dc7b6b-3f3c-4f91-b4e1-8f1879554e4f" aria-selected="false"
                  id="tab-dom-c8dc7b6b-3f3c-4f91-b4e1-8f1879554e4f">{{ $t('explane') }}</button>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body bg-light">
          <div class="tab-content">
            <div class="tab-pane preview-tab-pane active" role="tabpanel"
              aria-labelledby="tab-dom-160a4566-7e94-45a2-bf04-b36ef49d954f"
              id="dom-160a4566-7e94-45a2-bf04-b36ef49d954f">
              <form>
                <div class="mb-3">
                  <label class="form-label" for="basic-form-name">{{ $t('name') }}</label>
                  <input class="form-control" id="basic-form-name" v-model="categoryFucas.name" type="text"
                    :placeholder="$t('categories_example')" />
                </div>
                <button class="btn btn-primary" type="button" @click="addcategory">{{ $t('save') }}</button>
              </form>
            </div>
            <div class="tab-pane code-tab-pane" role="tabpanel"
              aria-labelledby="tab-dom-c8dc7b6b-3f3c-4f91-b4e1-8f1879554e4f"
              id="dom-c8dc7b6b-3f3c-4f91-b4e1-8f1879554e4f">
              {{ $t('category_add_alert') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade " id="deletecategory" tabindex="-1" aria-labelledby="deletecategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-3 danger" id="deletecategoryLabel">{{ $t('warning') }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{ $t('delete_category_message') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('retract') }}</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deletecategory">{{ $t('sure')
            }}</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade " id="editcategory" tabindex="-1" aria-labelledby="editcategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-3 " id="editcategoryLabel">{{ $t('edit') }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form class="from-control">
            <input type="text" v-model="categoryFucas.name" class="form-control" :placeholder="$t('name')">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('close') }}</button>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="submitchangecategory">{{
            $t("save") }}</button>
        </div>
      </div>
    </div>
  </div>
</template>
