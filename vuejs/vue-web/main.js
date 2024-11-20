import './plugins/bootstrap'
import { createApp } from 'vue';
import App from './app.vue';
import vuetify from './plugins/Vuetify';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes/index';
import '@mdi/font/css/materialdesignicons.css'
import { createPinia } from 'pinia'
import axios                 from 'axios';

import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';

import {createAuth}          from '@websanova/vue-auth';
import driverAuthBearer      from '@websanova/vue-auth/dist/drivers/auth/bearer.esm.js';
import driverHttpAxios       from '@websanova/vue-auth/dist/drivers/http/axios.1.x.esm.js';
import driverRouterVueRouter from '@websanova/vue-auth/dist/drivers/router/vue-router.2.x.esm.js';
import driverOAuth2Google    from '@websanova/vue-auth/dist/drivers/oauth2/google.esm.js';
import driverOAuth2Facebook  from '@websanova/vue-auth/dist/drivers/oauth2/facebook.esm.js';


const pinia = createPinia();
const app = createApp(App);

const router = createRouter({
	history: createWebHistory(),
	routes,
});

var auth = createAuth({
	plugins: {
		http: axios,
		router: router
	},
	drivers: {
		http: driverHttpAxios,
		auth: driverAuthBearer,
		router: driverRouterVueRouter,
		oauth2: {
			google: driverOAuth2Google,
			facebook: driverOAuth2Facebook,
		}
	},
	options: {
		rolesKey: 'type',
		notFoundRedirect: {name: 'home-page'},
		logoutData: { url: 'auth/logout', method: 'DELETE', redirect: '/', makeRequest: true },
		fetchData: {url: 'auth/user', method: 'GET'},
	},
})

app.use(auth);
app.use(pinia);
app.use(ToastPlugin);
app.use(router).use(vuetify).mount('#app');
