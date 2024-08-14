import './bootstrap';

import { createApp } from 'vue';

import dashBoard from './components_dashboard/app.vue';
import router from './router';


createApp(dashBoard).use(router).mount('#dashboard');