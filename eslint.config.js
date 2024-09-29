import pluginVue from 'eslint-plugin-vue'
export default [
    ...pluginVue.configs['flat/recommended'],
    {
        files: ["resources/js/**/*.*"],
        rules: {
            "no-unused-vars": "warn",
            "no-undef": "error"
        }
    }
];