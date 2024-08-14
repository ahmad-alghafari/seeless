
<script setup >
import axios from 'axios';
import {defineProps  , ref  } from 'vue';

const props = defineProps({
  categories: Object, // Ensure categories is defined as an Array
  food: Object,
  resturantId : Number,
  tableNumber : Number
});


const cart = ref({});
const total = ref(0);
const isempty = ref(true);


const display_flex = (id) => {
  add_increase(id);
  const div_by_id = document.getElementById(`${id}`);
  console.log("dd" + div_by_id.value);
  div_by_id.style.display = "flex";
}



const printCart = () => {
  console.log("Cart Contsents:");
  for (const id in cart.value) {
    if (cart.value.hasOwnProperty(id)) {
      console.log(`ID: ${id}, Quantity: ${cart.value[id]}`);
      
    }
  } 
  console.log('total : ' + total.value);
}


const add_increase = (id) => {
  if (cart.value[id]) {
    cart.value[id]++;
    } else {
    cart.value[id] = 1;
  }
  total.value  += props.food[id]['price'];
  isEmpty();
  printCart();
}

const decreaseQuantity = (id) => {
  if(cart.value[id] > 1 ){
    cart.value[id]--;
  }else if(cart.value[id] == 1){
    delete cart.value[id];
    const div_by_id = document.getElementById(`${id}`);
    div_by_id.style.display =  "none" ;
  }
  total.value -= props.food[id]['price'];
  isEmpty();
  printCart();
}

const deleteAllquantity = (id) => {
  const div_by_id = document.getElementById(`${id}`);
  div_by_id.style.display =  "none" ;
  if(cart.value[id]){
    total.value -= props.food[id]['price'] * cart.value[id] ;
    delete cart.value[id];
  }
  isEmpty();
  printCart();

}

const deleteCartContents = () => {
  cart.value = {};
  total.value = 0 ;
  isEmpty();
  printCart();
};

const submit = () => {
  if (Object.keys(cart.value).length === 0) {
    console.log("cannot send request with empty cart");
  } else {
    console.log("cart is not empty, will send request");
    sendRequest();
  }
};

const sendRequest = async () => {
    const response = await axios.post('http://127.0.0.1:8000/api/resturant/order' 
      ,{
        resturant_id  : props.resturantId , 
        table_number : props.tableNumber ,
        order_contents : cart.value , 
      }
      ,{ headers: {
      'Content-Type': 'application/json'
        }
      }
    ).then(function (response) {
    console.log("response : " + response.data.message);
    if(response.data.message == 'order sent successfuly'){
      deleteCartContents();
    }
    printCart();
    })
    .catch(function (error) {
    console.log("error : " + error);
    });
}


const isAdded = (id) => {
  if(cart.value[id]){
    return "none" ;
  }else{
    return "flex";
  }
}

const isEmpty = () => {
  if(Object.keys(cart.value).length === 0){ 
    isempty.value = true ;
  }else{
    isempty.value = false ;
  }
}



