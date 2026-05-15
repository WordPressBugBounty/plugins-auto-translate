const test = require('node:test');
const assert = require('node:assert/strict');

function loadModule(overrides = {}) {
  delete require.cache[require.resolve('../../src/public/scripts/script.js')];

  global.window = {
    console: { warn: () => {} },
    addEventListener: () => {},
    setTimeout: (fn) => fn(),
    performance: { getEntriesByType: () => [] },
  };
  global.document = {
    getElementsByTagName: () => [],
    getElementsByClassName: () => [{ innerHTML: '' }],
    createEventObject: null,
    createEvent: () => ({ initEvent: () => {} }),
    cookie: '',
  };
  global.listenCookieChange = () => {};
  global.readCookie = () => null;
  global.wpatWidgetType = 'classic';
  global.wpatBaseLanguage = 'en';
  global.setInterval = () => 1;
  global.clearInterval = () => {};
  global.setTimeout = (fn) => fn();
  global.jQuery = function () {
    return {
      ready(fn) {
        fn(() => ({
          length: 0,
          addClass: () => {},
          contents: () => ({ find: () => ({ click: () => {} }) }),
        }));
      },
    };
  };

  Object.assign(global, overrides);

  return require('../../src/public/scripts/script.js');
}

test('wpatDoTranslate retries are bounded when combo is missing', () => {
  let warnCalls = 0;
  const mod = loadModule({
    window: {
      console: { warn: () => { warnCalls++; } },
      addEventListener: () => {},
      setTimeout: (fn) => fn(),
      performance: { getEntriesByType: () => [] },
    },
  });

  mod.wpatDoTranslate('en|es');

  assert.equal(warnCalls, 1);
  assert.equal(mod.wpatDoTranslate.attempts, 0);
});

test('wpatDoTranslate sets combo and dispatches events when ready', () => {
  const combo = {
    className: 'goog-te-combo',
    options: [{}],
    innerHTML: '<option>es</option>',
    dispatchEvent: () => {},
  };

  const mod = loadModule({
    document: {
      getElementsByTagName: () => [combo],
      getElementsByClassName: () => [{ innerHTML: '<div>ready</div>' }],
      createEventObject: null,
      createEvent: () => ({ initEvent: () => {} }),
      cookie: '',
    },
  });

  mod.wpatDoTranslate('en|es');
  assert.equal(combo.value, 'es');
  assert.equal(mod.wpatDoTranslate.attempts, 0);
});

test('wpatRestoreTranslationState syncs widget and reapplies non-base translation', () => {
  let synced = 0;
  const combo = {
    className: 'goog-te-combo',
    options: [{}],
    innerHTML: '<option>fr</option>',
    dispatchEvent: () => {},
  };
  const mod = loadModule({
    wpatWidgetType: 'minimalist',
    wpatWidgetMinimalistInit: () => {},
    wpatWidgetMinimalistSync: () => { synced++; },
    document: {
      getElementsByTagName: () => [combo],
      getElementsByClassName: () => [{ innerHTML: '<div>ready</div>' }],
      createEventObject: null,
      createEvent: () => ({ initEvent: () => {} }),
      cookie: '',
    },
  });

  global.document.cookie = 'googtrans=/en/fr';
  mod.wpatRestoreTranslationState(global.jQuery);

  assert.equal(synced, 1);
  assert.equal(combo.value, 'fr');
});

test('pageshow history navigation restores translation state', () => {
  let handler = null;
  const combo = {
    className: 'goog-te-combo',
    options: [{}],
    innerHTML: '<option>fr</option>',
    dispatchEvent: () => {},
  };
  loadModule({
    window: {
      console: { warn: () => {} },
      addEventListener: (eventName, fn) => {
        if (eventName === 'pageshow') {
          handler = fn;
        }
      },
      setTimeout: (fn) => fn(),
      performance: { getEntriesByType: () => [{ type: 'back_forward' }] },
      jQuery: { mocked: true },
    },
    document: {
      getElementsByTagName: () => [combo],
      getElementsByClassName: () => [{ innerHTML: '<div>ready</div>' }],
      createEventObject: null,
      createEvent: () => ({ initEvent: () => {} }),
      cookie: 'googtrans=/en/fr',
    },
  });

  handler({ persisted: false });
  assert.equal(combo.value, 'fr');
});

