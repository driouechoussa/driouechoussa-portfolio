/* =========================================================
   TIMELINE — snake-like experience path.
   1. Places items in "boustrophedon" order: row 1 runs in the
      reading direction, row 2 runs back, and so on.
   2. Measures each dot and draws an SVG path through them with
      rounded U-turns between rows.
   Works in LTR and RTL (positions are measured, not assumed).
   Below 768px CSS hides the SVG and shows a vertical line.
   ========================================================= */
window.App = window.App || {};

App.timeline = (function () {
  var wrap, grid, svg, track, line, raf;
  var SVG_NS = "http://www.w3.org/2000/svg";

  function columns() {
    return getComputedStyle(grid).gridTemplateColumns.split(" ").filter(Boolean).length;
  }

  function place(items, cols) {
    items.forEach(function (el, i) {
      if (cols < 2) { el.style.gridRow = ""; el.style.gridColumn = ""; return; }
      var row = Math.floor(i / cols);
      var col = i % cols;
      if (row % 2 === 1) col = cols - 1 - col;
      el.style.gridRow = String(row + 1);
      el.style.gridColumn = String(col + 1);
    });
  }

  function layout() {
    if (!wrap) return;
    var items = Array.prototype.slice.call(grid.querySelectorAll(".xp-item"));
    var cols = columns();
    place(items, cols);
    if (cols < 2 || items.length < 2) return;

    var box = wrap.getBoundingClientRect();
    var pts = items.map(function (el) {
      var d = el.querySelector(".xp-dot").getBoundingClientRect();
      var r = el.getBoundingClientRect();
      return { x: d.left + d.width / 2 - box.left, y: d.top + d.height / 2 - box.top, left: r.left - box.left, right: r.right - box.left };
    });

    var lead = 28;
    var firstDir = Math.sign(pts[1].x - pts[0].x) || 1;
    if (Math.abs(pts[1].y - pts[0].y) > 4) firstDir = 1;
    var d = "M " + (pts[0].x - firstDir * lead) + " " + pts[0].y + " L " + pts[0].x + " " + pts[0].y;

    for (var i = 0; i < pts.length - 1; i++) {
      var a = pts[i];
      var b = pts[i + 1];
      if (Math.abs(a.y - b.y) < 4) {
        d += " L " + b.x + " " + b.y;
      } else {
        // U-turn on the outer side of the row end
        var dir = a.x > box.width / 2 ? 1 : -1;
        var edge = dir === 1 ? Math.max(a.right, b.right) + 18 : Math.min(a.left, b.left) - 18;
        var r = Math.min(36, (b.y - a.y) / 2);
        d += " L " + (edge) + " " + a.y +
          " Q " + (edge + dir * r) + " " + a.y + " " + (edge + dir * r) + " " + (a.y + r) +
          " L " + (edge + dir * r) + " " + (b.y - r) +
          " Q " + (edge + dir * r) + " " + b.y + " " + edge + " " + b.y +
          " L " + b.x + " " + b.y;
      }
    }
    var last = pts[pts.length - 1];
    var prev = pts[pts.length - 2];
    var lastDir = Math.abs(last.y - prev.y) < 4 ? Math.sign(last.x - prev.x) : -firstDir;
    d += " L " + (last.x + lastDir * lead) + " " + last.y;

    svg.setAttribute("viewBox", "0 0 " + box.width + " " + box.height);
    track.setAttribute("d", d);
    line.setAttribute("d", d);
    var len = Math.ceil(line.getTotalLength());
    line.style.setProperty("--path-len", len);
  }

  function schedule() {
    window.cancelAnimationFrame(raf);
    raf = window.requestAnimationFrame(layout);
  }

  function init() {
    wrap = document.querySelector(".xp-snake");
    if (!wrap) return;
    grid = wrap.querySelector(".xp-grid");

    svg = document.createElementNS(SVG_NS, "svg");
    svg.setAttribute("class", "xp-path");
    svg.setAttribute("aria-hidden", "true");
    svg.setAttribute("focusable", "false");
    svg.setAttribute("preserveAspectRatio", "none");
    track = document.createElementNS(SVG_NS, "path");
    track.setAttribute("class", "track");
    line = document.createElementNS(SVG_NS, "path");
    line.setAttribute("class", "line");
    svg.appendChild(track);
    svg.appendChild(line);
    wrap.insertBefore(svg, wrap.firstChild);

    layout();

    if ("ResizeObserver" in window) new ResizeObserver(schedule).observe(wrap);
    else window.addEventListener("resize", schedule);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(schedule);

    if ("IntersectionObserver" in window) {
      var io = new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) { wrap.classList.add("is-drawn"); io.disconnect(); }
      }, { threshold: 0.2 });
      io.observe(wrap);
    } else {
      wrap.classList.add("is-drawn");
    }
  }

  return { init: init, refresh: schedule };
})();
