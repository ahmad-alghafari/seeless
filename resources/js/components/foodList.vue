<script setup >
import {defineProps ,onMounted , ref , reactive } from 'vue';

const props = defineProps({
  categories: Object, // Ensure categories is defined as an Array
  food: Object,
});


const cart = ref([]);

const printCart = () => {
  console.log("Cart Contents:");
  for (const id in cart.value) {
    if (cart.value.hasOwnProperty(id)) {
      console.log(`ID: ${id}, Quantity: ${cart.value[id]}`);
    }
  } 
}

const add_increase = (id) => {
  if (cart.value[id]) {
    cart.value[id]++;
    } else {
    cart.value[id] = 1;
  }
  printCart();
}

const decreaseQuantity = (id) => {
  if(cart.value[id] > 1 ){
    cart.value[id]--;
  }else if(cart.value[id] == 1){
    delete cart.value[id];
  }
  printCart();
}

const deleteAllquantity = (id) => {
  delete cart.value[id];
  printCart();
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
        <li class="active" data-filter="*">All</li>
        <li v-for="cat in props.categories" :key="cat.index" :data-filter="`.${cat}`">{{ cat }}</li>
      </ul>

      <div class="filters-content">
        <div class="row grid">
          <!-- !-- loop food start -- -->
          <div v-for="(fod , index) in food " :key="fod.id" :class="`col-sm-6 col-lg-4 all ${props.categories[fod.category_id]} `">
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
                  <div class="options">
                    {{ fod.availability }}
                  </div>
                  <div >
                    <h6>
                      {{ fod.price }} SR
                    </h6>
                    <button type="button" @click="add_increase(index)" >
                      
                    add to cart
                    </button>
                    <button type="button" @click="add_increase(index)" >
                      increase 
                    </button>
                    <button type="button" @click="deleteAllquantity(index)" >
                      delete 
                    </button>
                    <button type="button" @click="decreaseQuantity(index)" >
                      decrease
                    </button>
                    
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <!-- !-- loop food end -- -->
        </div>
      </div>
    </div>
  </section>  
  <button  type="button" class="floating-button btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Cart</button>
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Cart Contents</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
              <tr v-for="id in cart" :key="id">
                <td>{{ id }}</td>
                <td>{{ props.food[id].name }}</td>
                <td>{{ cart[id] }}</td>
                <td>{{ props.food[id].price }}</td>
                <td>{{ cart[id] * props.food[id].price }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="delete">Delete Contents</button>
          <button type="button" class="btn btn-primary">Submit</button>
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