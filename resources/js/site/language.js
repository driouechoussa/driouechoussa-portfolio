/* =========================================================
   LANGUAGE — i18n engine (EN / FR / AR + RTL)
   Markup hooks:
     data-i18n="key"              → textContent
     data-i18n-html="key"         → innerHTML (trusted strings only)
     data-i18n-placeholder="key"  → placeholder
     data-i18n-aria="key"         → aria-label
     data-i18n-title="key"        → title attribute
     data-i18n-alt="key"          → alt attribute
   ========================================================= */
window.App = window.App || {};

App.lang = (function () {
  var STORAGE_KEY = "portfolio-lang";
  var SUPPORTED = ["en", "fr", "ar"];
  var LOCALES = { en: "en-GB", fr: "fr-FR", ar: "ar-MA-u-nu-latn" };
  var NAMES = { en: "English", fr: "Français", ar: "العربية" };
  var current = document.documentElement.getAttribute("lang") || "en";

  function dict(lang) { return (App.i18n && App.i18n[lang]) || {}; }

  /** Translate a UI key (falls back to English, then to the key). */
  function t(key) {
    var value = dict(current)[key];
    if (value === undefined) value = dict("en")[key];
    return value === undefined ? key : value;
  }

  /** Pick the current language from a localized { en, fr, ar } object. */
  function pick(obj) {
    if (obj === null || obj === undefined) return "";
    if (typeof obj !== "object" || Array.isArray(obj)) return obj;
    return obj[current] !== undefined ? obj[current] : obj.en;
  }

  function formatDate(iso, opts) {
    try {
      return new Intl.DateTimeFormat(LOCALES[current], opts || { year: "numeric", month: "short", day: "numeric" })
        .format(new Date(iso + "T00:00:00"));
    } catch (e) { return iso; }
  }

  function apply(root) {
    var scope = root || document;
    scope.querySelectorAll("[data-i18n]").forEach(function (el) { el.textContent = t(el.dataset.i18n); });
    scope.querySelectorAll("[data-i18n-html]").forEach(function (el) { el.innerHTML = t(el.dataset.i18nHtml); });
    scope.querySelectorAll("[data-i18n-placeholder]").forEach(function (el) { el.setAttribute("placeholder", t(el.dataset.i18nPlaceholder)); });
    scope.querySelectorAll("[data-i18n-aria]").forEach(function (el) { el.setAttribute("aria-label", t(el.dataset.i18nAria)); });
    scope.querySelectorAll("[data-i18n-title]").forEach(function (el) { el.setAttribute("title", t(el.dataset.i18nTitle)); });
    scope.querySelectorAll("[data-i18n-alt]").forEach(function (el) { el.setAttribute("alt", t(el.dataset.i18nAlt)); });
    syncSwitchers();
  }

  function syncSwitchers() {
    document.querySelectorAll(".lang-current").forEach(function (el) { el.textContent = NAMES[current]; });
    document.querySelectorAll(".lang-option").forEach(function (btn) {
      btn.setAttribute("aria-checked", String(btn.dataset.lang === current));
    });
  }

  function set(lang) {
    if (SUPPORTED.indexOf(lang) === -1 || lang === current) return;
    try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) { /* ignore */ }

    // Each language has its own URL (/fr/…, /ar/…): go to this page in that language.
    var target = App.alternates && App.alternates[lang];
    if (target) { window.location.href = target + window.location.hash; return; }

    current = lang;

    var html = document.documentElement;
    html.setAttribute("lang", lang);
    html.setAttribute("dir", lang === "ar" ? "rtl" : "ltr");

    document.dispatchEvent(new CustomEvent("app:langchange", { detail: { lang: lang } }));
  }

  function init() {
    document.addEventListener("click", function (e) {
      var option = e.target.closest(".lang-option");
      if (option) set(option.dataset.lang);
    });
  }

  return {
    t: t,
    pick: pick,
    apply: apply,
    set: set,
    init: init,
    formatDate: formatDate,
    get current() { return current; }
  };
})();
