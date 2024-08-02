<script>
export default {
data(){
    return{
        name : "ahmad alghafari",
        age : 23,
        foods : ['pizza' , 'burger' ],
        status :true,
        url : "https://google.com",
    }
},
methods:{
    changeStatus(){
        if(this.status === true){
            this.status = false;
        }else{
            this.status = true;
        }
    }
}
};
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

        <ul> 
            <li v-for="food in foods">{{ food }}</li>
        </ul> 
        <br>
        <a v-bind:href="url">google</a>
        <br>
        <!-- <button v-on:click="changeStatus">swap : {{ status }}</button>  -->
        <button @click="changeStatus">swap : {{ status }}</button> 

        <br>
    <!-- </nav> -->
    <main>
        <!-- <route-view/> -->
    </main>
</template>


