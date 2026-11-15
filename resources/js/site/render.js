/* =========================================================
   RENDER — builds data-driven sections from App.data.
   Any element with data-render="<type>" is filled here, and
   re-rendered when the language changes.
     projects · education · experience · posts
     skills · tools · project-detail · post-detail
   Options: data-limit, data-exclude-current
   ========================================================= */
window.App = window.App || {};

App.render = (function () {
  var t = function (k) { return App.lang.t(k); };
  var pick = function (o) { return App.lang.pick(o); };
  var url = function (p) { return App.components.url(p); };

  /* Filter/search state survives language re-renders */
  var state = { projects: "all", posts: "all", search: "" };

  function esc(str) {
    return String(str).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  function param(name) { return new URLSearchParams(window.location.search).get(name); }

  function period(start, end) {
    return start === end ? start : start + " — " + (end || t("common.present"));
  }

  /* ---------- Mini browser mockups (project previews) ---------- */
  function mock(variant) {
    var bar = '<div class="mock-bar"><i></i><i></i><i></i></div>';
    var inner;
    switch (variant) {
      case "dash":
        inner = '<div class="mock-content"><div class="mock-side"><div class="mock-line accent"></div><div class="mock-line"></div><div class="mock-line"></div><div class="mock-line w-60"></div></div>' +
          '<div><div class="mock-line w-40"></div><div class="mock-chart"><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div><div class="mock-line w-80 mt-mock"></div></div></div>';
        break;
      case "code":
        inner = '<div class="mock-content"><div class="mock-line accent w-40"></div><div class="mock-line indent w-60"></div><div class="mock-line indent w-80"></div><div class="mock-line indent accent w-40"></div><div class="mock-line w-60"></div><div class="mock-line indent w-80"></div><div class="mock-line accent w-40"></div></div>';
        break;
      case "landing":
        inner = '<div class="mock-content"><div class="mock-hero"></div><div class="mock-line w-80"></div><div class="mock-line w-60"></div><div class="mock-row"><div class="mock-tile"></div><div class="mock-tile accent"></div><div class="mock-tile"></div></div></div>';
        break;
      default: /* shop */
        inner = '<div class="mock-content"><div class="mock-line accent w-40"></div><div class="mock-row"><div class="mock-tile accent"></div><div class="mock-tile"></div><div class="mock-tile"></div></div><div class="mock-row"><div class="mock-tile"></div><div class="mock-tile"></div><div class="mock-tile accent"></div></div></div>';
    }
    return '<div class="mock mock--' + variant + '" aria-hidden="true">' + bar + inner + "</div>";
  }

  function preview(p, extraClass, variant, alt) {
    if (p.image && !variant) {
      return '<div class="project-preview has-image ' + (extraClass || "") + '" style="--accent:' + p.accent + '">' +
        '<img src="' + esc(p.image) + '" alt="' + esc(alt || "") + '" loading="lazy" decoding="async"></div>';
    }
    return '<div class="project-preview ' + (extraClass || "") + '" style="--accent:' + p.accent + '">' +
      '<span class="preview-glyph" aria-hidden="true"><i class="fa-solid ' + p.icon + '"></i></span>' +
      mock(variant || p.preview) + "</div>";
  }

  /* ---------- Card templates ---------- */
  function projectCard(p) {
    var cat = t("cat." + p.category);
    return '<div class="col-md-6 col-lg-4 reveal" data-category="' + p.category + '">' +
      '<a class="card-ui project-card" href="' + url("projects/" + p.id) + '" aria-label="' + esc(p.title + " — " + cat) + '">' +
        preview(p) +
        '<div class="project-body">' +
          '<div><span class="tag">' + esc(cat) + "</span></div>" +
          '<h3 class="project-title">' + esc(p.title) + "</h3>" +
          '<p class="project-desc">' + esc(pick(p.desc)) + "</p>" +
          '<div class="project-stack" dir="ltr">' + p.stack.slice(0, 3).map(function (s) { return "<span>" + esc(s) + "</span>"; }).join("") + "</div>" +
        "</div>" +
        '<span class="card-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span>' +
      "</a></div>";
  }

  function educationItem(e) {
    return '<li class="edu-item reveal">' +
      '<article class="card-ui edu-card">' +
        '<div class="edu-head"><div>' +
          '<h3 class="edu-degree">' + esc(pick(e.degree)) + "</h3>" +
          '<p class="edu-field">' + esc(pick(e.field)) + "</p></div>" +
          '<span class="tag"><i class="fa-regular fa-calendar" aria-hidden="true"></i>' + esc(period(e.start, e.end)) + "</span>" +
        "</div>" +
        '<p class="edu-school"><i class="fa-solid fa-building-columns" aria-hidden="true"></i>' + esc(pick(e.school)) + "</p>" +
        '<p class="edu-desc">' + esc(pick(e.desc)) + "</p>" +
      "</article></li>";
  }

  function experienceItem(x) {
    var current = x.end ? "" : '<span class="tag xp-current">' + esc(t("common.current")) + "</span>";
    return '<div class="xp-item reveal">' +
      '<span class="xp-dot" aria-hidden="true"></span>' +
      '<span class="xp-period">' + esc(period(x.start, x.end)) + "</span>" +
      '<article class="card-ui xp-card">' +
        '<h3 class="xp-role">' + esc(pick(x.role)) + "</h3>" +
        '<p class="xp-company">' + esc(x.company === "Self-employed" || x.company === "Freelance" ? t("xp.company." + x.company) : x.company) + current + "</p>" +
        '<div class="xp-meta"><span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>' + esc(pick(x.location)) + "</span>" +
          '<span><i class="fa-regular fa-calendar" aria-hidden="true"></i>' + esc(period(x.start, x.end)) + "</span></div>" +
        '<p class="xp-desc">' + esc(pick(x.desc)) + "</p>" +
      "</article></div>";
  }

  function postCard(b) {
    var title = pick(b.title);
    return '<div class="col-md-6 col-lg-4 reveal" data-category="' + b.category + '">' +
      '<a class="card-ui blog-card" href="' + url("blog/" + b.id) + '">' +
        '<div class="blog-thumb"><img src="' + url(b.image) + '" alt="' + esc(title) + '" width="800" height="500" loading="lazy" decoding="async">' +
          '<span class="tag">' + esc(t("blogcat." + b.category)) + "</span></div>" +
        '<div class="blog-body">' +
          '<div class="blog-meta"><span><i class="fa-regular fa-calendar" aria-hidden="true"></i><time datetime="' + b.date + '">' + esc(App.lang.formatDate(b.date)) + "</time></span>" +
            '<span><i class="fa-regular fa-clock" aria-hidden="true"></i>' + b.readTime + " " + esc(t("common.minRead")) + "</span></div>" +
          '<h3 class="blog-title">' + esc(title) + "</h3>" +
          '<p class="blog-excerpt">' + esc(pick(b.excerpt)) + "</p>" +
          '<span class="link-arrow">' + esc(t("common.readMore")) + ' <i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></span>' +
        "</div>" +
      "</a></div>";
  }

  function skillGroup(g) {
    return '<div class="col-md-6 col-lg-4 reveal"><div class="card-ui skill-group">' +
      '<h3><i class="fa-solid ' + g.icon + '" aria-hidden="true"></i>' + esc(t("about.skills." + g.group)) + "</h3>" +
      g.items.map(function (s) {
        return '<div class="skill"><div class="skill-top"><span dir="ltr">' + esc(s[0]) + "</span><span>" + s[1] + "%</span></div>" +
          '<div class="skill-bar" role="progressbar" aria-label="' + esc(s[0]) + '" aria-valuenow="' + s[1] + '" aria-valuemin="0" aria-valuemax="100"><span style="--level:' + s[1] + '%"></span></div></div>';
      }).join("") + "</div></div>";
  }

  function tool(x) {
    return '<div class="tool reveal"><i class="' + x.icon + '" aria-hidden="true"></i><span>' + esc(x.name) + "</span></div>";
  }

  /* ---------- Lists with optional filtering ---------- */
  function applyFilter(type) {
    var grid = document.querySelector('[data-render="' + type + '"]');
    if (!grid) return;
    var cat = state[type];
    var q = type === "posts" ? state.search.trim().toLowerCase() : "";
    var visible = 0;
    Array.prototype.forEach.call(grid.children, function (col) {
      var matchCat = cat === "all" || col.dataset.category === cat;
      var matchQ = !q || col.textContent.toLowerCase().indexOf(q) !== -1;
      var show = matchCat && matchQ;
      col.classList.toggle("is-hidden", !show);
      if (show) { visible++; col.classList.add("is-visible"); }
    });
    var empty = document.querySelector('[data-empty-for="' + type + '"]');
    if (empty) empty.classList.toggle("is-hidden", visible > 0);
  }

  function renderChips(type, categories, prefix) {
    var bar = document.querySelector('[data-filter-for="' + type + '"]');
    if (!bar) return;
    var cats = ["all"].concat(categories);
    bar.innerHTML = cats.map(function (c) {
      var label = c === "all" ? t("common.all") : t(prefix + c);
      return '<button type="button" class="chip" data-filter="' + c + '" aria-pressed="' + (state[type] === c) + '">' + esc(label) + "</button>";
    }).join("");
  }

  function unique(list, key) {
    return list.map(function (x) { return x[key]; }).filter(function (v, i, a) { return a.indexOf(v) === i; });
  }

  function setupFilters() {
    document.addEventListener("click", function (e) {
      var chip = e.target.closest("[data-filter]");
      if (!chip) return;
      var bar = chip.closest("[data-filter-for]");
      var type = bar.dataset.filterFor;
      state[type] = chip.dataset.filter;
      bar.querySelectorAll(".chip").forEach(function (c) { c.setAttribute("aria-pressed", String(c === chip)); });
      applyFilter(type);
    });
    var search = document.querySelector('[data-search-for="posts"]');
    if (search) {
      search.addEventListener("input", function () { state.search = search.value; applyFilter("posts"); });
    }
  }

  /* ---------- Detail pages ---------- */
  function notFound(type) {
    var back = type === "project" ? "projects" : "blog";
    return '<div class="container not-found"><i class="fa-solid fa-compass" aria-hidden="true"></i>' +
      "<h1>" + esc(t("common.notFoundTitle")) + "</h1><p>" + esc(t("common.notFoundText")) + "</p>" +
      '<a class="btn-ui btn-ui--primary" href="' + url(back) + '"><i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i><span>' +
      esc(t(type === "project" ? "pd.back" : "bd.back")) + "</span></a></div>";
  }

  function projectDetail(el) {
    var list = App.data.projects;
    var id = el.dataset.id || param("id") || list[0].id;
    var idx = list.findIndex(function (p) { return p.id === id; });
    if (idx === -1) { el.innerHTML = notFound("project"); document.title = t("common.notFoundTitle"); return; }
    var p = list[idx];
    var prev = list[(idx - 1 + list.length) % list.length];
    var next = list[(idx + 1) % list.length];
    var others = ["shop", "dash", "landing", "code"].filter(function (v) { return v !== p.preview; }).slice(0, 2);

    document.title = p.title + " — " + pick(App.data.profile.name);

    el.innerHTML =
      '<section class="page-hero"><div class="container">' +
        '<nav class="breadcrumb-ui" aria-label="breadcrumb"><a href="' + url("") + '">' + esc(t("nav.home")) + '</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i>' +
          '<a href="' + url("projects") + '">' + esc(t("nav.projects")) + '</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span aria-current="page">' + esc(p.title) + "</span></nav>" +
        '<span class="tag reveal">' + esc(t("cat." + p.category)) + "</span>" +
        '<h1 class="reveal">' + esc(p.title) + "</h1>" +
        '<p class="lead-text reveal">' + esc(pick(p.desc)) + "</p>" +
      "</div></section>" +
      '<section class="pb-5"><div class="container">' +
        '<div class="reveal" data-reveal="zoom">' + preview(p, "detail-hero-preview", null, p.title) + "</div>" +
        '<div class="detail-meta">' +
          metaItem("pd.client", p.client) + metaItem("pd.year", p.year) + metaItem("pd.role", pick(p.role)) + metaItem("pd.duration", pick(p.duration)) +
        "</div>" +
        '<div class="row g-5">' +
          '<div class="col-lg-8">' +
            block("01", "pd.overview", "<p>" + esc(pick(p.overview)) + "</p>") +
            block("02", "pd.challenge", "<p>" + esc(pick(p.challenge)) + "</p>") +
            block("03", "pd.solution", "<p>" + esc(pick(p.solution)) + "</p>") +
            block("04", "pd.results", '<div class="results-grid">' + p.results.map(function (r) {
              return '<div class="result-card"><strong dir="ltr">' + esc(r.value) + "</strong><span>" + esc(pick(r.label)) + "</span></div>";
            }).join("") + "</div>") +
            (p.image ? "" : block("05", "pd.gallery", '<div class="gallery-grid">' + others.map(function (v) { return preview(p, "", v); }).join("") + "</div>")) +
          "</div>" +
          '<aside class="col-lg-4"><div class="sticky-aside">' +
            '<div class="card-ui aside-card reveal"><h3>' + esc(t("pd.stack")) + '</h3><div class="stack-list" dir="ltr">' +
              p.stack.map(function (s) { return '<span class="tag tag--neutral">' + esc(s) + "</span>"; }).join("") + "</div></div>" +
            projectLinks(p) +
            '<div class="card-ui aside-card reveal"><h3>' + esc(t("pd.ctaTitle")) + "</h3><p>" + esc(t("pd.ctaText")) + "</p>" +
              '<a class="btn-ui btn-ui--primary w-100" href="' + url("contact") + '"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>' + esc(t("pd.ctaBtn")) + "</span></a></div>" +
          "</div></aside>" +
        "</div>" +
        '<nav class="pager" aria-label="' + esc(t("pd.pager")) + '">' +
          '<a class="card-ui prev" href="' + url("projects/" + prev.id) + '"><small><i class="fa-solid fa-arrow-left dir-icon" aria-hidden="true"></i>' + esc(t("common.prev")) + "</small><strong>" + esc(prev.title) + "</strong></a>" +
          '<a class="card-ui next" href="' + url("projects/" + next.id) + '"><small>' + esc(t("common.next")) + ' <i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></small><strong>' + esc(next.title) + "</strong></a>" +
        "</nav>" +
      "</div></section>";

    function projectLinks(project) {
      if (!project.liveUrl && !project.sourceUrl) return "";
      return '<div class="card-ui aside-card reveal"><h3>' + esc(t("pd.links")) + '</h3><div class="d-grid gap-2">' +
        (project.liveUrl ? '<a class="btn-ui btn-ui--outline w-100" href="' + esc(project.liveUrl) + '" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i><span>' + esc(t("pd.live")) + "</span></a>" : "") +
        (project.sourceUrl ? '<a class="btn-ui btn-ui--outline w-100" href="' + esc(project.sourceUrl) + '" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-github" aria-hidden="true"></i><span>' + esc(t("pd.source")) + "</span></a>" : "") +
        "</div></div>";
    }

    function metaItem(key, value) {
      return '<div class="detail-meta-item reveal"><small>' + esc(t(key)) + "</small><strong>" + esc(value) + "</strong></div>";
    }
    function block(num, key, html) {
      return '<div class="detail-block reveal"><h2><span class="num">' + num + "</span>" + esc(t(key)) + "</h2>" + html + "</div>";
    }
  }

  function postDetail(el) {
    var list = App.data.posts;
    var id = el.dataset.id || param("id") || list[0].id;
    var post = list.find(function (p) { return p.id === id; });
    if (!post) { el.innerHTML = notFound("post"); document.title = t("common.notFoundTitle"); return; }

    var title = pick(post.title);
    var profile = App.data.profile;
    var pageUrl = encodeURIComponent(window.location.href);
    var headings = [];
    document.title = title + " — " + pick(profile.name);

    var body = post.body.map(function (b) {
      var text = b.type === "code" ? b.text : pick(b.text);
      switch (b.type) {
        case "h2":
          var hid = "section-" + headings.length;
          headings.push({ id: hid, text: text });
          return '<h2 id="' + hid + '">' + esc(text) + "</h2>";
        case "quote": return "<blockquote>" + esc(text) + "</blockquote>";
        case "code": return '<pre><code>' + esc(text) + "</code></pre>";
        case "ul": return "<ul>" + text.map(function (li) { return "<li>" + esc(li) + "</li>"; }).join("") + "</ul>";
        default: return "<p>" + esc(text) + "</p>";
      }
    }).join("");

    el.innerHTML =
      '<div class="reading-progress" id="readingProgress" aria-hidden="true"></div>' +
      '<section class="page-hero"><div class="container">' +
        '<nav class="breadcrumb-ui" aria-label="breadcrumb"><a href="' + url("") + '">' + esc(t("nav.home")) + '</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i>' +
          '<a href="' + url("blog") + '">' + esc(t("nav.blogs")) + '</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span aria-current="page">' + esc(t("blogcat." + post.category)) + "</span></nav>" +
        '<div class="blog-meta reveal"><span class="tag">' + esc(t("blogcat." + post.category)) + "</span>" +
          '<span><i class="fa-regular fa-calendar" aria-hidden="true"></i><time datetime="' + post.date + '">' + esc(App.lang.formatDate(post.date, { year: "numeric", month: "long", day: "numeric" })) + "</time></span>" +
          '<span><i class="fa-regular fa-clock" aria-hidden="true"></i>' + post.readTime + " " + esc(t("common.minRead")) + "</span></div>" +
        '<h1 class="reveal article-title">' + esc(title) + "</h1>" +
        '<p class="lead-text reveal">' + esc(pick(post.excerpt)) + "</p>" +
      "</div></section>" +
      '<section><div class="container">' +
        '<figure class="article-cover reveal" data-reveal="zoom"><img src="' + url(post.image) + '" alt="' + esc(title) + '" width="1200" height="525"></figure>' +
        '<div class="row g-5">' +
          '<article class="col-lg-8"><div class="prose" id="articleBody">' + body + "</div>" +
            '<div class="card-ui author-box"><img src="' + url(profile.photo) + '" alt="' + esc(pick(profile.name)) + '" width="64" height="64">' +
              "<div><h3>" + esc(pick(profile.name)) + "</h3><p>" + esc(t("bd.authorBio")) + "</p></div></div>" +
          "</article>" +
          '<aside class="col-lg-4"><div class="sticky-aside">' +
            (headings.length ? '<div class="card-ui aside-card"><h3>' + esc(t("bd.toc")) + '</h3><ul class="toc-list">' +
              headings.map(function (h) { return '<li><a href="#' + h.id + '">' + esc(h.text) + "</a></li>"; }).join("") + "</ul></div>" : "") +
            '<div class="card-ui aside-card"><h3>' + esc(t("bd.share")) + '</h3><div class="share-links">' +
              '<a class="social-link" href="https://x.com/intent/post?url=' + pageUrl + "&text=" + encodeURIComponent(title) + '" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>' +
              '<a class="social-link" href="https://www.linkedin.com/sharing/share-offsite/?url=' + pageUrl + '" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>' +
              '<a class="social-link" href="https://www.facebook.com/sharer/sharer.php?u=' + pageUrl + '" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>' +
              '<button type="button" class="social-link" data-copy-link aria-label="' + esc(t("bd.copy")) + '" title="' + esc(t("bd.copy")) + '"><i class="fa-solid fa-link" aria-hidden="true"></i></button>' +
            "</div></div>" +
            '<div class="card-ui aside-card"><h3>' + esc(t("pd.ctaTitle")) + "</h3><p>" + esc(t("bd.ctaText")) + "</p>" +
              '<a class="btn-ui btn-ui--primary w-100" href="' + url("contact") + '"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>' + esc(t("pd.ctaBtn")) + "</span></a></div>" +
          "</div></aside>" +
        "</div>" +
      "</div></section>";

    var related = document.querySelector('[data-render="posts"][data-exclude-current]');
    if (related) related.dataset.exclude = post.id;
  }

  /* ---------- Orchestration ---------- */
  function all() {
    document.querySelectorAll("[data-render]").forEach(function (el) {
      var type = el.dataset.render;
      var limit = parseInt(el.dataset.limit, 10) || Infinity;
      var html = "";
      switch (type) {
        case "projects":
          html = App.data.projects.slice(0, limit).map(projectCard).join("");
          break;
        case "education":
          html = App.data.education.slice(0, limit).map(educationItem).join("");
          break;
        case "experience":
          html = App.data.experience.slice(0, limit).map(experienceItem).join("");
          break;
        case "posts":
          html = App.data.posts.filter(function (p) { return p.id !== el.dataset.exclude; }).slice(0, limit).map(postCard).join("");
          break;
        case "skills":
          html = App.data.skills.map(skillGroup).join("");
          break;
        case "tools":
          html = App.data.tools.map(tool).join("");
          break;
        case "project-detail":
          projectDetail(el); return;
        case "post-detail":
          postDetail(el); return;
        default: return;
      }
      el.innerHTML = html;
    });

    renderChips("projects", unique(App.data.projects, "category"), "cat.");
    renderChips("posts", unique(App.data.posts, "category"), "blogcat.");
    applyFilter("projects");
    applyFilter("posts");
  }

  function init() {
    setupFilters();
    document.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-copy-link]");
      if (!btn) return;
      var done = function () { App.components.toast(t("bd.copied")); };
      if (navigator.clipboard) navigator.clipboard.writeText(window.location.href).then(done, done);
      else done();
    });
  }

  return { all: all, init: init };
})();
