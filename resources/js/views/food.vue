<script setup>
import { onMounted, reactive, ref ,defineProps } from 'vue';
import axios, { Axios } from 'axios';
import { useRoute } from "vue-router";
const route = useRoute();

const foods = ref({});
const categories = ref({});
const isLoading = ref(true);

const foodFucas = ref({});
const categoryFucas = ref({});

const file = ref(null)
const id = ref(null);

onMounted( async ()  => {
    id.value = route.params.id;
    console.log("id : " + id.value);
     fetching();
});

const getFood = (id) => {
    foodFucas.value = foods.value[id] ;
}

const getCategory = (id) => {
    categoryFucas.value.id = id ;
    console.log("id test : " +categoryFucas.value.id);

    categoryFucas.value.name = categories.value[id];
    console.log("name test : " +categoryFucas.value.name);

}

const freeFoodFuces = () => {
    foodFucas.value = {} ;
}

const uploadfileshange = (event) => {
    file.value = event.target.files[0];
    console.log("file : " + file.value);
}

const print_food = () => {

  console.log("food object contnets :");
  for (const id in foods.value) {
    console.log("id :" + id);
    console.log("food :" + foods.value[id].name);

  } 
}

const oppisit = (id) => {
  if(foods.value[id].availability == "availble"){
      return "not_available";
  }else{
      return "availble";
  }
}

// fetching methods ------
const fetching = async ()  => {
  console.log("fetching : start");

     axios.get('http://127.0.0.1:8000/api/foodAndCategories/'+id.value).then(
        function (response){
            if(response.data.status == '200'){
                foods.value = response.data.food ;
                categories.value = response.data.categories ;
            }
            isLoading.value = false ;
            console.log("fetching : end");
        }
    ).catch(
        function (error) {
            console.log("catch error : " + error);
        }
    );
}

const submit = async () => {
    const formData = new FormData();
    formData.append('name', foodFucas.value.name);
    formData.append('category_id', foodFucas.value.category_id);
    formData.append('price', foodFucas.value.price);
    formData.append('description', foodFucas.value.description);

    // Append the file only if it's selected
    if (file.value) {
        formData.append('file', file.value);
    }

    try {
        const response = await axios.post("http://127.0.0.1:8000/api/food/add/"+id.value, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.status === "200") {
            console.log("response status: " + response.data.status);
            foodFucas.value = response.data.food;
            foods.value[foodFucas.value.id] = foodFucas.value;
        } else {
            console.log("message: " + response.data.message);
        }
        console.log("fetching: end with response");
    } catch (error) {
        console.log("catch error: " + error);
        console.log("fetching: end with catch");
    }

    foodFucas.value = {};
}

const submitchange = async () => {
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

    try {
        const response = await axios.post("http://127.0.0.1:8000/api/food/change", formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.status === "200") {
            console.log("response status: " + response.data.status);
            foodFucas.value.updated_at = response.data.updated_at;
            foodFucas.value.path = response.data.path;
            foods.value[foodFucas.value.id] = foodFucas.value;
        } else {
            console.log("message: " + response.data.message);
        }
        console.log("fetching: end with response");
    } catch (error) {
        console.log("catch error: " + error);
        console.log("fetching: end with catch");
    }

    foodFucas.value = {};
}

const postChangStatus = async (id) => {
    let temp = oppisit(id);
    axios.post("http://127.0.0.1:8000/api/food/status" , {
        status : oppisit(id) ,
        id : id
    }).then(function(response){
        if(response.data.status == "200" ){
            foods.value[id].availability = temp ;
            console.log(temp);
        }else{
          console.log(response.data.message);
        }
        console.log("change status end");

    }).catch(
        function (error) {
            console.log("catch error : " + error);
            console.log("change status end");
        }
    );
}

const deleteFood = async (id) =>{
  axios.delete("http://127.0.0.1:8000/api/food/delete/"+id )
    .then(
      function(response){
        if(response.data.status == "200" ){
            delete foods.value[id];
        }
        console.log(response.data.message);
    })
    .catch(
        function (error) {
            console.log("catch error : " + error);
            console.log("change status end");
        }
    );
}

const submitchangecategory = async () =>{
  console.log("change name of category start");
  axios.post("http://127.0.0.1:8000/api/category/edit/"+categoryFucas.value.id  , {
    name : categoryFucas.value.name ,
  }).then(
    function(response){
      if(response.data.status == "200"){
        categories.value[categoryFucas.value.id] = categoryFucas.value.name ;
        console.log(categories.value[categoryFucas.value.id]);
        
      }else{
        console.log(response.data.message);
      }
    }
).catch(
        function (error) {
            console.log("catch error : " + error);
            console.log("change status end");
        }
  );
  console.log("change name of category end");

  categoryFucas.value = {};
}
</script>

