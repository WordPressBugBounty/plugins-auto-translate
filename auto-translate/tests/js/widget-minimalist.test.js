const test = require('node:test');
const assert = require('node:assert/strict');
const { JSDOM } = require('jsdom');

function setupDom() {
  return new JSDOM(`<!doctype html><html><body>
    <div class="auto_translate_button_wrapper" id="wrap-a">
      <div class="auto_translate_minimalist wpat_invisible">
        <div class="wpat_lang_item wpat_lang_selected"><div class="wpat_flag" data-icon-class="dashicons-admin-site-alt3"></div><div class="wpat_lang_name"></div><div class="wpat_lang_code"></div><span class="wpat_chevron"></span></div>
        <div class="wpat_minimalist_dropdown wpat_closed">
          <div class="wpat_lang_item" data-lang-code="en"></div>
          <div class="wpat_lang_item" data-lang-code="es"></div>
        </div>
      </div>
    </div>
    <div class="auto_translate_button_wrapper" id="wrap-b">
      <div class="auto_translate_minimalist wpat_invisible">
        <div class="wpat_lang_item wpat_lang_selected"><div class="wpat_flag" data-icon-class="dashicons-admin-site-alt3"></div><div class="wpat_lang_name"></div><div class="wpat_lang_code"></div><span class="wpat_chevron"></span></div>
        <div class="wpat_minimalist_dropdown wpat_closed">
          <div class="wpat_lang_item" data-lang-code="en"></div>
          <div class="wpat_lang_item" data-lang-code="es"></div>
        </div>
      </div>
    </div>
  </body></html>`);
}

function loadModule(overrides = {}) {
  delete require.cache[require.resolve('../../src/global/scripts/widget-minimalist.js')];
  Object.assign(global, overrides);
  return require('../../src/global/scripts/widget-minimalist.js');
}

test('minimalist init initializes each widget instance independently', () => {
  const dom = setupDom();
  const window = dom.window;
  const $ = require('jquery')(window);

  $.fn.slideDown = function () { this.css('display', 'block'); return this; };
  $.fn.slideUp = function () { this.css('display', 'none'); return this; };

  const mod = loadModule({
    window,
    document: window.document,
    jQuery: $,
    $,
    wpatLanguagesCountries: {
      en: { lang_name: 'English', lang_code: 'en', country_code: 'us' },
      es: { lang_name: 'Spanish', lang_code: 'es', country_code: 'es' },
    },
    wpatBaseLanguage: 'en',
    wpatHostLanguage: 'en',
    wpatAutoDetect: 'disabled',
    languageAlreadySelected: () => true,
    wpatGetCurrentLang: () => 'en',
    setCookie: () => {},
    wpatDoTranslate: () => {},
    setInterval,
    clearInterval,
  });

  mod.wpatWidgetMinimalistInit($);

  assert.equal($('#wrap-a').data('wpatInit'), true);
  assert.equal($('#wrap-b').data('wpatInit'), true);
  assert.equal($('#wrap-a .auto_translate_minimalist').hasClass('wpat_invisible'), false);
  assert.equal($('#wrap-b .auto_translate_minimalist').hasClass('wpat_invisible'), false);
});

test('minimalist sync updates selected label and hidden dropdown item', () => {
  const dom = setupDom();
  const window = dom.window;
  const $ = require('jquery')(window);

  $.fn.width = function () { return 24; };
  $.fn.slideDown = function () { return this; };
  $.fn.slideUp = function () { return this; };

  const mod = loadModule({
    window,
    document: window.document,
    jQuery: $,
    $,
    wpatLanguagesCountries: {
      en: { lang_name: 'English', lang_code: 'en', country_code: 'us' },
      es: { lang_name: 'Spanish', lang_code: 'es', country_code: 'es' },
    },
    wpatBaseLanguage: 'en',
    wpatGetCurrentLang: () => 'es',
  });

  mod.wpatWidgetMinimalistSync($);

  assert.equal($('#wrap-a .wpat_lang_selected .wpat_lang_name').text(), 'Spanish');
  assert.equal($('#wrap-a .wpat_lang_selected .wpat_lang_code').text(), 'es');
  assert.equal($('#wrap-a .wpat_lang_item[data-lang-code="es"]').hasClass('wpat_hidden'), true);
});

