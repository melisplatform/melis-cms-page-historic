---
title: MelisCmsPageHistoric module
package: melisplatform/melis-cms-page-historic
doc_type: module-documentation
audience: ai
language: en
module_version: unversioned   # no `version` field in composer.json; this doc tracks the current source
last_reviewed: 2026-06-08
maintainer: Melis Technology
keywords: [history, historic, audit, log, page, edition, activity, tracking, cms, melis, back-office, dashboard]
screenshots_dir: ./images
---

# MelisCmsPageHistoric Module — Functional Documentation (for AI)

> **Purpose of this document**: describe, functionally and technically, the
> `melisplatform/melis-cms-page-historic` module, so that an AI (or a developer) can
> understand *what the module does*, *which tools it provides*, *how they work* and
> *where the corresponding code lives*.
>
> **Audience**: consumed by the **MelisAI** module (a MelisPlatform module that exposes an
> MCP function to answer user questions). MelisAI fetches this `.md` file and the
> screenshots in `./images/` **on demand** — so the doc is self-contained and §9 acts as
> the filename→content index for retrieving a specific screenshot.
>
> **Status**: reviewed 2026-06-08 against the current source. The module carries no
> semantic version (no `version` in `composer.json`), so treat this doc as describing the
> current `melisplatform/melis-cms-page-historic` source rather than a tagged release.
>
> Screenshots live in `./images/` (relative paths `./images/...`).

---

## 1. Overview

`MelisCmsPageHistoric` provides a **history / audit log for MelisCms page edition**. It
**records who did what to a page and when** — create, edit, publish, delete, etc. — and
surfaces that history in two places: a **"Historic" tab inside the page editor** (the audit
trail of the current page) and a **"Recent User Activity" dashboard widget** (recent page
activity across users). The history is captured automatically by **listeners** on the CMS
page lifecycle events — no manual logging.

