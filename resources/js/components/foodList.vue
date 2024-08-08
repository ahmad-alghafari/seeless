
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
  printCart();
}

const decreaseQuantity = (id) => {
  if(cart.value[id] > 1 ){
    cart.value[id]--;
  }else if(cart.value[id] == 1){
    delete cart.value[id];
  }
  total.value -= props.food[id]['price'];
  printCart();
}

const deleteAllquantity = (id) => {
  if(cart.value[id]){
    total.value -= props.food[id]['price'] * cart.value[id] ;
    delete cart.value[id];
  }
  printCart();
}

const deleteCartContents = () => {
  cart.value = {};
  total.value = 0 ;
  printCart();
}

const submit = () => {
  if(cart.value.isEmpty){
    console.log("cart is empty : " + cart.value);
    return;
  }else{
    console.log("cart is not empty : " );
    printCart();
    sendRequest();
  }
}

const sendRequest = async () => {
    const response = await axios.post('http://127.0.0.1:8000/api/resturant/order' 
      ,{
        resturantId  : props.resturantId , 
        tableNumber : props.tableNumber ,
        orderContents : cart.value , 
      }
      ,{ headers: {
      'Content-Type': 'application/json'
        }
      }
    ).then(function (response) {
    console.log("response : " + response.data);
    cart.value = {} ;
    total.value = 0 ;
    })
    .catch(function (error) {
    console.log("error : " + error);
    });
  
}

const isAdded = (id) => {
  if(cart.value[id]){
    return true ;
  }else{
    return false;
  }
}

const isEmpty = () => {
  if(cart.value.length == 0){ 
    return true ;
  }else{
    return false;
  }
}
</script>
<template>
  <section class="food_section layout_padding">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
          Our Menu 
        </h2>
      </div>

      <ul class="filters_menu">
        <li class="active" data-filter="*">All </li>
        <li v-for="cat in props.categories" :key="cat.index" :data-filter="`.${cat}`">{{ cat }}</li>
      </ul>

      <div class="filters-content">
        <div class="row grid">
          <!-- !-- loop food start -- -->
          <div v-for="fod in food " :key="fod.id" :class="`col-sm-6 col-lg-4 all ${props.categories[fod.category_id]} `">
            <div class="box">
              <div>
                <div class="img-box">
                  <img src="http://127.0.0.1:8000/layouts/1/images/f1.png" alt="">
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
                    
                    
                 
                    <button type="button" @click="add_increase(fod.id)" >
                    add to cart
                    </button>
                    <button type="button" @click="add_increase(fod.id)" >
                      increase 
                    </button>
                    <button type="button" @click="deleteAllquantity(fod.id)" >
                      delete 
                    </button>
                    <button type="button" @click="decreaseQuantity(fod.id)" >
                      decrease
                    </button>
                  </div>
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>  
  <button  type="button" class="floating-button btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">Cart</button>
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Cart Contents</h1>
        </div>
        <div class="modal-body">
          
          <table  class="table table-striped">
            <thead>
              <tr>
                <th>test</th>
                <th>Food Name</th>
                <th>Number</th>
                <th>Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>

              <tr v-for="(quantity, id) in cart" :key="id" >
                <td>{{ id }}</td>
                <td>{{ props.food[id]['name'] }}</td>
                <td>{{ quantity }}</td>
                <td>{{ props.food[id]['price'] }}</td>
                <td>{{ quantity * props.food[id]['price']}}</td>
               </tr> 
            </tbody>
          </table>
          <p>Total Invoice Value : {{ total }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondry" data-bs-dismiss="modal" aria-label="Close" >Cansel</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteCartContents">Delete Contents</button>
          <button type="button" class="btn btn-primary" @click="submit">Submit</button>
        </div>
      </div>
    </div>
  </div>
</template>



<style>
.floating-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
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
