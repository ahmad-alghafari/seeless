<script setup>
import { onMounted, ref  , computed } from 'vue';
import axios, { Axios } from 'axios';
import { useRoute } from "vue-router";
import { useToast } from 'vue-toastification';

const route = useRoute();
const toast = useToast();

const foods = ref({});
const categories = ref({});
const isLoading = ref(true);

const foodFucas = ref({});
const categoryFucas = ref({});

const file = ref(null)
const id = ref(null);

const foodCount = computed(() => Object.keys(foods.value).length);

const categoriesCount = computed(() => Object.keys(categories.value).length);



const availablefoodCount = computed(() => {
    return Object.values(foods.value).filter(food => food.availability === "availble").length;
});



onMounted( async ()  => {
  id.value = route.params.id;
  fetching();
});

const dd = (str = "" , variable) =>{
  console.log(str +" : " + variable);
} 
const print = ( variable) =>{
  console.log(variable);
} 
const getFood = (id) => {
    foodFucas.value = foods.value[id] ;
}

const getCategory = (id) => {
    categoryFucas.value.id = id ;
    categoryFucas.value.name = categories.value[id];
}

const freeCategoryFuces = () => {
    categoryFucas.value = {};
}

const freeFoodFuces = () => {
    foodFucas.value = {} ;
}

const uploadfileshange = (event) => {
    file.value = event.target.files[0];
    console.log("file : " + file.value);
}



const oppisit = (id) => {
  if(foods.value[id].availability == "availble"){
      return "not_available";
  }else{
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
const fetching = async ()  => {

  print("fetching : start");

     axios.get('http://127.0.0.1:8000/api/foodAndCategories/'+id.value).then(
        function (response){
            if(response.data.status == '200'){
                foods.value = response.data.food ;
                categories.value = response.data.categories ;
            }
            isLoading.value = false ;
            print("fetching : end");
        }
    ).catch(
        function (error) {
            console.log("catch error : " + error);
            print("fetching : end");
        }
    );
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

    try {
        const response = await axios.post("http://127.0.0.1:8000/api/food/add/"+id.value, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.status === "200") {
            foodFucas.value = response.data.food;
            foods.value[foodFucas.value.id] = foodFucas.value;
            toast.success('تم إضافة الطعام بنجاح' , {timeout : 1000});
        } else {
            console.log("message: " + response.data.message);
            toast.error('حدث خطأ ما' , {timeout : 1000});
        }
        print("add food end");
    } catch (error) {
        toast.error('حدث خطأ ما' , {timeout : 1000});
        print("add food end");
    }

    foodFucas.value = {};
}

const submitchange = async () => {
  print("change food start");
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
            foodFucas.value.updated_at = response.data.updated_at;
            foodFucas.value.path = response.data.path;
            foods.value[foodFucas.value.id] = foodFucas.value;
            toast.success('تم إضافة الطعام بنجاح' , {timeout : 1000});
        } else {
          toast.error('حدث خطأ ما' , {timeout : 1000});
        }
        print("change food end");
    } catch (error) {
        toast.error('حدث خطأ ما' , {timeout : 1000});
        print("change food end");
    }
    foodFucas.value = {};
}

const postChangStatus = async (id) => {
  print("change status of food start");
    let temp = oppisit(id);
    axios.post("http://127.0.0.1:8000/api/food/status" , {
        status : oppisit(id) ,
        id : id
    }).then(function(response){
        if(response.data.status == "200" ){
            foods.value[id].availability = temp ;
            foods.value[id].updated_at  = response.data.updated_at ;
            toast.success('تم تغيير حالة الطعام بنجاح' , {timeout : 1000});
        }else{
          toast.error('حدث خطأ ما' , {timeout : 1000});
        }
        print("change status of food end");
    }).catch(
        function (error) {
          toast.error('حدث خطأ ما' , {timeout : 1000});
          print("change status of food end");
        }
    );

}



