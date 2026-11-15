/* =========================================================
   ANIMATIONS — scroll reveals (IntersectionObserver), staggered
   groups, number counters and the article reading-progress bar.
   ========================================================= */
window.App = window.App || {};

App.anim = (function () {
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var observer = null;

  function reveal(el) {
    el.classList.add("is-visible");
    if (el.matches("[data-count]")) count(el);
    el.querySelectorAll("[data-count]").forEach(count);
  }

  /** Stagger siblings that share a parent. */
  function stagger(elements) {
    var groups = new Map();
    elements.forEach(function (el) {
      if (el.style.getPropertyValue("--reveal-delay")) return;
      var parent = el.parentElement;
      var i = groups.get(parent) || 0;
      el.style.setProperty("--reveal-delay", Math.min(i * 0.08, 0.48) + "s");
      groups.set(parent, i + 1);
    });
  }

  /**
   * Observe new .reveal elements inside `root`.
   * `instant` reveals immediately (used after a language re-render
   * so content doesn't fade in a second time).
   */
  function observe(root, instant) {
    var els = Array.prototype.slice.call((root || document).querySelectorAll(".reveal:not(.is-visible), [data-count]:not(.is-counted)"));
    if (!els.length) return;

    if (instant || reduce || !("IntersectionObserver" in window)) {
      els.forEach(function (el) { el.style.setProperty("--reveal-delay", "0s"); reveal(el); });
      return;
    }

    stagger(els.filter(function (el) { return el.classList.contains("reveal"); }));

    if (!observer) {
      observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          reveal(entry.target);
          observer.unobserve(entry.target);
        });
      }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" });
    }
    els.forEach(function (el) { observer.observe(el); });
  }

  /** Count-up for [data-count] (keeps any suffix element intact). */
  function count(el) {
    if (el.classList.contains("is-counted")) return;
    el.classList.add("is-counted");
    var target = parseFloat(el.dataset.count);
    var out = el.querySelector(".count-num") || el;
    if (reduce) { out.textContent = target; return; }
    var start = null;
    var duration = 1400;
    function step(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      out.textContent = Math.round(target * eased);
      if (p < 1) window.requestAnimationFrame(step);
    }
    window.requestAnimationFrame(step);
  }

  /** Reading progress on article pages. */
  function readingProgress() {
    var bar = document.getElementById("readingProgress");
    var article = document.getElementById("articleBody");
    if (!bar || !article) return;
    function update() {
      var rect = article.getBoundingClientRect();
      var total = rect.height - window.innerHeight * 0.6;
      var p = total > 0 ? Math.min(Math.max(-rect.top / total, 0), 1) : 1;
      bar.style.transform = "scaleX(" + p + ")";
    }
    window.addEventListener("scroll", update, { passive: true });
    update();
  }

  function init() {
    observe(document);
    readingProgress();
  }

  return { init: init, observe: observe, readingProgress: readingProgress };
})();
