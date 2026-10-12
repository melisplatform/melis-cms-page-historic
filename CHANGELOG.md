# Changelog

## v6.0.4 - 2026-09-23
### Security
* **security:** parameterised SQL for date filters, ORDER BY and hand-quoted values (audit item 11.0)

## v6.0.3 - 2026-08-20
### Security
* Gate mutating/data actions in legacy tool controllers (CWE-862)
### Docs
* **melisai:** React back-office AI documentation for MelisCmsPageHistoric

## v6.0.1 - 2026-08-10
### Dependencies & build
* **composer:** update docs/homepage links, swap zf2 keyword for laminas, bump php constraint to ^8.3|^8.5

## v6.0.0 - 2026-08-10
### Security
* **security:** add SECURITY.md (private vulnerability reporting policy)
### Added
* **marketplace:** add React back-office screenshots (etc/MarketPlace/images/react)
* **cms-react:** pagination serveur + dates localisées (analytics/historique/versioning) + badges & filtre historique
* **cms-react:** droits/capabilities éditeur de page CMS + polish
* **react:** onglet Historique natif pour l'editeur de page CMS (react-api)
* Add MelisAI module documentation for AI consumption
### Fixed
* **security:** harden legacy file/dir creation & output escaping
### Dependencies & build
* **composer:** bump melis-core/melis-engine/melis-front/melis-cms constraint to ^6.0
* Remove stale test/phpunit.xml
### Docs
* **meliscmspagehistoric:** rewrite as two-part doc (functional guide + technical reference with examples)

## v5.3.0 - 2024-09-25
### Added
* Add translations
### Fixed
* Fixed unfinished tool filter
### Changed
* Edit view file with javascript
* Update lang

## v5.2.0 - 2024-06-06
* Maintenance release.

## v5.1.0 - 2024-02-13
### Fixed
* Fix strftime error

## v5.0.1 - 2023-05-24
### Changed
* Converts table to utf8mb4

## v5.0.0 - 2022-06-22
### Changed
* Changed deprecated ArraySerializable to ArraySerializableHydrator
