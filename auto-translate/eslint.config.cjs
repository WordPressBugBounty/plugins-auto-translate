const globals = require('globals');

module.exports = [
  {
    files: ['src/**/*.js'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'script',
      globals: {
        ...globals.browser,
        wpatLanguagesCountries: 'readonly',
        wpatBaseLanguage: 'readonly',
        wpatHostLanguage: 'readonly',
        wpatDoTranslate: 'readonly',
        wpatAutoDetect: 'readonly',
        wpatGetCurrentLang: 'readonly',
        wpatButtonIcon: 'readonly',
        wpatWidgetType: 'readonly',
        wpatWidgetMinimalistInit: 'readonly',
        languageAlreadySelected: 'readonly',
        setCookie: 'readonly',
        listenCookieChange: 'readonly',
        readCookie: 'readonly',
        jQuery: 'readonly',
      },
    },
    rules: {
      'no-unused-vars': 'off',
      'no-undef': 'error',
      'no-redeclare': 'off',
    },
  },
  {
    files: ['tests/**/*.js'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'script',
      globals: {
        ...globals.node,
      },
    },
    rules: {
      'no-unused-vars': ['error', { args: 'none' }],
      'no-undef': 'error',
      'no-redeclare': 'error',
    },
  },
];
