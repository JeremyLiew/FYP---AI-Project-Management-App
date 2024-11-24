// vuetify.js
import 'vuetify/styles'
import { createVuetify } from 'vuetify'

export default createVuetify({
	theme: {
		defaultTheme: 'dark', // Set the default theme (optional)
		themes: {
			light: {
				primary: '#1976D2',
				secondary: '#f4f4f4',
				background: '#ffffff',
				text: '#212121',
			},
			dark: {
				primary: '#FF4081',
				secondary: '#323232',
				background: '#212121',
				text: '#EEEEEE',
			},
		},
	},
})
