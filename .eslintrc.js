module.exports = {
	env: {
		node: true,
	},
	extends: [
		"eslint:recommended",
		'plugin:vue/vue3-recommended',
	],
	rules: {
		"indent": ["error", "tab"], // must use tab
		"no-unused-vars": ["error", { "vars": "all", "args": "none", "ignoreRestSiblings": false }],
		"vue/html-indent": ["error", "tab"],
		"html-self-closing": 'off',
		"vue/no-v-html": 'off',
		"vue/max-attributes-per-line": ["error", {
			"singleline": {
				"max" : 3,
			},
			"multiline": {
				"max": 3,
			}
		}],
		"vue/multi-word-component-names": 'off',
		"vue/html-self-closing": 'off',
		"vue/singleline-html-element-content-newline": 'off',
		"no-debugger" : 'off',
		"@typescript-eslint/no-unused-vars" : "off",
		"@typescript-eslint/no-empty-function" : "off",
	}
}