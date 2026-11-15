# Website Requirements — driouechoussa.me

Everything the portfolio website must contain to be complete and ready to launch.

**Status key:** ✅ done · 🟡 partly done / placeholder · ❌ missing

---

## 1. Purpose

A personal portfolio for **Oussama Driouech**, a full-stack web developer. It must:

- Present who he is, what he does, and how to reach him.
- Show his work (projects / case studies) and his writing (blog).
- Turn visitors (clients, recruiters) into contacts.
- Be available in **English, French and Arabic**.

---

## 2. Pages and routes

| Page | URL | Route name | Status |
|---|---|---|---|
| Home | `/` | `home` | ✅ |
| About | `/about` | `about` | ✅ |
| Projects list | `/projects` | `projects.index` | ✅ |
| Project details | `/projects/{slug}` | `projects.show` | ✅ |
| Blog list | `/blog` | `posts.index` | ✅ |
| Blog article | `/blog/{slug}` | `posts.show` | ✅ |
| Contact | `/contact` | `contact` | ✅ |
| 404 / 500 error pages in the site's design | — | — | ❌ |
| Privacy policy (required if the contact form stores data or analytics is used) | `/privacy` | — | ❌ |

### Sections each page must have

**Home**
- ✅ Hero / identity card: photo, name, handle, role, social links, "Get my CV" and "My works" buttons
- ✅ Featured projects (6) + "Explore all projects" button
- ✅ Education
- ✅ Experience timeline (6)
- ✅ Latest blog posts (3) + "Explore all articles" button
- ✅ Call-to-action banner ("Start a project" / "Send an email")

**About**
- ✅ Page hero with breadcrumb
- ✅ Story: portrait, 3 paragraphs, info list (name, location, email, freelance status), CV + contact buttons
- ✅ Stats: years of experience, projects, clients, technologies
- ✅ Skills grouped (frontend, backend, tools) with levels
- ✅ Tools / technologies grid
- ✅ Experience timeline
- ✅ Education
- ✅ Call-to-action banner

**Projects**
- ✅ Page hero
- ✅ Category filter (e-commerce, web app, …)
- ✅ Project cards grid
- ✅ Empty state when a filter has no results
- ✅ Call-to-action banner

**Project details**
- ✅ Breadcrumb
- ✅ Title, category, year, client, duration, role, tech stack
- ✅ Overview, challenge, solution
- ✅ Results (key numbers)
- ✅ Previous / next project links
- ✅ Server-side page title and meta description
- ❌ Real screenshots or images of the project (currently drawn mock-ups)
- ❌ Live site and source code links

**Blog**
- ✅ Page hero
- ✅ Category filter and search
- ✅ Article cards (image, category, date, read time, excerpt)
- ✅ Call-to-action banner

**Blog article**
- ✅ Breadcrumb, cover image, title, date, read time
- ✅ Article body (paragraphs, headings, code blocks) + table of contents
- ✅ Author box
- ✅ Share links
- ✅ "Keep reading" (3 related articles)
- ✅ Server-side page title and meta description

**Contact**
- ✅ Page hero
- ✅ Contact details: email, phone, location, social links
- ✅ Form: name, email, subject, message
- 🟡 Form opens the visitor's email app (`mailto:`); it does not send from the server

### On every page

- ✅ Header with navigation, language switcher, theme switcher, "Hire me" button
- ✅ Mobile menu
- ✅ Footer with navigation, contact details, social links and privacy link
- ✅ "Skip to content" link

---

## 3. Content that must be real (not placeholder)

Edit the profile in `config/portfolio.php` and the rest in `database/seeders/data/portfolio.json`, then run `php artisan db:seed`.

| Content | Where | Status |
|---|---|---|
| Name, handle, location | `config/portfolio.php` | ✅ |
| Email | `config/portfolio.php` / `PORTFOLIO_EMAIL` | 🟡 check that `hello@driouechoussa.me` exists and receives mail |
| Phone | `config/portfolio.php` / `PORTFOLIO_PHONE` | 🟡 placeholder `+212 600 000 000` |
| Social links (LinkedIn, Instagram, X, GitHub) | `config/portfolio.php` | 🟡 verify each URL |
| Profile photo and about portrait | `public/images/` | 🟡 confirm final photos |
| CV (PDF) | `public/cv/oussama-driouech-cv.pdf` | 🟡 confirm it is the latest version |
| Projects (8) | seeder JSON | 🟡 replace sample projects/clients with real ones |
| Experience (6) | seeder JSON | 🟡 replace with real history |
| Education (3) | seeder JSON | 🟡 confirm |
| Blog posts (6) | seeder JSON | 🟡 review or replace |
| Skills and levels, tools | seeder JSON | 🟡 review |
| Stats numbers (7+, 50+, 30+, 12+) | `resources/views/pages/about.blade.php` | 🟡 confirm they are true |
| Page texts in EN / FR / AR | `public/js/translations.js` | ✅ (review the Arabic and French copy) |

