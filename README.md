# Community Bloom

A portfolio demo site built with Drupal 11.

The site is for a fictional nonprofit — **Community Bloom** — that supports local gardening initiatives and educational workshops. The concept is small and relatable, but provides enough content variety to demonstrate core Drupal and frontend skills across content types, views, theming, permissions, forms, and SEO.

---

## Tech Stack

| Layer | Technology |
|---|---|
| CMS | Drupal 11.3.3 (via `drupal/recommended-project`) |
| Base theme | Bootstrap Barrio (Bootstrap 5) |
| Sub-theme | `community_bloom` (custom) |
| Local dev | DDEV |
| Deployment target | Docker Compose → k3s → Cloudflare tunnel |

**Contrib modules:**

| Module | Purpose |
|---|---|
| Bootstrap Barrio | Bootstrap 5 base theme for the sub-theme |
| Webform | Newsletter signup form embedded on the homepage |
| Pathauto + Token | Clean, automatic URLs: `/workshops/spring-planting-basics` |
| Metatag | SEO meta tags — description and OG tags per node |
| Admin Toolbar | Improved admin UX, visible when the Reviewer logs in |

Dependencies are managed entirely by Composer. Drupal core and contrib modules are **not committed to the repository** — this is standard practice in professional Drupal shops. The `composer.json` is the source of truth; running `composer install` after cloning restores all dependencies.

---

## Design Decisions

### Why Bootstrap Barrio

Bootstrap Barrio provides a Bootstrap 5 base theme with full Drupal template integration, responsive navigation, and the card component out of the box. It accelerates development without sacrificing customizability — the sub-theme (`community_bloom`) handles all project-specific overrides without touching the base theme, which is the correct professional pattern for Drupal theming.

### Color palette — Greens & Earth Tones

| Role | Value | Usage |
|---|---|---|
| Primary | `#4a7c59` | Sage green — buttons, links, accents |
| Secondary | `#8b5e3c` | Warm brown — headings, borders |
| Background | `#f5f0e8` | Cream — page background |
| Surface | `#ffffff` | Card backgrounds |
| Text | `#2c2c2c` | Body copy |

Colors were chosen to feel organic and on-brand for a gardening nonprofit. All foreground/background combinations meet **WCAG 2.1 AA** contrast requirements (4.5:1 minimum for body text).

### Typography

System font stack — no external font dependencies:

```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```

This avoids external requests, eliminates GDPR font-hosting concerns, and keeps load times fast.

### Accessibility

- WCAG 2.1 AA color contrast on all text/background combinations
- Visible focus states on all interactive elements
- Semantic heading hierarchy per page (one `<h1>`)
- Alt text on all images via Drupal media fields
- Skip-navigation link in the header template

---

## Challenges & How They Were Solved

### 1. Drupal 11 vs. Drupal 10

The plan called for Drupal 10, but `composer create-project drupal/recommended-project` pulled **Drupal 11.3.3** — the current stable release. Rather than pin to 10, I verified that all required contrib modules had Drupal 11-compatible releases and continued on 11. Drupal 11 is fully supported, and deploying the current stable release is the right call for any new project.

### 2. DDEV on Windows — Docker BuildKit path bug

Docker Desktop on Windows has a BuildKit path-doubling bug that causes `ddev start` to fail with a build error on Windows drive paths. The fix is to disable BuildKit for DDEV's build step:

```bash
DOCKER_BUILDKIT=0 ddev start
```

Normal `ddev exec`, `drush`, and `composer` commands are unaffected. This is a known Docker Desktop issue on Windows and not specific to DDEV or Drupal.

### 3. AI Claude use

Due to the immediate goal of delivering a Drupal project and my unfamiliarity with Drupal (I've mainly used Wordpress), I leaned heavily on Claude for the development environment and familiarization with Drupal's architecture.

---

## Local Development Setup

**Prerequisites:** [DDEV](https://ddev.readthedocs.io/) and Docker Desktop installed and running.

```bash
# 1. Clone the repository
git clone <repo-url> community-bloom
cd community-bloom

# 2. Configure DDEV (already committed in .ddev/)
ddev start

# 3. Install Composer dependencies (core, contrib modules, Drush)
ddev composer install

# 4. Install Drupal (fresh install)
ddev drush site:install --account-name=admin --account-pass=admin -y

# 5. Import committed configuration
ddev drush config:import -y

# 6. Clear cache
ddev drush cache:rebuild
```

The site will be available at **http://community-bloom.ddev.site**.

> **Windows note:** If `ddev start` fails during the build step, prefix it with `DOCKER_BUILDKIT=0` — see challenge #2 above.

---

## Repository Structure

```
community-bloom/
├── composer.json          # Dependency definitions (source of truth)
├── composer.lock
├── config/
│   └── sync/              # Drupal config exports (drush config:export)
├── web/
│   └── themes/
│       └── custom/
│           └── community_bloom/   # Sub-theme (CSS, JS, templates)
└── README.md
```

Drupal core, contrib modules, and the `vendor/` directory are excluded from version control via `.gitignore`.
