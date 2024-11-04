// resources/js/app.js
import './bootstrap';
import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import EN from './locale/en.json';
import AR from './locale/ar.json';
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";
import dashBoard from './components/dashboard/app.vue';
import router from './router'; // Import the router
import store from './store'; // Import the store
import Cookies from 'js-cookie'


// Set up i18n
const i18n = createI18n({
  legacy: false,
  locale: Cookies.get('locale') || 'EN',
  fallbackLocale: 'EN', 
  messages: {
    EN: EN,
    AR: AR
  }
});

// Create the app and use the imported components and plugins
const app = createApp(dashBoard);
app.use(router); // Use the router
app.use(store);  // Use the store
app.use(Toast, {
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

// Mount the app
app.use(i18n);
app.mount('#dashboard');
