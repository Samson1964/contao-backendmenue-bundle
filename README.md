# Backend-Menü-Bundle für Contao 4.13 & 5.7

Erweiterung für **Contao 4.13** und **Contao 5.7** zur Verwaltung eigener Bereiche in der Backend-Menüspalte. Bereiche tragen einen eigenen Namen, ein Icon in frei wählbarer Farbe und eine Position; beliebige Backend-Module lassen sich darin zusammenfassen. Bereiche gelten entweder global für alle Benutzer oder persönlich für einzelne — und jeder Benutzer kann die globalen Bereiche für seinen Zugang abschalten.

## Features

✅ **Eigene Menübereiche** — Beliebig viele neue Bereiche im Backend-Menü  
✅ **Global oder persönlich** — Bereiche für alle, oder nur für einzelne Benutzer  
✅ **Abschaltbar** — Jeder Benutzer kann die globalen Bereiche in seinem Profil ausblenden  
✅ **Frei konfigurierbare Icons** — Font Awesome 6, Lucide, Contao-Icons oder eigene Bilddatei, mit wählbarer Farbe  
✅ **Module umsortieren** — Backend-Module aus den Standardbereichen in eigene Bereiche verschieben  
✅ **Drag & Drop** — Reihenfolge der Module mit der Maus festlegen  
✅ **Standardbereiche erhalten** — Alle Standard-Menübereiche (System, Inhalte etc.) bleiben bestehen  
✅ **Kompatibilität** — Läuft unter Contao 4.13 LTS und 5.7 LTS  

## Installation

```bash
composer require schachbulle/contao-backendmenue-bundle
```

Nach der Installation die Bundle-Registrierung aktualisieren:

```bash
php vendor/bin/contao-console cache:clear
php vendor/bin/contao-console contao:migrate
```

## Verwendung

### Im Backend

