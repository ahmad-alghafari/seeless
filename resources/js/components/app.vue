<script setup>
import {onMounted, ref } from "vue";
import foodList from "./foodList.vue";
import foter from "./foter.vue";
import navBar from "./navBar.vue";

const resturant = ref({});
const categories = ref({});
const food = ref({});
const tableNumber = ref();

onMounted(() => {
  if (window.resturant) {
    resturant.value = window.resturant;
  }
  if (window.categories) {
    categories.value = window.categories;
  }
  if (window.food) {
    food.value = window.food;
  }
  if (window.tableNumber) {
    tableNumber.value = window.tableNumber;
  }
  
});
</script>

<template>
    <navBar :returantName="resturant.name" />
    <foodList  :food="food" :categories="categories"  :resturantId="resturant.id" :tableNumber="tableNumber" />
    <foter/>
</template>