const deleteFood = async (id) =>{
  print("delete food start");
  axios.delete("http://127.0.0.1:8000/api/food/delete/"+id )
    .then(
      function(response){
        if(response.data.status == "200" ){
            delete foods.value[id];
            toast.success('تم حذف الطعام بنجاح' , {timeout : 1000});
        }else{
          toast.error('حدث خطأ ما' , {timeout : 1000});
        }
        print("delete food end");

    })
    .catch(
        function (error) {
          toast.error('حدث خطأ ما' , {timeout : 1000});
          print("delete food end");
        }
    );
}

const submitchangecategory = async () =>{
  print("change name of category start");
  axios.post("http://127.0.0.1:8000/api/category/edit/"+categoryFucas.value.id  , {
    name : categoryFucas.value.name ,
  }).then(
    function(response){
      if(response.data.status == "200"){
        categories.value[categoryFucas.value.id] = categoryFucas.value.name ;
        toast.success('تم تعديل الصنف بنجاح' , {timeout : 1000});
      }else{
        toast.error('حدث خطأ ما' , {timeout : 1000});
      }
      freeCategoryFuces();
      print("change name of category end");

    }
  ).catch(
        function (error) {
          toast.error('حدث خطأ ما' , {timeout : 1000});
          print("change name of category end");
        }
  );
}

const deletecategory = async () =>{
  print("delete category start");
  axios.delete("http://127.0.0.1:8000/api/category/delete/"+categoryFucas.value.id
  ).then(function(response){
    if(response.data.status == '200'){
      delete categories.value[categoryFucas.value.id];
      toast.success('تم حذف الصنف بنجاح' , {timeout : 1000});

    }else{
      toast.error('حدث خطأ ما' , {timeout : 1000});
    }
    freeCategoryFuces();
    print("delete category end");

  }).catch(
    function (error) {
      toast.error('حدث خطأ ما' , {timeout : 1000});
      print("delete category end");
    }
  );
}

const addcategory = async () => {
  print("add category start");
  axios.post("http://127.0.0.1:8000/api/category/create/"+id.value  , {
    name : categoryFucas.value.name ,
  }).then(
    function(response){
      if(response.data.status == "200"){
        categories.value[response.data.id] = categoryFucas.value.name ;
        toast.success('تم إضافة الصنف بنجاح' , {timeout : 1000});
      }else{
        toast.error('حدث خطأ ما' , {timeout : 1000});
      }
      freeCategoryFuces();
      print("add category end");
    }
  ).catch(
        function (error) {
          toast.error('حدث خطأ ما' , {timeout : 1000});
          print("add category end");
        }
  );
}
</script>

