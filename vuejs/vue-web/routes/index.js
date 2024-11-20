import * as BaseModule from '../modules/base/router'
import * as GeneralModule from '../modules/_general/router'
import * as AuthModule from '../modules/auth/router'

import BaseLayout from '../layouts/BaseLayout.vue';

const routes = [

	{ path: '/login', name: 'login-page', component: AuthModule.Login , meta: {
		auth: false
	}},
	{ path: '/register', name: 'register-page', component: AuthModule.Register},
	{ path: '/forgot-password', name: 'forgot-password-page', component: AuthModule.ForgotPassword },
	{ path: '/password/reset/:token', name: 'reset-password', component: AuthModule.ResetPassword, meta: { auth: null, title: 'Reset Password' } },

	{
		path: '/',
		name: 'base-layout',
		component: BaseLayout,
		children: [
			{
				path: '/', name: 'home-page', component: BaseModule.HomePage,
			},
			{
				path: '/profile', name: 'profile-page', component: GeneralModule.ProfilePage,
				meta: {
					auth: true
				}
			},
		]
	},

	{
		path: '/coming-soon',
		name: 'coming-soon-page',
		component: GeneralModule.ComingSoon,
	},
	{
		path: '/:catchAll(.*)', // This is a catch-all route, it will redirect to 404 for unknown routes
		name: 'Error404',
		component: GeneralModule.Error404,
	},
];

export default routes;