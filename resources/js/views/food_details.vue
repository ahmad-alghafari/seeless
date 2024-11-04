<script setup>
import { onMounted, ref, onBeforeMount } from "vue";
import axios, { Axios } from "axios";


import { useRouter } from "vue-router";
const router = useRouter();

import { useRoute } from "vue-router";
const route = useRoute();

import { useToast } from 'vue-toastification';
const toast = useToast();

import { useStore } from 'vuex';
const store = useStore();

const isLoading = ref();
const food_id = ref();
const food_details = ref({});

const foodFucas = ref({});
const categories = ref({});
const file = ref(null);
const time = ref(5000);

onBeforeMount(() => {
    if (!store.state.isLoggedIn) {
        router.push({ name: "login" });
        store.dispatch('logout');
    }
});

onMounted(() => {
    const isFluid = JSON.parse(localStorage.getItem('isFluid'));
    if (isFluid) {
        const container = document.querySelector('[data-layout]');
        container.classList.remove('container');
        container.classList.add('container-fluid');
    }
    food_id.value = route.params.id;
    fetching();
});

const uploadfileshange = (event) => {
    file.value = event.target.files[0];
    console.log("file : " + file.value);
}

const fetching = () => {
    console.log("fetching start");
    isLoading.value = true;
    axios.get("http://127.0.0.1:8000/api/food/item",
        {
            params: {
                id: food_id.value
            },
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + store.state.token
            },
        }).then(
            function (response) {
                if (response.status == 200) {
                    console.log("success");
                    food_details.value = response.data.food;
                    foodFucas.value = response.data.food;
                    categories.value = response.data.categories;
                }
            }
        ).catch(function (error) {
            if (error.response && error.response.status === 401) {
                console.log("Unauthorized - logging out.");
                store.dispatch('logout');
                router.push({ name: "login" });
            }
            toast.error('حدث خطأ ما', { timeout: 1000 });
            console.log("catch error : " + error);
        }).finally(function () {
            isLoading.value = false;
            console.log("fetching start");
        });
}



