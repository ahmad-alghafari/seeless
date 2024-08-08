<script setup>
import {onMounted, ref } from "vue";
import foodList from "./foodList.vue";
import foter from "./foter.vue";
import navBar from "./navBar.vue";

const resturant_info = ref({});
const categories = ref({});
const food = ref({});
const tableNumber = ref();

onMounted(() => {
  if (window.resturant_info) {
    resturant_info.value = window.resturant_info;
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
    <navBar :returantName="resturant_info.name" />
    <foodList  :food="food" :categories="categories"  :resturantId="resturant_info.id" :tableNumber="tableNumber" />
    <foter/>
</template>

