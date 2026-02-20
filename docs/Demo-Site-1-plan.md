# Community Bloom — Demo Site Plan

## Purpose

A portfolio demo site built on Drupal 10, created as part of a Web Designer job application to the Bureau of Economic Geology at UT Austin. The site is a fictional nonprofit called **Community Bloom** that supports local gardening initiatives and educational workshops. The concept is small, relatable, and provides enough content variety to demonstrate core Drupal and frontend skills without requiring the hiring manager to dig deep.

**Hiring manager will receive:**
- A link to the live demo (Cloudflare tunnel → k3s cluster)
- A link to the GitHub repo
- A short intro message

---

## Pages (3 total)

### 1. Home
- Hero section with a banner image and tagline
- Brief mission statement (2–3 sentences)
- Featured Workshops block — a View block pulling the next 2–3 upcoming workshops (image, title, date)
- Newsletter signup form (Webform) — name + email, embedded as a block

### 2. About Us
- Mission & values section with body text
- Team grid — a View displaying all Team Member nodes as cards (photo, name, role, short bio)

### 3. Workshops
- Full listing of Workshop nodes via a Drupal View
- Cards showing: image, title, date, location, short body excerpt
- Sorted by date ascending (upcoming first)
- No pagination needed at 3 nodes — but View will be built so adding more nodes works automatically

---

## Content Types

### Workshop
| Field         | Type              | Notes                         |
|---------------|-------------------|-------------------------------|
| Title         | Core text         | Node title                    |
| Body          | Long text + summary | Used for card excerpt + full page |
| Image         | Image             | Required; used on cards        |
| Date & Time   | Date              | Used for sorting + display    |
| Location      | Text (plain)      | e.g., "Lincoln Community Center" |
| Registration Link | Link          | Optional CTA on each node page |

### Team Member
| Field   | Type       | Notes                     |
|---------|------------|---------------------------|
| Name    | Core title | Node title                |
| Role    | Text plain | e.g., "Executive Director"|
| Photo   | Image      | Square crop preferred     |
| Bio     | Long text  | Short paragraph           |

**Sample content:**
- 3 Workshop nodes (realistic gardening workshop titles, dates ~1–2 months out)
- 3 Team Member nodes

---

## User Roles & Permissions

