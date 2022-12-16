module.exports = {
    extends: 'loris/es5',
    root: true,
    env: {
        browser: true
    },
    globals: {
        modules: true,
        Routing: true,
        Translator: true,
        Cookies: true,
        $: true
    },
    rules: {
        'consistent-this': 'off',
        'max-len': [2, {code: 100, ignoreUrls: true}],
        'no-invalid-this': 'off',
        'strict': 'off'
    }
};
