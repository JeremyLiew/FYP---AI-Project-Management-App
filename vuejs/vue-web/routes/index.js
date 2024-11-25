import * as BaseModule from '../modules/base/router'
import * as GeneralModule from '../modules/_general/router'
import * as AuthModule from '../modules/auth/router'
import * as ProjectModule from '../modules/project/router'

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
				path: '/project-listings', name: 'project-listings-page', component: ProjectModule.ProjectListingsPage,
			},
			{
				path: '/project-create', name: 'project-create-page', component: ProjectModule.ProjectCreateEditPage,
				props: { isEdit: false },
			},
			{
				path: "/projects/:id/edit",
				name: "project-edit-page",
				component: ProjectModule.ProjectCreateEditPage,
				props: { isEdit: true },
			},
			{
				path: '/contact-us', name: 'contact-us-page', component: BaseModule.ContactUsPage,
			},
			{
				path: '/profile', name: 'profile-page', component: GeneralModule.ProfilePage,
				meta: {
					auth: true
				}
			},
			{
				path: '/settings', name: 'settings-page', component: GeneralModule.SettingsPage,
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