---

## 4. Languages and theme

- ✅ English, French, Arabic, switchable without reloading
- ✅ Right-to-left layout for Arabic
- ✅ Dark and light themes, saved in the browser and applied before the page appears
- ❌ Language in the URL (e.g. `/fr/projects`) so search engines can index each language separately

---

## 5. SEO

- ✅ Unique `<title>` and meta description per page (detail pages filled from the database)
- ✅ Open Graph title, description and type
- ✅ `robots.txt`
- ❌ `sitemap.xml` listing every page, project and article
- ❌ Canonical URL on each page
- ❌ `og:image` and Twitter card image (a 1200×630 share image)
- ❌ Structured data (JSON-LD): `Person` on home/about, `BlogPosting` on articles
- ❌ `hreflang` tags (depends on language URLs, section 4)
- 🟡 Page content is drawn by JavaScript; key text (project and article content) should also be in the server HTML so search engines and link previews see it

---

## 6. Contact and email

- 🟡 Contact form uses `mailto:` (works without a server, but many visitors have no email app set up)
- ❌ Server-side sending: `ContactController@store`, form request validation, mail to the owner, success/error message
- ❌ Spam protection (honeypot field and rate limiting, or a captcha)
- ❌ Optional: save messages in a `contact_messages` table
- ❌ Mail settings in `.env` (`MAIL_MAILER` is currently `log`, `MAIL_FROM_ADDRESS` is `hello@example.com`)

---

## 7. Accessibility

- ✅ Skip link, landmarks (`header`, `main`, `footer`), labelled breadcrumbs
- ✅ Image alt texts, `aria-hidden` on decorative icons
- 🟡 Check colour contrast in both themes
- 🟡 Check full keyboard navigation (menu, filters, form)
- 🟡 Respect `prefers-reduced-motion` for animations

---

## 8. Performance

- ✅ Scripts loaded with `defer`, images with `loading="lazy"` and set sizes
- 🟡 Compress and resize photos (serve WebP where possible)
- ❌ Bundle and minify the site's own CSS/JS (Vite) instead of 12 separate script files
- ❌ Long cache headers for static files on the production server
- 🟡 Bootstrap, Font Awesome and Google Fonts come from CDNs. Consider self-hosting for speed and privacy.

---

## 9. Technical setup

- ✅ Laravel 12, PHP 8.2+
- ✅ Content in the database: `Project`, `Post`, `Education`, `Experience`, `SkillGroup`/`Skill`, `Tool`
- ✅ Controllers per page, named routes, slugs in URLs, 404 for unknown slugs
- ✅ Future-dated posts stay hidden until their date
- ✅ Feature tests for every page (`php artisan test`)
- 🟡 Finish removing Inertia / Breeze: run `composer update` and remove the React, Inertia and Tailwind npm packages
- 🟡 Delete unused old-site files in `public/` (`styles/`, `webfonts/`, `js/main.js`, `images/logo.png`, `images/pic-overview.png`, `images/certificates/`, `cv/Oussama_Driouech.pdf`)
- ❌ Admin area to edit content without touching JSON (e.g. Filament), protected by login
- ❌ Image uploads for projects and posts

---

## 10. Security

- ❌ `APP_DEBUG=false` and `APP_ENV=production` on the live server
- ❌ HTTPS with HTTP → HTTPS redirect
- ❌ Security headers (Content-Security-Policy, X-Frame-Options, Referrer-Policy)
- ❌ CSRF token and validation on the contact form (when it posts to the server)
- ✅ No user accounts or admin login exposed (Breeze removed)

---

## 11. Launch checklist

- [ ] `APP_NAME` set to the site name (currently `Laravel`)
- [ ] `APP_URL=https://driouechoussa.me`
- [ ] Production database created, `php artisan migrate --force` and `php artisan db:seed --force` run
- [ ] `php artisan config:cache`, `route:cache`, `view:cache`
- [ ] Mail settings working and tested
- [ ] All placeholder content from section 3 replaced
- [ ] `sitemap.xml` submitted to Google Search Console
- [ ] Favicon and share image checked
- [ ] Every page tested in EN / FR / AR, dark / light, on mobile and desktop
- [ ] Analytics added (privacy-friendly, e.g. Plausible) and mentioned in the privacy policy
