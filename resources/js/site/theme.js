/* =========================================================
   THEME — dark / light switcher
   Persists in localStorage. Uses the View Transitions API for a
   circular reveal from the toggle when supported, with a CSS
   color-transition fallback.
   ========================================================= */
window.App = window.App || {};

App.theme = (function () {
  var STORAGE_KEY = "portfolio-theme";
  var root = document.documentElement;
  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  function current() { return root.getAttribute("data-theme") === "light" ? "light" : "dark"; }

  function updateToggles() {
    var isDark = current() === "dark";
    document.querySelectorAll(".theme-toggle").forEach(function (btn) {
      btn.setAttribute("aria-pressed", String(!isDark));
      btn.setAttribute("aria-label", App.lang.t(isDark ? "theme.toLight" : "theme.toDark"));
      btn.setAttribute("title", App.lang.t(isDark ? "theme.toLight" : "theme.toDark"));
    });
  }

  function commit(theme) {
    root.setAttribute("data-theme", theme);
    try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) { /* ignore */ }
    updateToggles();
  }

  function toggle(event) {
    var next = current() === "dark" ? "light" : "dark";

    if (reduceMotion.matches) { commit(next); return; }

    if (document.startViewTransition && event) {
      var x = event.clientX || window.innerWidth / 2;
      var y = event.clientY || 0;
      var radius = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y));
      var transition = document.startViewTransition(function () { commit(next); });
      transition.ready.then(function () {
        root.animate(
          { clipPath: ["circle(0px at " + x + "px " + y + "px)", "circle(" + radius + "px at " + x + "px " + y + "px)"] },
          { duration: 600, easing: "cubic-bezier(0.22, 0.61, 0.36, 1)", pseudoElement: "::view-transition-new(root)" }
        );
      }).catch(function () { /* ignore */ });
      return;
    }

    root.classList.add("theme-transition");
    commit(next);
    window.setTimeout(function () { root.classList.remove("theme-transition"); }, 500);
  }

  function init() {
    document.addEventListener("click", function (e) {
      var btn = e.target.closest(".theme-toggle");
      if (btn) toggle(e);
    });
    updateToggles();
  }

  return { init: init, toggle: toggle, refresh: updateToggles, current: current };
})();