test('minimalist auto-detect and click handlers translate and update selection', () => {
  const dom = setupDom();
  const window = dom.window;
  const $ = require('jquery')(window);
  const translations = [];
  const cookies = [];

  $.fn.slideDown = function () { this.css('display', 'block'); return this; };
  $.fn.slideUp = function () { this.css('display', 'none'); return this; };

  const mod = loadModule({
    window,
    document: window.document,
    jQuery: $,
    $,
    wpatLanguagesCountries: {
      en: { lang_name: 'English', lang_code: 'en', country_code: 'us' },
      es: { lang_name: 'Spanish', lang_code: 'es', country_code: 'es' },
    },
    wpatBaseLanguage: 'en',
    wpatHostLanguage: 'es',
    wpatAutoDetect: 'enabled',
    languageAlreadySelected: () => false,
    wpatGetCurrentLang: () => 'en',
    setCookie: (name, value) => cookies.push([name, value]),
    wpatDoTranslate: (langPair) => translations.push(langPair),
    setInterval: (fn) => {
      fn();
      return 1;
    },
    clearInterval: () => {},
  });

  mod.wpatWidgetMinimalistInit($);
  $('#wrap-a .wpat_lang_item.wpat_lang_selected').trigger('click');
  $('#wrap-a .wpat_lang_item[data-lang-code="en"]').trigger('click');

  assert.equal(translations.includes('en|es'), true);
  assert.equal(translations.includes('en|en'), true);
  assert.deepEqual(cookies[0], ['wpatauto', '/en/en']);
  assert.equal($('#wrap-a .wpat_lang_selected .wpat_lang_name').text(), 'English');
});

test('minimalist markSelected falls back to base language for unknown codes', () => {
  const dom = setupDom();
  const window = dom.window;
  const $ = require('jquery')(window);
  const mod = loadModule({
    window,
    document: window.document,
    jQuery: $,
    $,
    wpatLanguagesCountries: {
      en: { lang_name: 'English', lang_code: 'en', country_code: 'us' },
    },
    wpatBaseLanguage: 'en',
  });

  mod.wpatWidgetMinimalistMarkSelected($, $('#wrap-a'), 'zz');

  assert.equal($('#wrap-a .wpat_lang_selected .wpat_lang_name').text(), 'English');
  assert.equal($('#wrap-a .wpat_lang_selected .wpat_flag').hasClass('us'), true);
});

test('minimalist dropdown positions upward when there is not enough space below', () => {
  const dom = setupDom();
  const window = dom.window;
  const $ = require('jquery')(window);
  const mod = loadModule({
    window,
    document: window.document,
    jQuery: $,
    $,
    wpatLanguagesCountries: {
      en: { lang_name: 'English', lang_code: 'en', country_code: 'us' },
    },
    wpatBaseLanguage: 'en',
  });

  Object.defineProperty(window, 'innerHeight', { value: 200, configurable: true });
  Object.defineProperty(window, 'innerWidth', { value: 360, configurable: true });

  const selectedEl = $('#wrap-a .wpat_lang_item.wpat_lang_selected').get(0);
  const dropdownEl = $('#wrap-a .wpat_minimalist_dropdown').get(0);
  selectedEl.getBoundingClientRect = () => ({ top: 170, bottom: 190, right: 350 });
  dropdownEl.getBoundingClientRect = () => ({ right: 370 });

  mod.wpatWidgetMinimalistPositionDropdown($, $('#wrap-a'));

  assert.equal($('#wrap-a .wpat_minimalist_dropdown').css('bottom') !== 'auto', true);
  assert.equal($('#wrap-a .wpat_minimalist_dropdown').css('right'), '0px');
});

test('minimalist resize uses intrinsic dropdown width when overlay positioning is active', () => {
  const dom = setupDom();
  const window = dom.window;
  const $ = require('jquery')(window);
  const mod = loadModule({
    window,
    document: window.document,
    jQuery: $,
    $,
    wpatLanguagesCountries: {
      en: { lang_name: 'English', lang_code: 'en', country_code: 'us' },
    },
    wpatBaseLanguage: 'en',
  });

  const selectedEl = $('#wrap-a .wpat_lang_item.wpat_lang_selected').get(0);
  const dropdownEl = $('#wrap-a .wpat_minimalist_dropdown').get(0);
  const dropdownItems = $('#wrap-a .wpat_minimalist_dropdown .wpat_lang_item').toArray();

  Object.defineProperty(selectedEl, 'scrollWidth', { value: 120, configurable: true });
  Object.defineProperty(dropdownEl, 'scrollWidth', { value: 140, configurable: true });
  Object.defineProperty(dropdownItems[0], 'scrollWidth', { value: 180, configurable: true });
  Object.defineProperty(dropdownItems[1], 'scrollWidth', { value: 220, configurable: true });

  mod.wpatWidgetMinimalistResize($, $('#wrap-a'));

  assert.equal($('#wrap-a .wpat_lang_item.wpat_lang_selected').css('width'), '220px');
  assert.equal($('#wrap-a .wpat_minimalist_dropdown').css('width'), '220px');
});
