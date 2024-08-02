<script setup>

import {ref} from 'vue';

const name = ref("ahmad alghafari");
const age = ref(23) ;
const foods = ref(['pizza' , 'burger']);
const bag = ref([]);
const status = ref(true);
const url = ref("https://google.com");
const newFoodOrderd = ref("");

const changeStatus = () => {
        if(status.value === true){
            status.value = false;
        }else{
            status.value = true;
        }
}

const addFood = () =>{
    if(newFoodOrderd.value.trim() !== ""){
        foods.value.push(newFoodOrderd.value);
        newFoodOrderd.value = "";

    }
}

const addToBag = (food) => {
    bag.value.push(food);
}

const deleteFood = (index) => {
    foods.value.splice(index ,1);
}
</script>
<template>
    <!-- <h1>home</h1> -->
    <!-- <nav> -->
        <!-- <router-link to="/">Home page</router-link><br>
        <router-link to="/about">about</router-link><br>
        <router-link to="/contact">contact</router-link><br>
        <hr>
        <p v-if="status">online</p>
        <br>
        <p>{{name }}</p>
        <br>-->

        
        <br>
        <!-- <a v-bind:href="url">google</a> -->
        <br>
        <!-- <button v-on:click="changeStatus">swap : {{ status }}</button>  -->
        <!-- <button @click="changeStatus">swap : {{ status }}</button>  -->
        <br>

        <form @submit.prevent="addFood">
            <label for="food"></label>
            <input type="text" id="food" v-model="newFoodOrderd" name="newFood">
            <button type="submit">submit</button>
        </form>

        <ul>
            <li v-for="(food , index) in foods" :key="food">
                <button @click="deleteFood(index)">x</button>
                {{ food }}
                <button @click="addToBag(food)">+</button>
            </li>
        </ul>
        <hr>
        <h4>my bag</h4>
        <ul>
            <li v-for="(food , index) in bag" :key="food">
                {{index+1}} : {{food}} 
            </li>
        </ul>
    <!-- </nav> -->
    <main>
        <!-- <route-view/> -->
    </main>
</template>