1. Öffne **System → Eigenes Backend-Menü**
2. Klicke auf **Neu**
3. Gib einen Namen für den Bereich ein (z.B. „Meine Tools")
4. Wähle ein Icon (Font Awesome 6, Lucide oder Contao Standard)
5. Definiere die Position im Backend-Menü: **1 = ganz oben**, 2 = an zweiter Stelle usw.; größere Werte hängen den Bereich ans Ende
6. **Speichern**

> **Wichtig:** Ein Bereich erscheint erst dann im Backend-Menü, wenn ihm über „Module verwalten" mindestens ein Modul zugeordnet wurde — leere Gruppen blendet Contao grundsätzlich aus. Ein entsprechender Hinweis wird auch in der Bereichs-Übersicht angezeigt.

#### Module zuordnen

1. Im Bereiche-Listing: Klick auf **Module verwalten** eines Bereichs
2. Klick auf **Neu**
3. Wähle ein Backend-Modul (die Auswahl ist nach den aktuellen Menübereichen gruppiert)
4. **Speichern**

Das ausgewählte Modul wird nun aus seinem Standardbereich entfernt und unter dem neuen Bereich angezeigt.

Die **Reihenfolge der Module** innerhalb eines Bereichs wird in dieser Übersicht per **Drag & Drop** festgelegt — einfach am Anfasser ziehen. Die Reihenfolge schlägt sofort auf das Backend-Menü durch.

### Globale und persönliche Bereiche

Unter **Geltung** wird festgelegt, für wen ein Bereich gilt:

- **Global** — der Bereich erscheint bei allen Benutzern, sofern sie die globalen Bereiche nicht in ihrem Profil ausgeblendet haben.
- **Persönlich** — der Bereich erscheint nur bei den ausgewählten Benutzern. Diese dürfen ihn auch selbst bearbeiten.

Jeder Benutzer kann in seinem Profil (**Benutzerprofil → Backend-Einstellungen**) die Einstellung **„Globale Menübereiche ausblenden"** setzen. Dann gelten für ihn nur noch seine persönlichen Bereiche; die Module der globalen Bereiche bleiben in ihren ursprünglichen Menübereichen. Administratoren können denselben Schalter in der Benutzerverwaltung setzen.

#### Benutzer verwalten ihre Bereiche selbst

Ein Benutzer ohne Administratorrechte sieht im Modul ausschließlich seine eigenen Bereiche und kann nur diese bearbeiten; legt er einen neuen an, gehört dieser automatisch ihm. Die Geltung ist für ihn nicht einstellbar. Administratoren sehen und bearbeiten alle Bereiche.

Damit ein Benutzer ohne Administratorrechte das Modul nutzen kann, braucht er in seiner Benutzergruppe (oder in seinen eigenen Rechten) **zwei** Freigaben:

1. **Module** → „Eigenes Backend-Menü"
2. **Datensatzrechte** → für `tl_backendmenue_bereiche` und `tl_backendmenue_zuordnungen` jeweils Anlegen, Bearbeiten und Löschen

Fehlt die zweite Freigabe, lässt sich das Modul öffnen, aber kein Datensatz bearbeiten (Contao antwortet mit „Forbidden"). Das ist das normale Rechtesystem von Contao und gilt für alle Tabellen gleichermaßen.

### Programmgesteuerte Nutzung

Der `BackendMenuManipulator`-Service kann auch direkt genutzt werden:

```php
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;

$manipulator = new BackendMenuManipulator();
$manipulator->manipulateBackendMenu();

// Das Backend-Menü ist nun neu organisiert
```

## Icons

Das Icon eines Bereichs wird links neben dem Bereichsnamen in der Backend-Navigation angezeigt. Unter **Icon-Herkunft** wählst du zwischen einer der mitgelieferten Sammlungen und einer **eigenen Bilddatei**.

### Icon-Farbe

Das Feld **Icon-Farbe** ist optional; bleibt es leer, gilt die Standardfarbe des Backend-Themes. Bei Bilddateien wird die Farbe nicht einfach überlagert, sondern auf die **Silhouette** des Bildes angewendet (CSS-Maske) — ein einfarbiges SVG lässt sich damit beliebig umfärben.

### Eigene Bilddatei

Erlaubt sind **SVG, PNG und GIF** aus der Dateiverwaltung. Am besten quadratisch und einfarbig, dargestellt wird sie in 16 × 16 Pixeln. Wird zusätzlich eine Icon-Farbe gesetzt, dient die Datei als Maske — deshalb sind nur Formate mit Alphakanal zugelassen.

### Mitgelieferte Sammlungen

Drei Icon-Sätze stehen zur Auswahl:

### Font Awesome 6 (mitgeliefert)
- Präfix: `fa-`, z. B. `fa-chess`, `fa-gear`, `fa-trophy`, `fa-users`
- Die Schrift (Font Awesome Free 6.7.2, Solid) liegt dem Bundle bei — es wird nichts von externen Servern geladen
- Alt-Namen aus Font Awesome 5 (z. B. `fa-cog`, `fa-search`) werden automatisch auf ihre FA6-Entsprechung aufgelöst

### Lucide (mitgeliefert)
- Präfix: `lucide-`, z. B. `lucide-crown`, `lucide-settings`, `lucide-swords`
- Die Schrift stammt aus dem offiziellen lucide-static-Paket (ISC-Lizenz) und liegt dem Bundle bei
- Feiner Outline-Stil, der gut zur Contao-5-Optik passt

### Contao Standard Icons
- Direkt der Icon-Dateiname: `settings.svg`, `article.svg`, `filemanager.svg`
- Quelle: Backend-Theme „flexible" (`system/themes/flexible/icons/`)
- Die Auswahl enthält nur Icons, die in Contao 4.13 **und** 5.7 existieren

Alle Icon-Schriften werden per `::before`-Regel mit eigenen `font-family`-Namen gerendert, sodass es keine Konflikte mit eventuell vorhandenen Icon-Schriften gibt. Geladen wird eine Schrift nur, wenn mindestens ein Bereich sie tatsächlich nutzt.

### Beispiel-Icons

| Icon | Beschreibung |
|------|-------------|
| `fa-chess` | Schach (FA6) |
| `fa-gear` | Zahnrad (FA6) |
| `lucide-crown` | Krone (Lucide) |
| `lucide-swords` | Schwerter (Lucide) |
| `settings.svg` | Einstellungen (Contao) |
| `filemanager.svg` | Dateiverwaltung (Contao) |

## Datenbank-Tabellen

Das Bundle erzeugt automatisch zwei Tabellen:

### `tl_backendmenue_bereiche`

Speichert benutzerdefinierte Menübereiche:

| Spalte | Typ | Beschreibung |
|--------|-----|-------------|
| `id` | int | Primärschlüssel |
| `name` | varchar(255) | Name des Bereichs |
| `iconType` | varchar(16) | `library` (Sammlung) oder `file` (eigene Datei) |
| `icon` | varchar(255) | Icon-Name aus einer der Sammlungen |
| `iconFile` | binary(16) | UUID der eigenen Bilddatei |
| `iconColor` | varchar(6) | Hex-Farbe ohne Raute, leer = Theme-Standard |
| `position` | int | Position im Backend-Menü (1 = ganz oben) |
| `scope` | varchar(16) | `global` oder `user` (persönlich) |
| `users` | blob | Bei persönlichen Bereichen: die zugeordneten Benutzer |
| `tstamp` | int | Änderungszeitstempel |
| `sorting` | int | Interne Sortierung |

### `tl_backendmenue_zuordnungen`

Verknüpft Backend-Module zu benutzerdefinierten Bereichen:

| Spalte | Typ | Beschreibung |
|--------|-----|-------------|
| `id` | int | Primärschlüssel |
| `pid` | int | Foreign Key → `tl_backendmenue_bereiche` |
| `module` | varchar(255) | Name des Backend-Moduls |
| `tstamp` | int | Änderungszeitstempel |
| `sorting` | int | Reihenfolge im Bereich (per Drag & Drop gepflegt) |

### Ergänzung an `tl_user`

| Spalte | Typ | Beschreibung |
|--------|-----|-------------|
| `backendmenueHideGlobal` | char(1) | Globale Menübereiche für diesen Zugang ausblenden |

## Architektur

### Service: `BackendMenuManipulator`

Kern-Service, der die Menüstruktur zur Laufzeit manipuliert:

- Lädt benutzerdefinierte Bereiche aus der Datenbank
- Lädt Modul-Zuordnungen
- Rekonstruiert das Backend-Menü basierend auf Konfiguration
- Entfernt Module aus Standard-Bereichen und ordnet sie neuen Bereichen zu

**Methoden:**
- `manipulateBackendMenu()` — Hauptmethode, manipuliert `$GLOBALS['BE_MOD']`

### EventListener: `BackendMenuListener`

Symfony-Listener, der auf den Contao-Hook `loadBackendModule` reagiert:

- Wird beim Laden eines Backend-Moduls aufgerufen
- Ruft den `BackendMenuManipulator` auf
- Integriert sich nahtlos in Contaos Hook-System

### DCA-Dateien

- **`tl_backendmenue_bereiche.php`** — Definiert das Formular für Menübereiche
- **`tl_backendmenue_zuordnungen.php`** — Definiert das Formular für Modul-Zuordnungen

## Entwicklung

### Tests ausführen

```bash
php vendor/bin/phpunit
```

### Code-Style prüfen

```bash
php vendor/bin/ecs check src
```

### PHP-Syntax prüfen

```bash
php -l src/**/*.php
```

## Kompatibilität

| Contao Version | Status | Anmerkung |
|---|---|---|
| 4.13 LTS | ✅ Unterstützt | Vollständig getestet |
| 5.0–5.6 | ⚠️ Wahrscheinlich | Benötigt Tests |
| 5.7 LTS | ✅ Unterstützt | Vollständig getestet |

**PHP-Versionen:** 7.4, 8.0, 8.1, 8.2, 8.3

## Bekannte Limitierungen

1. **Keine Verschachtelung** — Menübereiche können nicht verschachtelt werden
2. **Keine Zuordnung von Custom-Modulen** — Nur vordefinierte Module aus `$GLOBALS['BE_MOD']` können zugeordnet werden
3. **Keine Benutzergruppen-Zuordnung** — Ein persönlicher Bereich wird einzelnen Benutzern zugewiesen, nicht einer ganzen Benutzergruppe

## Lizenz

GNU Lesser General Public License (LGPL 3.0 oder später)

## Support

Bugs und Feature-Requests: [GitHub Issues](https://github.com/samson1964/contao-backendmenue-bundle/issues)

---

**Autor:** Samson Hoppe  
**E-Mail:** hoppe.berlin@googlemail.com
