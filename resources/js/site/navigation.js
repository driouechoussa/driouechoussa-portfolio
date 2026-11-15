/* =========================================================
   NAVIGATION — header scroll state, sliding nav indicator,
   language dropdown, mobile menu and back-to-top button.
   ========================================================= */
window.App = window.App || {};

App.nav = (function () {
  var header, nav, indicator, menuBtn, mobileMenu, langToggle, langMenu, backTop, ring;

  /* ---------- Header + back-to-top on scroll ---------- */
  function onScroll() {
    var y = window.scrollY;
    if (header) header.classList.toggle("is-scrolled", y > 20);
    if (backTop) {
      backTop.classList.toggle("is-visible", y > 500);
      var max = document.documentElement.scrollHeight - window.innerHeight;
      var progress = max > 0 ? Math.min(y / max, 1) : 0;
      if (ring) ring.setAttribute("stroke-dashoffset", String(100 - progress * 100));
    }
  }

  /* ---------- Sliding indicator ---------- */
  function moveIndicator(link) {
    if (!indicator || !nav) return;
    if (!link) { indicator.classList.remove("is-ready"); return; }
    var navRect = nav.getBoundingClientRect();
    var r = link.getBoundingClientRect();
    var first = !indicator.classList.contains("is-ready");
    if (first) indicator.style.transition = "none"; // appear in place on load, animate afterwards
    indicator.style.width = r.width + "px";
    indicator.style.height = r.height + "px";
    indicator.style.transform = "translate(" + (r.left - navRect.left - nav.clientLeft) + "px," + (r.top - navRect.top - nav.clientTop) + "px)";
    indicator.classList.add("is-ready");
    if (first) {
      void indicator.offsetWidth; // flush styles before re-enabling transitions
      indicator.style.transition = "";
    }
  }

  function activeLink() { return nav ? nav.querySelector(".nav-link-ui.is-active") : null; }

  function refreshIndicator() { moveIndicator(activeLink()); }

  function setupIndicator() {
    if (!nav) return;
    nav.querySelectorAll(".nav-link-ui").forEach(function (link) {
      link.addEventListener("mouseenter", function () {
        moveIndicator(link);
        nav.querySelectorAll(".nav-link-ui").forEach(function (l) { l.classList.toggle("is-hovered", l === link); });
      });
    });
    nav.addEventListener("mouseleave", function () {
      nav.querySelectorAll(".nav-link-ui").forEach(function (l) { l.classList.remove("is-hovered"); });
      refreshIndicator();
    });
    // fonts change link widths: re-measure once they are ready
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(refreshIndicator);
    window.addEventListener("resize", refreshIndicator, { passive: true });
    refreshIndicator();
  }

  /* ---------- Language dropdown ---------- */
  function setLangMenu(open) {
    if (!langMenu) return;
    langMenu.classList.toggle("is-open", open);
    langToggle.setAttribute("aria-expanded", String(open));
    if (open) {
      var checked = langMenu.querySelector('[aria-checked="true"]') || langMenu.querySelector(".lang-option");
      if (checked) checked.focus();
    }
  }

  function setupLangMenu() {
    if (!langToggle) return;
    langToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      setLangMenu(!langMenu.classList.contains("is-open"));
    });
    langMenu.addEventListener("click", function (e) {
      if (e.target.closest(".lang-option")) { setLangMenu(false); langToggle.focus(); }
    });
    langMenu.addEventListener("keydown", function (e) {
      var items = Array.prototype.slice.call(langMenu.querySelectorAll(".lang-option"));
      var i = items.indexOf(document.activeElement);
      if (e.key === "ArrowDown") { e.preventDefault(); items[(i + 1) % items.length].focus(); }
      if (e.key === "ArrowUp") { e.preventDefault(); items[(i - 1 + items.length) % items.length].focus(); }
    });
    document.addEventListener("click", function (e) {
      if (!e.target.closest(".lang-switch")) setLangMenu(false);
    });
  }

  /* ---------- Mobile menu ---------- */
  function setMenu(open) {
    if (!mobileMenu) return;
    mobileMenu.classList.toggle("is-open", open);
    menuBtn.setAttribute("aria-expanded", String(open));
    document.body.classList.toggle("menu-open", open);
    if (open) mobileMenu.removeAttribute("inert");
    else mobileMenu.setAttribute("inert", "");
  }

  function setupMobileMenu() {
    if (!menuBtn) return;
    menuBtn.addEventListener("click", function () {
      setMenu(menuBtn.getAttribute("aria-expanded") !== "true");
    });
    mobileMenu.addEventListener("click", function (e) {
      if (e.target.closest("a")) setMenu(false);
    });
    window.addEventListener("resize", function () {
      if (window.innerWidth >= 992) setMenu(false);
    }, { passive: true });
  }

  function onKeydown(e) {
    if (e.key !== "Escape") return;
    if (langMenu && langMenu.classList.contains("is-open")) { setLangMenu(false); langToggle.focus(); }
    if (mobileMenu && mobileMenu.classList.contains("is-open")) { setMenu(false); menuBtn.focus(); }
  }

  function init() {
    header = document.getElementById("siteHeader");
    nav = document.querySelector(".nav-main");
    indicator = document.querySelector(".nav-indicator");
    menuBtn = document.querySelector(".menu-toggle");
    mobileMenu = document.getElementById("mobileMenu");
    langToggle = document.querySelector(".lang-toggle");
    langMenu = document.getElementById("langMenu");
    backTop = document.getElementById("backToTop");
    ring = backTop ? backTop.querySelector("circle") : null;

    setupIndicator();
    setupLangMenu();
    setupMobileMenu();
    document.addEventListener("keydown", onKeydown);

    if (backTop) {
      backTop.addEventListener("click", function () {
        var smooth = !window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        window.scrollTo({ top: 0, behavior: smooth ? "smooth" : "auto" });
      });
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  return { init: init, refresh: refreshIndicator, closeMenu: function () { setMenu(false); } };
})();
