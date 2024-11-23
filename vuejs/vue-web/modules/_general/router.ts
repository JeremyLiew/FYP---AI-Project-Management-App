const Error404 = () => import( './views/404.vue')
const LandingPage = () => import( './views/LandingPage.vue')
const PrivacyPolicyPage = () => import('./views/PrivacyPolicyPage.vue')
const TermsAndConditionsPage = () => import('./views/TermsAndConditionsPage.vue')
const ProfilePage = () => import('./views/Profile.vue')

export {
	Error404,
	LandingPage,
	PrivacyPolicyPage,
	TermsAndConditionsPage,
	ProfilePage,
}