const submitchange = async () => {
    console.log("change food start");
    const formData = new FormData();
    formData.append('name', foodFucas.value.name);
    formData.append('category_id', foodFucas.value.category_id);
    formData.append('price', foodFucas.value.price);
    formData.append('discount', foodFucas.value.discount);
    formData.append('description', foodFucas.value.description);
    formData.append('id', foodFucas.value.id);

    // Append the file only if it's selected
    if (file.value) {
        formData.append('file', file.value);
    }

    axios.post("http://127.0.0.1:8000/api/food/change", formData,
        {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + store.state.token
            }
        }).then(function (response) {
            if (response.status == 200) {
                foodFucas.value.updated_at = response.data.updated_at;
                foodFucas.value.path = response.data.path;
                food_details.value = foodFucas.value;
                toast.success('تم تعديل الطعام بنجاح', { timeout: time });
            }
        }).catch(function (error) {
            if (error.response && error.response.status === 401) {
                console.log("Unauthorized - logging out.");
                store.dispatch('logout');
                router.push({ name: "login" });
            }
            toast.error('حدث خطأ ما', { timeout: time });
            console.log("catch error : " + error);
        }).then(function () {
            console.log("change food end");

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
        .catch(function (error) {
            if (error.response && error.response.status === 401) {
                console.log("Unauthorized - logging out.");
                store.dispatch('logout');
                router.push({ name: "login" });
            }
            toast.error('حدث خطأ ما', { timeout: time });
            console.log("catch error : " + error);
        }).then(function () {
            print("delete food end");
        });
}
</script>
<template>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <img class="rounded-1 fit-cover h-60 w-60" :src="`/${food_details.path}`" />
                </div>
                <div class="col-lg-6">
                    <h5>{{ food_details.name }}</h5>
                    <p class="fs--1">
                        {{ $t('category') + " : " }}
                        <router-link class="fs--1 mb-2 " :to="{ name: 'categories' }">
                            {{ categories[food_details.category_id] }}
                        </router-link>
                    </p>


                    <div class="fs--2 mb-3 d-inline-block text-decoration-none"><span
                            class="fa fa-star text-warning"></span><span class="fa fa-star text-warning"></span><span
                            class="fa fa-star text-warning"></span><span class="fa fa-star text-warning"></span><span
                            class="fa fa-star-half-alt text-warning star-icon"></span><span
                            class="ms-1 text-600">(8)</span>
                    </div>

                    <p class="fs--1">{{ food_details.description }}</p>



                    <h4 class="d-flex align-items-center" v-if="food_details.discount">

                        <span class="text-warning me-2">
                            ${{ (food_details.price - (food_details.discount * food_details.price / 100)) }}
                        </span>

                        <span class="me-1 fs--1 text-500">
                            <del class="me-1">${{ food_details.price }}</del>
                            <strong>-{{ food_details.discount }}%</strong>
                        </span>
                    </h4>

                    <h4 class="d-flex align-items-center" v-else>
                        <span class="text-warning me-2">
                            ${{ food_details.price }}
                        </span>
                        <span class="me-1 fs--1 text-500">
                            <strong>{{ $t('no_discount') }}</strong>
                        </span>
                    </h4>


                    <p class="fs--1">{{ $t('availability') + " : " }}

                        <strong class="text-success" v-if="food_details.availability">
                            {{ $t('available') }}
                        </strong>
                        <strong class="text-danger" v-else>
                            {{ $t('unavailable') }}
                        </strong>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="overflow-hidden mt-4">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item"><a class="nav-link active ps-0" id="description-tab"
                                    data-bs-toggle="tab" href="#tab-description" role="tab"
                                    aria-controls="tab-description" aria-selected="true">{{ $t('edit') }}</a></li>

                            <li class="nav-item"><a class="nav-link px-2 px-md-3" id="specifications-tab"
                                    data-bs-toggle="tab" href="#tab-specifications" role="tab"
                                    aria-controls="tab-specifications" aria-selected="false">
                                    {{ $t("rating") }}</a>
                            </li>

                            <li class="nav-item"><a class="nav-link px-2 px-md-3" id="reviews-tab" data-bs-toggle="tab"
                                    href="#tab-reviews" role="tab" aria-controls="tab-reviews" aria-selected="false">{{
                                        $t('explane') }}</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
                                aria-labelledby="description-tab">
                                <div class="row mt-3">
                                    <div class="col-lg-6 ps-lg-5">
                                        <form>
                                            <div class="mb-3">
                                                <label class="form-label" for="name">{{ $t('name')
                                                    }}:</label>
                                                <input class="form-control" id="name" type="text"
                                                    v-model="foodFucas.name" />
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="formGrouptextareaInput">{{
                                                    $t('description') }}:</label>
                                                <textarea class="form-control" id="formGrouptextareaInput" rows="3"
                                                    v-model="food_details.description">{{ food_details.description }}</textarea>

                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="category">{{ $t('description')
                                                    }}:</label>

                                                <select name="category_id" id="category" v-model="foodFucas.category_id"
                                                    class="form-select form-select-sm">
                                                    <option v-for="key in Object.keys(categories)" :key="key"
                                                        :value="key" :selected="key == foodFucas.category_id">
                                                        {{ categories[key] }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="price">
                                                    {{ $t('price') }}:</label>
                                                <input type="number" name="price" v-model="foodFucas.price"
                                                    :placeholder="$t('price')" id="price" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="discount">
                                                    {{ $t('discount') }}:</label>
                                                <input type="number" min="0" max="100" name="discount"
                                                    :placeholder="$t('discount')" id="discount"
                                                    v-model="foodFucas.discount" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="image">
                                                    {{ $t('image') }}:</label>
                                                <input type="file" @change="uploadfileshange" class="form-control"
                                                    id="image">

                                            </div>
                                            <button class="btn btn-primary" type="button" @click="submitchange">{{
                                                $t('save') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-specifications" role="tabpanel"
                                aria-labelledby="specifications-tab">
                                <br>
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                    <div class="mb-1"><span class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star text-warning fs--1"></span><span
                                            class="ms-3 text-dark fw-semi-bold">Awesome support, great code
                                            😍</span>
                                    </div>
                                    <p class="fs--1 mb-2 text-600">By Drik Smith • October 14, 2019</p>
                                    <p class="mb-0">You shouldn't need to read a review to see how nice and polished
                                        this theme is. So I'll tell you something you won't find in the demo. After
                                        the download I had a technical question, emailed the team and got a response
                                        right from the team CEO with helpful advice.</p>
                                    <hr class="my-4" />
                                    <div class="mb-1"><span class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star text-warning fs--1"></span><span
                                            class="fa fa-star-half-alt text-warning star-icon fs--1"></span><span
                                            class="ms-3 text-dark fw-semi-bold">Outstanding Design, Awesome
                                            Support</span>
                                    </div>
                                    <p class="fs--1 mb-2 text-600">By Liane • December 14, 2019</p>
                                    <p class="mb-0">This really is an amazing template - from the style to the font
                                        - clean layout. SO worth the money! The demo pages show off what Bootstrap 4
                                        can impressively do. Great template!! Support response is FAST and the team
                                        is amazing - communication is important.</p>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="mt-3">
                                    {{ $t('edit_food_explane') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>