| Role          | Capabilities                                                    |
|---------------|-----------------------------------------------------------------|
| Administrator | Full site access                                                |
| Editor        | Create, edit, delete Workshop and Team Member nodes; no admin config access |
| Reviewer      | View-only admin access (for the hiring manager's demo login); can browse admin UI but cannot create or edit anything |

The hiring manager will receive **Reviewer** credentials with the intro message.

---

## Contrib Modules

| Module           | Purpose                                               |
|------------------|-------------------------------------------------------|
| Bootstrap Barrio | Base theme for the sub-theme                         |
| Webform          | Newsletter signup form on the homepage               |
| Pathauto + Token | Clean URLs: `/workshops/spring-planting-basics`      |
| Metatag          | SEO meta tags (description, OG tags per node)        |
| Admin Toolbar    | Improved admin UX — visible when the reviewer logs in |

---

## Theme

**Base:** Bootstrap Barrio (contributed)
**Sub-theme name:** `community_bloom`

### Customization scope (moderate)
- Custom sticky header with logo + primary nav
- Nav: horizontal desktop, hamburger collapse on mobile
- Workshop and Team Member card styles (Bootstrap card component, custom overrides)
- Homepage hero section styling
- CSS custom properties for color and font overrides

### Color palette — Greens & Earth Tones
| Role       | Value       | Usage                        |
|------------|-------------|------------------------------|
| Primary    | `#4a7c59`   | Sage green — buttons, links, accents |
| Secondary  | `#8b5e3c`   | Warm brown — headings, borders |
| Background | `#f5f0e8`   | Cream — page background      |
| Surface    | `#ffffff`   | Card backgrounds             |
| Text       | `#2c2c2c`   | Body copy                    |

### Typography
System font stack — no external dependency (fast load, no GDPR font-hosting concern):
```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```

### Accessibility targets
- WCAG 2.1 AA color contrast on all text/background combinations
- Visible focus states on all interactive elements
- Semantic heading hierarchy per page (one `<h1>`)
- Alt text on all images (set via Drupal media fields)
- Skip-navigation link in the header template

---

## Development Environment

**Tool:** DDEV
**Why:** Community standard for Drupal development; pre-configured for Drush, Composer, and Xdebug. No manual LAMP/LEMP stack setup.

### Local setup workflow
```bash
mkdir community-bloom && cd community-bloom
ddev config --project-type=drupal10 --docroot=web
ddev start
ddev composer create drupal/recommended-project .
ddev drush site:install --account-name=admin --account-pass=admin -y
```

Then install modules and Bootstrap Barrio via Composer + Drush.

---

## Deployment Workflow

1. **Develop locally with DDEV** — all Drupal config, content, and theme work happens here
2. **Export Drupal config** — `ddev drush config:export` → committed to repo under `config/sync/`
3. **Export database** — `ddev export-db` for a clean database snapshot
4. **Write `docker-compose.yml`** — Drupal + MariaDB, mirroring what DDEV runs internally
5. **Verify on Docker Compose** — confirm the site runs cleanly outside DDEV
6. **Convert to k3s manifests** — Deployment, Service, PersistentVolumeClaim, Ingress (handled separately)
7. **Cloudflare tunnel** — expose the k3s Ingress service (handled separately)

> **Note:** The Docker Compose → k3s conversion and Cloudflare tunnel are out of scope for the Drupal build phase and will be handled independently.

---

## GitHub Repository

**Contents:** Custom code + config only (standard Drupal professional practice)

```
community-bloom/
├── composer.json
├── composer.lock
├── .gitignore           # excludes vendor/, web/core/, web/modules/contrib/
├── config/
│   └── sync/            # Drupal config exports (drush config:export)
├── web/
│   └── themes/
│       └── custom/
│           └── community_bloom/   # sub-theme files only
└── README.md
```

**Why custom code + config only:** Drupal core and contrib modules are managed by Composer and are not committed to version control. This is the standard expected in any professional Drupal shop, including university IT departments. The `composer.json` file is the source of truth for dependencies — anyone cloning the repo runs `composer install` to get everything.

---

## README Sections

1. **Project Purpose & Context** — what Community Bloom is, why it was built, who it's for
2. **Tech Stack** — Drupal 10, Bootstrap Barrio, modules list, DDEV, Docker, k3s
3. **Design Decisions** — color palette rationale, Bootstrap Barrio selection, sub-theme scope
4. **Challenges & How They Were Solved** — document 1–2 real issues encountered during the build
5. **Local Development Setup** — DDEV setup steps so anyone can clone and run it
6. **Demo Credentials** — Reviewer login for the hiring manager

---

## Build Phases & Time Budget

Given the 1–2 day timeline, tasks are ordered to get a working site visible as fast as possible.

| Phase | Task | Est. Time |
|-------|------|-----------|
| 1 | DDEV init, Drupal 10 install, Bootstrap Barrio install | 1–2 hrs |
| 2 | Content types (Workshop + Team Member) + fields | 1 hr |
| 3 | Views: Workshop listing, Team Member grid | 1 hr |
| 4 | User roles + Reviewer permissions | 30 min |
| 5 | Pathauto + Metatag + Admin Toolbar config | 30 min |
| 6 | Sub-theme scaffold + color/font CSS overrides | 1–2 hrs |
| 7 | Custom header, nav, card styles | 2–3 hrs |
| 8 | Webform: newsletter signup, embed on homepage | 30 min |
| 9 | Placeholder content (3 workshops, 3 team members, page bodies) | 1 hr |
| 10 | Mobile/responsive QA + accessibility check | 1 hr |
| 11 | Config export + GitHub repo setup + .gitignore | 30 min |
| 12 | README | 1 hr |
| **Total** | | **~11–14 hrs** |

> At a comfortable pace this is a full day of focused work. At the tight end of 1–2 days, skip polish and ship — a working, readable site beats a half-finished polished one.

---

## What This Demonstrates (Mapped to Job Posting)

| Job Requirement | How It's Shown |
|---|---|
| CMS proficiency | Drupal 10 install, config management, contrib module setup |
| Content types & fields | Workshop + Team Member content types |
| HTML/CSS | Custom sub-theme; reviewable in the GitHub repo |
| Responsive / mobile-friendly | Bootstrap Barrio grid + custom breakpoint overrides |
| Web accessibility | WCAG AA contrast, skip-nav, semantic HTML, focus states |
| CMS workflows & permissions | Editor + Reviewer roles with distinct permission sets |
| SEO | Metatag module, Pathauto clean URLs, heading hierarchy |
| Browser dev tools (implied) | Code comments noting cross-browser decisions |
| Drupal module development (preferred) | Sub-theme with custom Twig template overrides and CSS |
| JavaScript (preferred) | Bootstrap Barrio's JS for nav collapse + any minor custom JS |
