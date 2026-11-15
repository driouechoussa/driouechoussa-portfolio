/* =========================================================
   MAIN — boot sequence. Every module is optional on a page:
   each init() exits early when its markup isn't present.
   App.data / App.i18n / App.alternates are printed inline by the
   Laravel layout; resources/js/app.js imports the modules in order.
   ========================================================= */
(function () {
  function translateAll(instant) {
    App.components.bindProfile();
    App.lang.apply();
    App.theme.refresh();
    App.anim.observe(document, instant);
  }

  function boot() {
    App.components.render();
    App.render.init();
    App.render.all();

    App.lang.init();
    App.theme.init();
    translateAll(false);

    App.nav.init();
    App.anim.init();
    App.timeline.init();
    App.contact.init(); // exits early when the page has no contact form

    document.addEventListener("app:langchange", function () {
      App.render.all();
      translateAll(true);
      App.anim.readingProgress();
      App.nav.refresh();
      App.timeline.refresh();
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
