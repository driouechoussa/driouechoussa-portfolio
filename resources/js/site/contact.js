/* =========================================================
   CONTACT — validates the contact form in the browser, then
   sends it to Laravel (POST /contact) without a page reload.
   Without JavaScript the form still posts normally and the
   server shows errors / the success message.
   ========================================================= */
window.App = window.App || {};

App.contact = (function () {
  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  /** Front-end message key for each field (server errors map to the same keys). */
  var FIELD_ERROR = { name: "form.errRequired", email: "form.errEmail", message: "form.errMessage" };

  function showError(input, key) {
    var field = input.closest(".field");
    var msg = field && field.querySelector(".field-error");
    if (field) field.classList.toggle("has-error", !!key);
    input.setAttribute("aria-invalid", String(!!key));
    if (msg) {
      msg.dataset.i18n = key || "";
      msg.textContent = key ? App.lang.t(key) : "";
    }
  }

  function validateField(input) {
    var value = input.value.trim();
    var error = "";

    if (input.required && !value) error = "form.errRequired";
    else if (input.type === "email" && value && !EMAIL_RE.test(value)) error = "form.errEmail";
    else if (input.minLength > 0 && value && value.length < input.minLength) error = "form.errMessage";
    else if (input.maxLength > 0 && value.length > input.maxLength) error = "form.errTooLong";

    showError(input, error);
    return !error;
  }

  function setBusy(button, busy) {
    var label = button.querySelector("span");
    if (busy) {
      button.dataset.label = label.dataset.i18n;
      label.dataset.i18n = "form.sending";
    } else if (button.dataset.label) {
      label.dataset.i18n = button.dataset.label;
    }
    label.textContent = App.lang.t(label.dataset.i18n);
    button.disabled = busy;
    button.setAttribute("aria-busy", String(busy));
  }

  function init() {
    var form = document.getElementById("contactForm");
    if (!form) return;
    var inputs = form.querySelectorAll(".input-ui[required], .input-ui[type=email]");
    var button = form.querySelector('button[type="submit"]');

    inputs.forEach(function (input) {
      input.addEventListener("blur", function () { validateField(input); });
      input.addEventListener("input", function () {
        if (input.closest(".field").classList.contains("has-error")) validateField(input);
      });
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var firstInvalid = null;
      inputs.forEach(function (input) {
        if (!validateField(input)) firstInvalid = firstInvalid || input;
      });
      if (firstInvalid) { firstInvalid.focus(); return; }

      setBusy(button, true);

      fetch(form.action, {
        method: "POST",
        headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
        body: new FormData(form),
        credentials: "same-origin"
      })
        .then(function (res) {
          return res.json().catch(function () { return {}; }).then(function (body) { return { status: res.status, body: body }; });
        })
        .then(function (r) {
          if (r.status >= 200 && r.status < 300) {
            App.components.toast(r.body.message || App.lang.t("form.success"));
            form.reset();
            return;
          }
          if (r.status === 422 && r.body.errors) {
            var focusTarget = null;
            Object.keys(r.body.errors).forEach(function (name) {
              var input = form.elements[name];
              if (!input || !input.classList || !input.classList.contains("input-ui")) return;
              showError(input, FIELD_ERROR[name] || "form.errChoice");
              focusTarget = focusTarget || input;
            });
            if (focusTarget) focusTarget.focus();
            return;
          }
          App.components.toast(App.lang.t(r.status === 429 ? "form.errThrottle" : "form.errServer"));
        })
        .catch(function () { App.components.toast(App.lang.t("form.errServer")); })
        .then(function () { setBusy(button, false); });
    });
  }

  return { init: init };
})();