</script>
<template>
  <section class="food_section layout_padding">
    <div class="container">
      <div class="heading_container heading_center">
        <h2 class="zoomIn">
          قائمة الطعام
        </h2>
      </div>

      <ul class="filters_menu" >
        <li class="active" data-filter="*">الكل  </li>
        <li v-for="cat in props.categories" :key="cat.index" :data-filter="`.${cat}`">{{ cat }}</li>
      </ul>
      <div class="filters-content">
        <div class="row grid"  >
          <!-- !-- loop food start -- -->
          <div v-for="fod in food " :key="fod.id" :class="`col-sm-6 col-lg-4 all ${props.categories[fod.category_id]} `" >
            <div class="box zoomIn">
              <div>
                <div class="img-box">
                  <img :src="`/${food.path}`" alt="">
                </div>
                <div class="detail-box">
                    <h5>
                      {{ fod.name }}
                    </h5>
                    <p>
                      {{ fod.description }}
                    </p>
                    <p>
                      {{ fod.availability }}
                    </p>
                    
                    <div class="options">
                      <h6>
                        {{ fod.price }} SR
                      </h6>

                      <!-- icon start -->
                      <a class="cart_link "  @click="display_flex(fod.id)" :style="`display: ${isAdded(fod.id)} ;`">
                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 456.029 456.029" style="enable-background:new 0 0 456.029 456.029;" xml:space="preserve">
                    <g>
                      <g>
                        <path d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248
                    c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                      </g>
                    </g>
                    <g>
                      <g>
                        <path d="M439.296,84.91c-1.024,0-2.56-0.512-4.096-0.512H112.64l-5.12-34.304C104.448,27.566,84.992,10.67,61.952,10.67H20.48
                    C9.216,10.67,0,19.886,0,31.15c0,11.264,9.216,20.48,20.48,20.48h41.472c2.56,0,4.608,2.048,5.12,4.608l31.744,216.064
                    c4.096,27.136,27.648,47.616,55.296,47.616h212.992c26.624,0,49.664-18.944,55.296-45.056l33.28-166.4
                    C457.728,97.71,450.56,86.958,439.296,84.91z" />
                      </g>
                    </g>
                    <g>
                      <g>
                        <path d="M215.04,389.55c-1.024-28.16-24.576-50.688-52.736-50.688c-29.696,1.536-52.224,26.112-51.2,55.296
                    c1.024,28.16,24.064,50.688,52.224,50.688h1.024C193.536,443.31,216.576,418.734,215.04,389.55z" />
                      </g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                  </svg>
                      </a>
                      <!-- icon end -->

                      <!-- at the bottom start -->
                      <div class="atTheButtom" :id="fod.id" v-if="cart[fod.id] != 0" >
                        <button type="button" id="plus" @click="add_increase(fod.id)">
                          <i class="fa fa-plus"></i>
                        </button>
                        <div class="numOfCate" v-if="cart[fod.id]">
                          <h5>{{ cart[fod.id]  }}</h5>
                        </div>
                        <button type="button" id="minus" @click="decreaseQuantity(fod.id)">
                          <i class="fa fa-minus"></i>
                        </button>
                        <a class="delete" @click="deleteAllquantity(fod.id)" >
                          X
                        </a>
                      </div> 
                      <!-- at the bottom end -->
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>  
  <!-- fixed cart button start -->
  <button  type="button" class="floating-button btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">شراء</button>
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">محتويات الطلب</h1>
        </div>
        <div class="modal-body">
          <table  class="table table-striped">
            <thead>
              <tr>
                <th>الطلب</th>
                <th>العدد</th>
                <th>السعر</th>
                <th>المجموع</th>
              </tr>
            </thead>
            <tbody>

              <tr v-for="(quantity, id) in cart" :key="id" >
                <td>{{ props.food[id]['name'] }}</td>
                <td>{{ quantity }}</td>
                <td>{{ props.food[id]['price'] }}</td>
                <td>{{ quantity * props.food[id]['price']}}</td>
               </tr> 
            </tbody>
          </table>
          <p>قيمة الفاتورة : {{ total }} </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="submit"  :disabled="isempty">إرسال الطلب</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteCartContents" :disabled="isempty">حذف المحتويات</button>
          <button type="button" class="btn btn-secondry" data-bs-dismiss="modal" aria-label="Close" >إغلاق</button>

        </div>
      </div>
    </div>
  </div>
</template>



<style>
.floating-button {
  position: fixed;
  bottom: 20px;
  left: 20px;
  width: 60px;
  height: 60px;
  background-color: #f39c12;
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 24px;
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.3);
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

@media (max-width: 600px) {
  .floating-button {
    width: 50px;
    height: 50px;
    font-size: 20px;
    bottom: 15px;
    right: 15px;
  }
}
</style>
