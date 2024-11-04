<script setup>
import { ref, onBeforeMount, onMounted } from 'vue';
import axios, { Axios } from 'axios';

import { useRouter } from "vue-router";
const router = useRouter();

import { useToast } from 'vue-toastification';
const toast = useToast();

import { useStore } from 'vuex';
const store = useStore();

const foodFucas = ref({});
const file = ref(null);
const time = ref(5000);
const categories = ref({});
const isLoading = ref();


onBeforeMount(() => {
    if (!store.state.isLoggedIn) {
        router.push({ name: "login" });
        store.dispatch('logout');
    }
});

onMounted(() => {
    fetching();
});


const print = (variable) => {
    console.log(variable);
}

const uploadfileshange = (event) => {
    file.value = event.target.files[0];
}

const fetching = async () => {
    isLoading.value = true;

    print("fetching : start");

    axios.get('http://127.0.0.1:8000/api/category',
        {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + store.state.token
            }
        }
    ).then(
        function (response) {
            if (response.status == 200) {
                categories.value = response.data.categories;
                foodFucas.value.category_id = Object.keys(categories.value)[0];
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
</script>
<template>
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" data-anchor="data-anchor">{{ $t('add_dishe') }}</h5>
                    </div>

                </div>
            </div>
            <div class="card-body bg-light">
                <div class="tab-content">
                    <div class="tab-pane preview-tab-pane active" role="tabpanel"
                        aria-labelledby="tab-dom-160a4566-7e94-45a2-bf04-b36ef49d954f"
                        id="dom-160a4566-7e94-45a2-bf04-b36ef49d954f">
                        <form >
                            <div class="md-4">
                                <label class="form-label" for="basic-form-name">{{ $t('name') }}</label>
                                <input class="form-control" v-model="foodFucas.name" id="basic-form-name" type="text"
                                    :placeholder="`${$t('food_example')}`" />
                            </div>
                            <div class="md-4">
                                <label class="form-label" for="basic-form-name">{{ $t('name') }}</label>

                                <select class="form-control" name="category_id" id="category" v-model="foodFucas.category_id">
                                    <option v-for="(key , index ) in Object.keys(categories)" 
                                    :key="key" 
                                    :value="key">
                                            {{ categories[key].name }}
                                    </option>
                                </select>
                            </div>
                            <div class="md-4">
                                <label class="form-label" for="basic-form-name">{{ $t('price') }}</label>

                                <input class="form-control" type="number" name="price" v-model="foodFucas.price"
                                placeholder="130" id="price">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-form-name">{{ $t('description') }}</label>

                                <textarea class="form-control" name="description" id="description"
                                v-model="foodFucas.description"></textarea>
                            </div>
                            <div class="mb-3">
                                <input  class="form-control" type="file" @change="uploadfileshange">
                            </div>
                            <button class="btn btn-primary" type="button" @click="submit">{{ $t('save') }}</button>
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

</template>