// resources/js/store.js
import axios from 'axios';
import { createStore } from 'vuex';

const store = createStore({
  state() {
    return {
      isLoggedIn: localStorage.getItem('isLoggedIn') === 'true' || false,
      token: localStorage.getItem('token') || null,
      resturant_id: localStorage.getItem('resturant_id') ||null,
      order: localStorage.getItem('order') ||null,
      name: localStorage.getItem('name') ||null,
      saved_orders: localStorage.getItem('saved_orders') ||null,
      owner_id: localStorage.getItem('owner_id') || null,
      id: localStorage.getItem('id') || null,
    };
  },
  mutations: {
    setLogin(state, status) {
      state.isLoggedIn = status;
      localStorage.setItem('isLoggedIn', status);
    },
    setToken(state, token) {
      state.token = token;
      localStorage.setItem('token', token);
    },
    setResturantId(state, status) {
      state.resturant_id = status;
      localStorage.setItem('resturant_id', status);
    },
    
    setName(state, status) {
      state.name = status;
      localStorage.setItem('name', status);
    },
    
    setSavedOrders(state, status) {
      state.saved_orders = status;
      localStorage.setItem('saved_orders', status);
    },
    
    setOrder(state, status) {
      state.order = status;
      localStorage.setItem('order', status);
    },
    
    setServiceType(state, status) {
      state.service_type = status;
      localStorage.setItem('service_type', status);
    },

    setId(state, status) {
      state.id = status;
      localStorage.setItem('id', status);
    },

    deleteToken(state) {
      state.token = null;
      state.resturant_id = null;
      state.name = null;
      state.saved_orders = null;
      state.order = null;
      state.service_type = null;
      state.id = null;

      localStorage.removeItem('token');
      localStorage.removeItem('resturant_id');
      localStorage.removeItem('name');
      localStorage.removeItem('saved_orders');
      localStorage.removeItem('order');
      localStorage.removeItem('service_type');
      localStorage.removeItem('id');

      localStorage.setItem('isLoggedIn', 'false');
    }
  },
  actions: {
    login({ commit }, {token , resturant_id , name , saved_orders , order , service_type , id}) {
      commit('setLogin', true);
      commit('setToken', token);
      commit('setResturantId', resturant_id);
      commit('setName', name);
      commit('setSavedOrders', saved_orders);
      commit('setOrder', order);
      commit('setServiceType', service_type);
      commit('setId', id);
    },
    logout({ commit }) {
      commit('setLogin', false);
      commit('deleteToken');
    }
  }
});

export default store; 
