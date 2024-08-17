import './bootstrap';

import { createApp } from 'vue';

import dashBoard from './components_dashboard/app.vue';
import router from './router';
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";





// createApp(dashBoard).use(router).use(Toast).mount('#dashboard');

const app = createApp(dashBoard);

// Use the router
app.use(router);

// Use Toastification with optional configuration
app.use(Toast, {
  // You can customize the position, duration, etc.
  position: POSITION.BOTTOM_LEFT,
  timeout: 5000, // 5 seconds
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: "button",
  icon: true,
  rtl: false,
});

// Mount the app to the DOM element with the ID 'dashboard'
app.mount('#dashboard');


