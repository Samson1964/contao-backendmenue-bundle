# Backend-Menü-Bundle für Contao 4.13 & 5.7

Erweiterung für **Contao 4.13** und **Contao 5.7** zur Verwaltung benutzerdefinierter Bereiche in der Backend-Menüspalte. Administratoren können neue Menübereiche mit eigenen Namen, Icons und Positionen anlegen sowie einzelne Backend-Module zwischen diesen Bereichen verschieben.

## Features

✅ **Benutzerdefinierte Menübereiche** — Erstelle beliebig viele neue Bereiche im Backend-Menü  
✅ **Frei konfigurierbare Icons** — Nutze Font Awesome 6 oder Contao-Standard-Icons  
✅ **Module umsortieren** — Verschiebe Backend-Module aus Standardbereichen in neue Bereiche  
✅ **Sortierbarkeit** — Definiere die Reihenfolge von Bereichen und Modulen  
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

1. Öffne **System → Backend-Menübereiche**
2. Klicke auf **Neuen Datensatz erstellen**
3. Gib einen Namen für den Bereich ein (z.B. „Meine Tools")
4. Wähle ein Icon (Font Awesome 6, z.B. `fa-tools`)
5. Definiere die Position (Sortierposition im Menü)
6. **Speichern**

#### Module zuordnen

1. Im Bereiche-Listing: Klick auf **Module verwalten** eines Bereichs
2. Klick auf **Neuen Datensatz erstellen**
3. Wähle ein Backend-Modul (z.B. `tl_page`, `tl_article`)
4. Definiere die Position des Moduls im Bereich
5. **Speichern**

Das ausgewählte Modul wird nun aus seinem Standardbereich entfernt und unter dem neuen Bereich angezeigt.

### Programmgesteuerte Nutzung

Der `BackendMenuManipulator`-Service kann auch direkt genutzt werden:

```php
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;

$manipulator = new BackendMenuManipulator();
$manipulator->manipulateBackendMenu();

// Das Backend-Menü ist nun neu organisiert
```

## Icons

Das Icon eines Bereichs wird links neben dem Bereichsnamen in der Backend-Navigation angezeigt. Drei Icon-Sätze stehen zur Auswahl:

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
| `icon` | varchar(255) | Icon-Name (Font Awesome oder Contao) |
| `position` | int | Sortierposition |
| `tstamp` | int | Änderungszeitstempel |
| `sorting` | int | Interne Sortierung |

### `tl_backendmenue_zuordnungen`

Verknüpft Backend-Module zu benutzerdefinierten Bereichen:

| Spalte | Typ | Beschreibung |
|--------|-----|-------------|
| `id` | int | Primärschlüssel |
| `pid` | int | Foreign Key → `tl_backendmenue_bereiche` |
| `module` | varchar(255) | Name des Backend-Moduls |
| `position` | int | Sortierposition im Bereich |
| `tstamp` | int | Änderungszeitstempel |
| `sorting` | int | Interne Sortierung |

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
3. **Keine Benutzergruppen-Filterung** — Die Menü-Anpassung gilt für alle Benutzer gleich
4. **Position** — Eigene Bereiche werden hinter den Standardbereichen einsortiert; die Position steuert die Reihenfolge der eigenen Bereiche untereinander

## Lizenz

GNU Lesser General Public License (LGPL 3.0 oder später)

## Support

Bugs und Feature-Requests: [GitHub Issues](https://github.com/samson1964/contao-backendmenue-bundle/issues)

---

**Autor:** Samson Hoppe  
**E-Mail:** hoppe.berlin@googlemail.com