<template>
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">الأطعمة  </h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الأسم</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">الصنف</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">التوفر</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">السعر</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">التخفيض</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">زمن آخر تعديل</th>
                      <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th> -->
                      <th class="text-secondary opacity-7"></th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody v-show="isLoading == false" >
                    <tr v-for="food in foods" :key="food.id">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img :src="`http://127.0.0.1:8000/${food.path}`" class="avatar avatar-sm me-3 border-radius-lg" alt="user1">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ food.name}}</h6>
                            <!-- <p class="text-xs text-secondary mb-0">john@creative-tim.com</p> -->
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ categories[food.category_id]}}</p>
                        <!-- <p class="text-xs text-secondary mb-0">Organization</p> -->
                      </td>
                      <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-success" v-if="food.availability == 'availble'" @click="postChangStatus(food.id)">متوفر</span>
                            <span class="badge badge-sm bg-gradient-secondary" v-else @click="postChangStatus(food.id)">غير متوفر</span>
                        <!-- </button> -->

                        
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ food.price }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold" v-if="food.discount == null">لا</span>
                        <span class="text-secondary text-xs font-weight-bold" v-else>{{ food.discount }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ food.updated_at.split('T')[0] }}</span>
                      </td>
                      <td class="align-middle">
                        <!-- <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                          تعديل
                        </a> -->
                        <button type="button" @click="getFood(food.id)" class="btn btn-secondary font-weight-bold" data-bs-toggle="modal" data-bs-target="#editfood" >
                            تعديل
                        </button>
                        
                      </td>
                      <td class="align-middle">
                        <button type="button" @click="deleteFood(food.id)" class="btn btn-danger font-weight-bold" >
                            حذف
                        </button>
                      </td>
                    </tr>
                  </tbody>
                  <!-- <tbody >
                    loading
                  </tbody> -->
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#addfood" class="custom-button" @click="freeFoodFuces">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">add</i>
                    </div>
                </button>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">إضافة</p>
                <h4 class="mb-0">طعام</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
              <p class="mb-0 text-start">
                أحرص على إضافة جميع الحقول التى تخص الطعام بشكل صحيح    
            </p>
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
                <h6 class="text-white text-capitalize ps-3">الأصناف</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الأسم</th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="id in Object.keys(categories)" :key="id">
                      <td>
                        <div class="d-flex px-2">
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">{{ categories[id] }}</h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button type="button" @click="getCategory(id)" class="btn btn-secondary font-weight-bold" data-bs-toggle="modal" data-bs-target="#editcategory" >
                            تعديل
                        </button>
                        
                      </td>
                      <td class="align-middle">
                        <button type="button"  class="btn btn-danger font-weight-bold" >
                            حذف
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
    

<!-- Modal -->
<div class="modal fade dark" id="editfood" tabindex="-1" aria-labelledby="editfoodLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editfoodLabel">تعديل على المعلومات المحفوظة</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="from-control">
            <input type="text" name="name" id="name"  v-model="foodFucas.name" class="form-control" placeholder="الأسم">
            <select name="category_id" id="category" v-model="foodFucas.category_id">
                <option  v-for="key in Object.keys(categories)" :key="key" :value="key" :selected="key == foodFucas.category_id" >
                    {{ categories[key] }}
                </option>
            </select>
            <br>
            <input type="number" name="price" v-model="foodFucas.price" placeholder="السعر" id="price">
            <input type="number" name="discount" placeholder="التخفيض" id="discount" v-model="foodFucas.discount">
            <textarea name="description" id="description" v-model="foodFucas.description">{{ foodFucas.description }}</textarea>
            <img :src="foodFucas.path" >
            <input type="file" @change="uploadfileshange">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-primary"  data-bs-dismiss="modal" @click="submitchange">حفظ</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade dark" id="addfood" tabindex="-1" aria-labelledby="addfoodLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addfoodLabel">إضافة طبق جديد</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="from-control">
            <input type="text" name="name" id="name"  v-model="foodFucas.name" class="form-control" placeholder="الأسم">
            <select name="category_id" id="category" v-model="foodFucas.category_id">
                <option  v-for="key in Object.keys(categories)" :key="key" :value="key" >
                    {{ categories[key] }}
                </option>
            </select>
            <br>
            <input type="number" name="price" v-model="foodFucas.price" placeholder="السعر" id="price">
            <textarea name="description" id="description" v-model="foodFucas.description">أضف الوصف هنا</textarea>
            <input type="file" @change="uploadfileshange">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-primary"  data-bs-dismiss="modal" @click="submit">حفظ</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade dark" id="editcategory" tabindex="-1" aria-labelledby="editcategoryLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editcategoryLabel">تعديل على المعلومات المحفوظة</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="from-control">
            <input type="text"  v-model="categoryFucas.name" class="form-control" placeholder="الأسم">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-primary"  data-bs-dismiss="modal" @click="submitchangecategory">حفظ</button>
      </div>
    </div>
  </div>
</div>
</template>