const ComingSoon = () => import( './views/Comingsoon.vue')
const Error404 = () => import( './views/404.vue')
const LandingPage = () => import( './views/LandingPage.vue')
const PrivacyPolicyPage = () => import('./views/PrivacyPolicyPage.vue')
const TermsAndConditionsPage = () => import('./views/TermsAndConditionsPage.vue')
const SucessPaymentPage = () => import('./views/SuccessPayment.vue')
const CancelPaymentPage = () => import('./views/CancelPayment.vue')
const ProfilePage = () => import('./views/Profile.vue')

export {
	ComingSoon,
	Error404,
	LandingPage,
	PrivacyPolicyPage,
	TermsAndConditionsPage,
	SucessPaymentPage,
	CancelPaymentPage,
	ProfilePage,
}