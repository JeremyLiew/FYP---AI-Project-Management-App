import * as GeneralModule from '../modules/_general/router'

const routes = [
	{
		path: '/:catchAll(.*)', // This is a catch-all route, it will redirect to 404 for unknown routes
    name: 'Error404',
		component: GeneralModule.Error404,
	},
];

export default routes;