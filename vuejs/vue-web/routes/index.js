import * as BaseModule from '../modules/base/router'
import * as GeneralModule from '../modules/_general/router'
import * as AuthModule from '../modules/auth/router'
import * as ProjectModule from '../modules/project/router'
import * as BudgetModule from '../modules/budget/router'
import * as ExpenseModule from '../modules/expense/router'
import * as CategoryModule from '../modules/category/router'
import * as ActivityLogModule from '../modules/activity_log/router'
import * as UserMaintenanceModule from '../modules/user_maintenance/router'
import * as ReportModule from '../modules/report/router'
import * as WeatherModule from '../modules/weather/router'

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
				meta: {
					auth: true
				}
			},
			{
				path: '/project-create', name: 'project-create-page', component: ProjectModule.ProjectCreateEditPage,
				props: { isEdit: false },
				meta: {
					auth: true
				}
			},
			{
				path: "/project/:id/edit",
				name: "project-edit-page",
				component: ProjectModule.ProjectCreateEditPage,
				props: { isEdit: true },
				meta: {
					auth: true
				}
			},
			{
				path: '/project/:id/info', name: 'project-info-page', component: ProjectModule.ProjectInfoPage,
				meta: {
					auth: true
				}
			},
			{
				path: '/budget-listings', name: 'budget-listings-page',component: BudgetModule.BudgetListingsPage,
				meta: {
					auth: true
				}
			},
			{
				path: '/budget-create', name: 'budget-create-page', component: BudgetModule.BudgetCreatePage, 
				props: { isEdit: false },
				meta: {
					auth: true
				}
			},
			{
				path: "/budgets/:id/edit", name: "budget-edit-page", component: BudgetModule.BudgetCreatePage,
				props: { isEdit: true },
				meta: {
					auth: true
				}
			},
			{
				path: '/expense-listings', name: 'expense-listings-page',  component: ExpenseModule.ExpenseListingsPage, 
				meta: {
					auth: true
				}
			},
			{
				path: '/expense-create',  name: 'expense-create-page', component: ExpenseModule.ExpenseCreateEditPage, 
				props: { isEdit: false },
				meta: {
					auth: true
				}
			},
			{
				path: "/expenses/:id/edit", name: "expense-edit-page", component: ExpenseModule.ExpenseCreateEditPage,
				props: { isEdit: true },
				meta: {
					auth: true
				}
			},
			{
				path: '/expense-category-listings', name: 'expense-category-listings-page',  component: CategoryModule.ExpenseListingsPage, 
				meta: {
					auth: true
				}
			},
			{
				path: '/expense-category-create', name: 'expense-category-create-page', component: CategoryModule.ExpenseCategoryCreateEditPage, 
				props: { isEdit: false },
				meta: {
					auth: true
				}
			},
			{
				path: "/expense-category/:id/edit", name: "expense-category-edit-page", component: CategoryModule.ExpenseCategoryCreateEditPage,
				props: { isEdit: true },
				meta: {
					auth: true
				}
			},
			{
				path: '/report', name: 'report-page', component: ReportModule.ReportListingsPage, 
				meta: {
					auth: true
				}
			},
			{
				path: '/team-report/:id', name: 'team-report-page', component: ReportModule.TeamReportsPage, 
				meta: {
					auth: true
				}
			},
			{
				path: '/weather', name: 'weather-page', component: WeatherModule.WeatherListingsPage, 
				meta: {
					auth: true
				}
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
				path: '/user-maintenance', name: 'user-maintenance-page', component: UserMaintenanceModule.UserListingsPage,
				meta: {
					auth: true
				}
			},
			{
				path: '/activity-log', name: 'activity-log-page', component: ActivityLogModule.ActivityLogPage,
				meta: {
					auth: true
				}
			},
			{
				path: '/notifications', name: 'notifications-page', component: GeneralModule.NotificationsPage,
				meta: {
					auth: true
				}
			},
			{
				path: '/settings', name: 'settings-page', component: GeneralModule.SettingsPage,
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