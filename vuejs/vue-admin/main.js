import { createApp } from 'vue';
import App from './app.vue';
import vuetify from './plugins/vuetify';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes/index';

import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';

const app = createApp(App);

const router = createRouter({
	history: createWebHistory(),
	routes,
});

app.use(ToastPlugin);
app.use(router).use(vuetify).mount('#app');