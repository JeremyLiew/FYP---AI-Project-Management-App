import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';

export default defineConfig({
	server: {
		watch: {
			usePolling: true,
		}
	},
	plugins: [
		laravel({
			input: [
				'resources/sass/app.scss',
				'vuejs/vue-web/main.js',
			],
			// refresh: ['**'],
			// exclude: ['storage/**'],
			// include: ['**'],
			refresh: true,
		}),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false,
				},
			},
		}),
		vuetify({ autoImport: true }),
	],
	resolve: {
		alias: {
			vue: 'vue/dist/vue.esm-bundler.js',
		},
	},
});
