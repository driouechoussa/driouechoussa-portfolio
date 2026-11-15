/* =========================================================
   COMPONENTS — shared header, mobile menu and footer.
   Injected once per page into #site-header / #site-footer so the
   markup lives in one place. Links are prefixed with App.root
   ("" on the home page, "../" inside /pages).
   ========================================================= */
window.App = window.App || {};

App.root = document.body.getAttribute("data-root") || "";
App.page = document.body.getAttribute("data-page") || "home";

App.components = (function () {
  // Wordmark. Always left-to-right, even on the Arabic site.
  var WORDMARK = '<span class="wordmark" dir="ltr" translate="no">Driouechoussa</span>';

  var NAV = [
    { key: "home", href: "" },
    { key: "about", href: "about" },
    { key: "projects", href: "projects" },
    { key: "blogs", href: "blog" },
    { key: "contact", href: "contact" }
  ];

  var SOCIALS = [
    { key: "linkedin", icon: "fa-brands fa-linkedin-in", label: "LinkedIn" },
    { key: "instagram", icon: "fa-brands fa-instagram", label: "Instagram" },
    { key: "x", icon: "fa-brands fa-x-twitter", label: "X (Twitter)" },
    { key: "github", icon: "fa-brands fa-github", label: "GitHub" }
  ];

  var LANGS = [
    { code: "en", name: "English" },
    { code: "fr", name: "Français" },
    { code: "ar", name: "العربية", cls: "lang-name-ar" }
  ];

  /** Site link relative to the current language root; absolute URLs pass through. */
  function url(path) { return /^(https?:)?\/\//.test(path) ? path : App.root + path; }

  /** Pages that belong to a nav section (details pages). */
  function activeKey() {
    var map = { "project-details": "projects", "blog-details": "blogs" };
    return map[App.page] || App.page;
  }

  /** Only the networks with a link saved in the admin panel. */
  function socialLinks(extraClass) {
    var saved = (App.data && App.data.profile && App.data.profile.socials) || {};
    return SOCIALS.filter(function (s) { return saved[s.key]; }).map(function (s) {
      return '<a class="social-link ' + (extraClass || "") + '" data-profile-href="' + s.key + '" href="#" target="_blank" rel="noopener noreferrer" aria-label="' + s.label + '">' +
        '<i class="' + s.icon + '" aria-hidden="true"></i></a>';
    }).join("");
  }

  function langOptions(role) {
    return LANGS.map(function (l) {
      return '<button type="button" class="lang-option" role="' + role + '" data-lang="' + l.code + '" aria-checked="false" lang="' + l.code + '">' +
        '<span class="' + (l.cls || "") + '">' + l.name + "</span></button>";
    }).join("");
  }

  function header() {
    var active = activeKey();
    var links = NAV.map(function (n) {
      var isActive = n.key === active;
      return '<li><a class="nav-link-ui' + (isActive ? " is-active" : "") + '" href="' + url(n.href) + '"' +
        (isActive ? ' aria-current="page"' : "") + ' data-nav="' + n.key + '" data-i18n="nav.' + n.key + '">' + n.key + "</a></li>";
    }).join("");

    var mobileLinks = NAV.map(function (n, i) {
      var isActive = n.key === active;
      return '<li><a class="mobile-nav-link' + (isActive ? " is-active" : "") + '" href="' + url(n.href) + '"' +
        (isActive ? ' aria-current="page"' : "") + '><span data-i18n="nav.' + n.key + '">' + n.key + '</span><span class="num">0' + (i + 1) + "</span></a></li>";
    }).join("");

    return '' +
      '<header class="site-header" id="siteHeader">' +
        '<div class="container header-inner">' +
          '<a class="logo" href="' + url("") + '" data-i18n-aria="nav.logoAria">' +
            WORDMARK +
          "</a>" +
          '<nav class="nav-main" data-i18n-aria="nav.aria">' +
            '<span class="nav-indicator" aria-hidden="true"></span>' +
            '<ul class="nav-links">' + links + "</ul>" +
          "</nav>" +
          '<div class="header-actions">' +
            '<div class="lang-switch">' +
              '<button type="button" class="lang-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="langMenu" data-i18n-aria="nav.language">' +
                '<span class="lang-current">English</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>' +
              "</button>" +
              '<div class="lang-menu" id="langMenu" role="menu">' + langOptions("menuitemradio") + "</div>" +
            "</div>" +
            '<button type="button" class="icon-btn theme-toggle" aria-pressed="false">' +
              '<i class="fa-solid fa-sun" aria-hidden="true"></i><i class="fa-solid fa-moon" aria-hidden="true"></i>' +
            "</button>" +
            '<a class="btn-ui btn-ui--primary btn-ui--sm btn-hire" href="' + url("contact") + '" data-i18n-aria="nav.hire">' +
              '<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span data-i18n="nav.hire">Hire Me</span>' +
            "</a>" +
            '<button type="button" class="icon-btn menu-toggle" aria-expanded="false" aria-controls="mobileMenu" data-i18n-aria="nav.menu">' +
              '<span class="burger" aria-hidden="true"><span></span><span></span><span></span></span>' +
            "</button>" +
          "</div>" +
        "</div>" +
      "</header>" +
      '<div class="mobile-menu" id="mobileMenu" inert>' +
        '<nav data-i18n-aria="nav.aria"><ul class="mobile-nav-list">' + mobileLinks + "</ul></nav>" +
        '<div class="mobile-menu-footer">' +
          '<div class="mobile-lang" role="group" data-i18n-aria="nav.language">' + langOptions("radio") + "</div>" +
          '<a class="btn-ui btn-ui--primary btn-ui--lg" href="' + url("contact") + '"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span data-i18n="nav.hire">Hire Me</span></a>' +
          '<div class="socials">' + socialLinks() + "</div>" +
        "</div>" +
      "</div>";
  }

  function footer() {
    var quick = NAV.map(function (n) {
      return '<li><a href="' + url(n.href) + '" data-i18n="nav.' + n.key + '">' + n.key + "</a></li>";
    }).join("");

    return '' +
      '<footer class="site-footer">' +
        '<div class="container">' +
          '<div class="footer-grid">' +
            '<div class="footer-about">' +
              '<a class="logo" href="' + url("") + '" data-i18n-aria="nav.logoAria">' +
                WORDMARK +
              "</a>" +
              '<p data-i18n="footer.desc"></p>' +
              '<div class="socials">' + socialLinks() + "</div>" +
            "</div>" +
            "<div>" +
              '<h2 class="footer-title" data-i18n="footer.quick"></h2>' +
              '<ul class="footer-links">' + quick + "</ul>" +
            "</div>" +
            "<div>" +
              '<h2 class="footer-title" data-i18n="footer.contactTitle"></h2>' +
              '<ul class="footer-contact">' +
                '<li><i class="fa-solid fa-envelope" aria-hidden="true"></i><a data-profile-href="email" data-profile="email" href="#"></a></li>' +
                (App.data && App.data.profile && App.data.profile.phone
                  ? '<li><i class="fa-solid fa-phone" aria-hidden="true"></i><a data-profile-href="phone" data-profile="phone" href="#" dir="ltr"></a></li>'
                  : "") +
                '<li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span data-profile="location"></span></li>' +
              "</ul>" +
            "</div>" +
          "</div>" +
          '<div class="footer-bottom">' +
            '<p>&copy; <span class="year"></span> <span data-profile="name"></span>. <span data-i18n="footer.rights"></span></p>' +
            '<p><a href="' + url("privacy") + '" data-i18n="footer.privacy"></a> · <span data-i18n="footer.made"></span> <i class="fa-solid fa-heart heart" aria-hidden="true"></i></p>' +
          "</div>" +
        "</div>" +
      "</footer>" +
      '<button type="button" class="back-to-top" id="backToTop" data-i18n-aria="nav.backToTop">' +
        '<svg viewBox="0 0 58 58" aria-hidden="true"><circle cx="29" cy="29" r="27" pathLength="100" stroke-dasharray="100" stroke-dashoffset="100"></circle></svg>' +
        '<i class="fa-solid fa-arrow-up" aria-hidden="true"></i>' +
      "</button>" +
      '<div class="toast-ui" id="toast" role="status" aria-live="polite"><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span class="toast-msg"></span></div>';
  }

  /** Fill [data-profile], [data-profile-href] and [data-profile-src] bindings. */
  function bindProfile(scope) {
    var p = App.data.profile;
    var pick = App.lang.pick;
    var root = scope || document;
    var values = {
      name: pick(p.name),
      firstName: pick(p.name).split(" ")[0],
      initials: p.initials,
      handle: p.handle,
      email: p.email,
      phone: p.phone,
      location: pick(p.location)
    };
    var hrefs = {
      email: "mailto:" + p.email,
      phone: "tel:" + (p.phone || "").replace(/\s+/g, ""),
      cv: url(p.cv)
    };
    Object.keys(p.socials || {}).forEach(function (k) { hrefs[k] = p.socials[k]; });

    root.querySelectorAll("[data-profile]").forEach(function (el) {
      var v = values[el.dataset.profile];
      if (v !== undefined) el.textContent = v;
    });
    root.querySelectorAll("[data-profile-href]").forEach(function (el) {
      var v = hrefs[el.dataset.profileHref];
      if (v !== undefined) el.setAttribute("href", v);
    });
    root.querySelectorAll("[data-profile-src]").forEach(function (el) {
      if (el.dataset.profileSrc === "photo") el.setAttribute("src", url(p.photo));
    });
  }

  function render() {
    var h = document.getElementById("site-header");
    var f = document.getElementById("site-footer");
    if (h) h.outerHTML = header();
    if (f) f.outerHTML = footer();
    document.querySelectorAll(".year").forEach(function (el) { el.textContent = new Date().getFullYear(); });
  }

  /** Show a short status message. */
  var toastTimer;
  function toast(message) {
    var el = document.getElementById("toast");
    if (!el) return;
    el.querySelector(".toast-msg").textContent = message;
    el.classList.add("is-visible");
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () { el.classList.remove("is-visible"); }, 3500);
  }

  return { render: render, bindProfile: bindProfile, toast: toast, url: url, socialLinks: socialLinks };
})();