<template>
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#addfood" class="custom-button" @click="freeFoodFuces" style="border: 0px  ; border-width: 0;">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">add</i>
                    </div>
                </button>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">إضافة</p>
                <h4 class="text-center">طعام</h4>
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
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">leaderboard</i>
              </div>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">عدد الأطعمة</p>
                <h4 class="text-center">{{ foodCount }}</h4>
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
                <p class="text-sm mb-0 text-capitalize">عدد الأطعمة المتاحة</p>
                <h4 class="text-center">{{ availablefoodCount }}</h4>
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
                <h6 class="text-white text-capitalize ps-3">الأطعمة  </h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary font-weight-bolder ">الأسم</th>
                      <th class="text-center text-uppercase text-secondary font-weight-bolder ">الصنف</th>
                      <th class="text-center text-uppercase text-secondary font-weight-bolder ">التوفر</th>
                      <th class="text-center text-uppercase text-secondary font-weight-bolder "> السعر ل.س</th>
                      <th class="text-center text-uppercase text-secondary font-weight-bolder ">التخفيض</th>
                      <th class="text-center text-uppercase text-secondary font-weight-bolder ">زمن آخر تعديل</th>
                      <th class="text-secondary opacity-7"></th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody v-show="isLoading == false" >
                    <tr v-for="food in foods" :key="food.id">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img :src="`/${food.path}`" class="avatar avatar-sm me-3 border-radius-lg" alt="user1">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ food.name}}</h6>
                          </div>
                        </div>
                      </td>

                      <td>
                        <p class="text-center font-weight-bold ">{{ categories[food.category_id]}}</p>
                      </td>

                      <td class="align-middle text-center text-sm">
                        <!-- <span class="badge badge-sm bg-gradient-success"  @click="postChangStatus(food.id)">متوفر</span> -->
                        <button v-if="food.availability == 'availble'" 
                        class="btn bg-gradient-success w-100" 
                        @click="postChangStatus(food.id)"
                        type="button">
                        متوفر
                      </button>
                        <!-- <span class="badge badge-sm bg-gradient-secondary" v-else @click="postChangStatus(food.id)">غير متوفر</span> -->
                      <button 
                        v-else
                        
                        class="btn bg-gradient-warning w-100  "
                        @click="postChangStatus(food.id)"
                        >
                        غير متوفر
                      </button>
                      </td>

                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ food.price }}</span>
                      </td>

                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold" v-if="food.discount == null">لا يوجد</span>
                        <span class="text-secondary text-xs font-weight-bold" v-else>{{ food.discount }}</span>
                      </td>

                      <td class="align-middle text-center">
                        <!-- <span class="text-secondary text-xs font-weight-bold">{{ food.updated_at.split('T')[0] }}</span> -->
                        <span class="text-secondary text-xs font-weight-bold">{{ since(food.updated_at) }}</span>

                      </td>
                      <td class="d-flex justify-content-center text-center">
                        <!-- <div class=""> -->

                          
                          <button type="button" @click="getFood(food.id)" class="btn bg-gradient-info w-100" data-bs-toggle="modal" data-bs-target="#editfood" >
                            تعديل
                          </button>
                        <!-- </div> -->
                      </td>
                      <td class="align-middle">
                        <button type="button" @click="deleteFood(food.id)" class="btn btn-danger font-weight-bold" >
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

      <div class="row">
        <div class="col-lg-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#addcategory" class="custom-button" @click="freeCategoryFuces" style="border-width: 0; border: hidden;">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">add</i>
                    </div>
                </button>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">إضافة</p>
                <h4 class="text-center">صنف</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
              <p class="mb-0 text-start">
                   الصنف يظهر للمستخدم على أنه نوع المأكولات او المشروبات وكل طعام ينتمي إلى صنف 
              </p>
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
                <p class="text-sm mb-0 text-capitalize">عدد الأصناف</p>
                <h4 class="text-center">{{ categoriesCount }}</h4>
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
                <h6 class="text-white text-capitalize ps-3">الأصناف</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-start font-weight-bolder opacity-7" style="padding-right: 110px;">الأسم</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="id in Object.keys(categories)" :key="id">
                      <td class="align-middle text-center">
                        <div class="row">
                          <div class="col-3 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                              <h6>{{ categories[id] }}</h6>
                            </div>
                          </div>
                        <div class="col-1">
                          <button type="button" @click="getCategory(id)" class="btn btn-secondary font-weight-bold" data-bs-toggle="modal" data-bs-target="#editcategory" >
                            تعديل
                          </button>
                        </div>
                        <div class="col-1">
                          <button type="button" @click="getCategory(id)" class="btn btn-danger font-weight-bold" data-bs-toggle="modal" data-bs-target="#deletecategory">
                            حذف
                          </button>
                        </div>
                      </div>
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
        <h1 class="modal-title fs-5" id="editcategoryLabel">تعديل اسم الصنف</h1>
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

<div class="modal fade dark" id="deletecategory" tabindex="-1" aria-labelledby="deletecategoryLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="deletecategoryLabel">تحذير</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>في حال حذفت صنف طعام ما , فقد يتم جميع الأطعمة التي تندرج تحت هذا الصنف</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-danger"  data-bs-dismiss="modal" @click="deletecategory">تأكيد</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade dark" id="addcategory" tabindex="-1" aria-labelledby="addcategoryLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addcategoryLabel">إضافة صنف جديد   </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="from-control">
            <input type="text"  v-model="categoryFucas.name" class="form-control" placeholder="الأسم">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-primary"  data-bs-dismiss="modal" @click="addcategory">حفظ</button>
      </div>
    </div>
  </div>
</div>


</template>