| Item | Value |
|---|---|
| Package name | `melisplatform/melis-cms-page-historic` |
| Type | `melisplatform-module` |
| PHP namespace | `MelisCmsPageHistoric\` → `src/` (PSR-4) |
| Melis category | `cms` |
| License | OSL-3.0 |
| PHP required | `^8.1 | ^8.3` |
| Framework | Laminas (ex-Zend Framework 2/3), Melis MVC architecture |
| dbdeploy | `true` (DB migrations applied automatically) |

### Dependencies (required Melis modules)

Declared in `composer.json`:

- `melisplatform/melis-core` (`^5.2`) — foundation, users, rights, services, events, dashboard
- `melisplatform/melis-engine` (`^5.2`) — page engine
- `melisplatform/melis-front` (`^5.2`) — front-office management
- `melisplatform/melis-cms` (`^5.2`) — CMS pages & the page editor whose events it records and whose tabs it extends

---

## 2. Functional concepts

- **History entry**: a single recorded action on a page — **which page** (`hist_page_id`),
  **what action** (`hist_action`, e.g. save/publish/delete), **when** (`hist_date`), **by
  whom** (`hist_user_id`), and an optional **description** (`hist_description`).
- **Automatic capture**: entries are written by **listeners** that hook the MelisCms page
  lifecycle events (page saved / published / deleted…), not by user action.
- **Two views**: the per-page **Historic tab** (the audit trail of one page) and the
  cross-page **Recent User Activity** dashboard widget.

### Data model (MySQL table)

| Table | Role | Primary key |
|---|---|---|
| `melis_hist_page_historic` | One recorded page action (`hist_page_id`, `hist_action`, `hist_date`, `hist_user_id`, `hist_description`) | `hist_id` |

- MySQL Workbench model: `install/sql/Model/MelisCmsPageHistoric.mwb`
- Base structure: `install/sql/setup_structure.sql`
- Incremental migrations: `install/dbdeploy/*.sql` (install, utf8mb4 conversion)

---

## 3. Tools and elements provided

The module exposes:

1. **A "Historic" tab in the CMS page editor** — the audit trail of the current page
2. **A "Recent User Activity" dashboard plugin** — recent page activity across users
3. **Listeners** that record the history on page events
4. **A table gateway** to read/write history entries

Everything back-office is driven by `src/Controller/PageHistoricController.php`.

---

### 3.1 "Historic" tab (CMS page editor)

Injected as a tab in the CMS page edition tabs (declared in `config/app.interface.php`,
key `melispagehistoric_historic`, icon `history`).

- **Controller**: `src/Controller/PageHistoricController.php`
- **Table configuration**: `config/app.tools.php` (key `tool_meliscmspagehistoric`)
- **Views**: `view/melis-cms-page-historic/page-historic/*.phtml`

A Melis DataTable of the **history entries of the current page** with columns: **User**
(`hist_user_id`), **Action** (`hist_action`), **Date** (`hist_date`), **Description**
(`hist_description`). Data loads via AJAX from
`/melis/MelisCmsPageHistoric/PageHistoric/getPageHistoricData` (`getPageHistoricDataAction`);
the table is scoped to the page via the `hist_page_id` searchable.

Filters: **limit** and **search by user** (left); **date filter** and **actions filter**
(center, narrow by action type); **refresh** (right). The user filter is fed by
`getBackOfficeUsersAction` / `getBOUsersAction`. This tab is **read-only** (no action
buttons) — it displays the recorded history.

![Historic tab in the CMS page editor](./images/meliscmspagehistoric-page-tab-historic.png)
*Caption: the page editor's Historic tab — a table of the current page's actions (user,
action, date, description) with limit, user, date and action-type filters.*

---

### 3.2 Dashboard plugin — Recent User Activity

- **Plugin**: `src/Controller/DashboardPlugins/MelisCmsPageHistoricRecentUserActivityPlugin.php`
  (`recentActivityPages`)
- **Config**: `config/dashboard-plugins/MelisCmsPageHistoricRecentUserActivityPlugin.config.php`
- **View**: `view/melis-cms-page-historic/dashboard-plugins/recent-user-activity.phtml`
- Adds a **Recent User Activity** widget to the MelisCore back-office Dashboard (section
  *MelisCms*, icon `fa fa-users`), listing recent page activity across users.

![Recent User Activity dashboard widget](./images/meliscmspagehistoric-dashboard-plugin-recentactivity.png)
*Caption: the Recent User Activity dashboard widget — a list of recent page actions across
users.*

---

### 3.3 Recording the history (listeners)

History entries are written automatically by **listeners** registered in `src/Module.php`
(`onBootstrap`, back-office only). They hook the MelisCms page lifecycle and persist an entry
via the controller's `savePageHistoricAction` / the table gateway.

| Listener | Trigger | Role |
|---|---|---|
| `MelisPageHistoricPageEventListener` | MelisCms page events (save / publish / …) | Records a history entry for the action |
| `MelisPageHistoricDeletePageListener` | MelisCms page **delete** (`meliscms_page_delete_end`) | Records the deletion / cleans up the page's entries |

> Other modules can hook the same MelisCms page events (e.g. `meliscms_page_delete_end`,
> see README) to react to page lifecycle changes.

`deletePageHistoricAction` removes history entries (e.g. when a page is deleted).

### 3.4 Table gateway

The module has **no dedicated service class**; history is read/written through the table
gateway `MelisPageHistoricTable` (→ `melis_hist_page_historic`, in `src/Model/Tables/`),
plus the controller's save/delete actions.

---

## 4. Internationalization & diagnostic

- Translation files: `language/en_EN.interface.php`, `language/fr_FR.interface.php`,
  `language/en_EN.forms.php`, `language/fr_FR.forms.php`; keys use the
  `tr_melispagehistoric_*` prefix. Loaded via `Module::createTranslations()`.
- `config/diagnostic.config.php` — module health checks (Melis diagnostic system).
- The module applies its own back-office layout `layout/layoutMelisPageHistoric` on dispatch.

---

## 5. Front assets

Declared in `config/app.interface.php` (key `ressources`):

- **JS**: `public/js/melispagehistoric.js`
- **CSS**: `public/css/styles.css`
- **Compiled bundle**: `public/build/css/bundle.css`, `public/build/js/bundle.js`

---

## 6. Quick code map

```
melis-cms-page-historic/
├── composer.json                 → module dependencies & metadata (dbdeploy: true)
├── config/
│   ├── module.config.php         → routes, table, controller
│   ├── app.interface.php         → the Historic tab injected into the CMS page editor
│   ├── app.tools.php             → the history DataTable (columns + filters)
│   ├── app.forms.php             → forms (filters)
│   ├── diagnostic.config.php     → diagnostic tests
│   └── dashboard-plugins/        → Recent User Activity dashboard plugin config
├── src/
│   ├── Module.php                → bootstrap, listener wiring, layout, translations
│   ├── Controller/               → PageHistoricController (tab + data + save/delete), DashboardPlugins/
│   ├── Listener/                 → MelisPageHistoricPageEventListener, MelisPageHistoricDeletePageListener
│   └── Model/Tables/             → MelisPageHistoricTable
├── view/                         → .phtml templates (historic tab/table/filters, dashboard, layout)
├── public/                       → JS/CSS assets + bundles
├── language/                     → en_EN / fr_FR (interface + forms)
├── install/                      → SQL (structure, MWB model, dbdeploy migrations)
└── etc/                          → MarketPlace (xml) + MelisAI/doc (this doc)
```

---

## 7. Typical lifecycle

1. **A user edits a page** in MelisCms (save / publish / delete).
2. **A listener captures it** (`MelisPageHistoricPageEventListener` /
   `MelisPageHistoricDeletePageListener`) and writes a row to `melis_hist_page_historic`
   (page, action, date, user, description).
3. **Review per page**: open the **Historic** tab in the page editor → the table of that
   page's actions, filterable by user / date / action type.
4. **Review across pages**: the **Recent User Activity** dashboard widget shows recent
   actions by all users.
5. **Cleanup**: deleting a page records/cleans its entries (`deletePageHistoricAction`).

---

## 8. Screenshot index (for on-demand retrieval)

All screenshots live in `./images/` (i.e. `/etc/MelisAI/doc/images/`). This table is the
**filename → content** index the MelisAI MCP uses to fetch a specific screenshot on demand;
each row's caption in the body gives the text-only description of what the image shows.

| Image file | Content |
|---|---|
| `meliscmspagehistoric-page-tab-historic.png` | Historic tab in the CMS page editor (user/action/date/description table + filters) |
| `meliscmspagehistoric-dashboard-plugin-recentactivity.png` | Recent User Activity dashboard widget |

---

*Document for AI consumption (MelisAI MCP) — describes the `melisplatform/melis-cms-page-historic`
module. Last reviewed 2026-06-08 against the current source.*