test('translateFireEvent supports legacy IE event dispatch and setCookie writes path', () => {
  const fired = [];
  const mod = loadModule({
    document: {
      getElementsByTagName: () => [],
      getElementsByClassName: () => [{ innerHTML: '' }],
      createEventObject: () => ({ legacy: true }),
      createEvent: () => ({ initEvent: () => {} }),
      cookie: '',
    },
  });

  mod.translateFireEvent({
    fireEvent: (name) => fired.push(name),
  }, 'change');
  mod.setCookie('demo', 'value');

  assert.deepEqual(fired, ['onchange']);
  assert.equal(global.document.cookie, 'demo=value;;path=/');
});

test('languageAlreadySelected uses googtrans or wpatauto cookies', () => {
  const mod = loadModule();

  global.document.cookie = 'googtrans=/en/es';
  assert.equal(mod.languageAlreadySelected(), true);

  global.document.cookie = 'wpatauto=/en/en';
  assert.equal(mod.languageAlreadySelected(), true);

  global.document.cookie = '';
  assert.equal(mod.languageAlreadySelected(), false);
});

test('wpatDetectHostLanguage prefers navigator languages and falls back to base', () => {
  const mod = loadModule({
    window: {
      console: { warn: () => {} },
      addEventListener: () => {},
      setTimeout: (fn) => fn(),
      performance: { getEntriesByType: () => [] },
      navigator: { languages: ['fr-FR', 'es-ES'], language: 'de-DE' },
    },
    wpatLanguagesCountries: {
      en: { lang_name: 'English' },
      es: { lang_name: 'Spanish' },
    },
  });

  assert.equal(mod.wpatDetectHostLanguage(), 'es');
});

test('wpatDetectHostLanguage uses base language when no supported locale matches', () => {
  const mod = loadModule({
    window: {
      console: { warn: () => {} },
      addEventListener: () => {},
      setTimeout: (fn) => fn(),
      performance: { getEntriesByType: () => [] },
      navigator: { languages: ['fr-FR'], language: 'de-DE' },
    },
    wpatLanguagesCountries: {
      en: { lang_name: 'English' },
      es: { lang_name: 'Spanish' },
    },
    wpatBaseLanguage: 'en',
  });

  assert.equal(mod.wpatDetectHostLanguage(), 'en');
});

test('wpatInitMinimalistWithRetry initializes minimalist widget when available', () => {
  let calls = 0;
  const mod = loadModule({
    wpatWidgetType: 'minimalist',
    wpatWidgetMinimalistInit: () => { calls++; },
  });

  mod.wpatInitMinimalistWithRetry(global.jQuery);
  assert.equal(calls >= 1, true);
  assert.equal(mod.wpatShouldInitMinimalist(), true);
});

test('wpatInitMinimalistWithRetry retries and warns when init keeps throwing', () => {
  let warnCalls = 0;
  let retries = 0;
  const mod = loadModule({
    window: {
      console: { warn: () => { warnCalls++; } },
      addEventListener: () => {},
      setTimeout: (fn) => fn(),
      performance: { getEntriesByType: () => [] },
    },
    setTimeout: (fn) => {
      retries++;
      fn();
    },
    wpatWidgetType: 'minimalist',
    wpatWidgetMinimalistInit: () => { throw new Error('fail'); },
  });

  mod.wpatInitMinimalistWithRetry(global.jQuery);
  assert.equal(retries >= 8, true);
  assert.equal(warnCalls >= 1, true);
});
