<script setup>
import {onMounted, ref , defineProps} from "vue";
import navBar from "./navBar.vue";
import foodList from "./foodList.vue";
import foter from "./foter.vue";

const resturant_info = ref({});

onMounted(() => {
  if (window.resturant_info) {
    resturant_info.value = window.resturant_info;
  }
});
</script>

<template>
    <navBar :returantName="resturant_info.name" :id="resturant_info.id"/>
    <foodList :id="resturant_info.id"/>
    <foter/>
</template>