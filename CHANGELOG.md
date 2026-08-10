# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

## 1.2.0 (2026-08-10)

### Add
- Lucide-Icons als dritter Icon-Satz (132 Icons, Schrift aus dem offiziellen lucide-static-Paket, ISC-Lizenz)
- FA5-Alt-Namen (z. B. `fa-cog`, `fa-search`) werden automatisch auf ihre FA6-Entsprechung aufgelöst — bestehende Datenbankwerte bleiben gültig

### Change
- Font Awesome von 5.5 auf 6.7.2 aktualisiert (92 Icons mit kanonischen FA6-Namen, Codepoints aus der Original-CSS extrahiert)
- Icon-Schriften werden weiterhin nur geladen, wenn mindestens ein Bereich sie nutzt

## 1.1.0 (2026-08-10)

### Add
- Icons der Bereiche werden jetzt in der Backend-Navigation angezeigt (per ::before an den Gruppenköpfen, injiziert über den Hook parseBackendTemplate)
- Font Awesome Free 5.5 (Solid) wird als Webfont mitgeliefert — kein Laden von externen Servern
- Neuer BackendAssetsListener erzeugt die Icon-CSS-Regeln nur für bekannte Icons (Schutz vor beliebigen Datenbankwerten)

### Change
- Icon-Listen komplett überarbeitet und gegen die echten Bestände geprüft: 84 Font-Awesome-Icons mit verifizierten Unicode-Codepoints (inkl. Schach-Icons), 48 Contao-Theme-Icons aus der Schnittmenge von 4.13.58 und 5.7.7 — die bisherigen Listen enthielten teils nicht existierende Namen
- Icon-Auswahl zeigt deutsche Bezeichnungen mit technischem Namen als Suchhilfe

## 1.0.5 (2026-08-10)

### Fix
- DataContainer-Klasse korrigiert: `Contao\DC_Table` statt der nicht existierenden Klasse `Contao\DataContainer\Table` (behebt den ClassNotFoundError endgültig, kompatibel mit 4.13 und 5.x)
- Attribut-Namespace korrigiert: `Contao\CoreBundle\DependencyInjection\Attribute` statt des nicht existierenden `Contao\CoreBundle\Attributes` — die Hooks wurden dadurch nie registriert
- Menü-Manipulation auf den real existierenden Hook `initializeSystem` umgestellt (der Hook `loadBackendModule` existiert in Contao nicht); läuft nur noch bei Backend-Anfragen
- Verwaisten `child_record_callback` auf gelöschte Inline-Klasse durch `#[AsCallback]`-Listener ersetzt (DcaCallbackListener, liefert auch Icon- und Modul-Optionen)
- Logikfehler im BackendMenuManipulator: Module wurden erst aus dem Menü entfernt und dann gesucht — umgehängte Module gingen dadurch komplett verloren
- BE_MOD-Gruppenschlüssel auf `backendmenue_<id>` umgestellt (Bereichsnamen mit Leerzeichen/Umlauten zerstörten als CSS-Klasse das Backend-Markup)
- Schutz vor fehlenden Tabellen: Vor der ersten Migration bleibt das Backend nutzbar

### Change
- Operationen nach Konvention: `edit` öffnet die Modul-Zuordnungen, `editheader` bearbeitet den Bereich
- PHP-Anforderung ehrlich auf `^8.1` gesetzt (readonly-Promotion); `contao/manager-plugin` als Abhängigkeit ergänzt
- Ungenutzte SQL-Datei entfernt (das Schema stammt vollständig aus den DCA-Definitionen)

## 1.0.4 (2026-08-10)

### Fix
- Entferne manuelle Hook-Registrierung die zu Dependency-Injection-Fehlern führt
- Nutze Symfony `#[AsHook]` Attribute für korrekte Hook-Integration
- Behebt "ArgumentCountError: Too few arguments" beim cache:warmup

## 1.0.3 (2026-08-10)

### Fix
- Contao 5.7 Kompatibilität: DataContainer Namespace vollständig angeben (`Contao\DataContainer\Table`)
- Behebt "ClassNotFoundError: Attempted to load class Table from global namespace"

## 1.0.2 (2026-08-10)

### Fix
- Behobe "Class Table not found" Fehler durch Refactoring der DCA-Callbacks
- LoadDataContainerListener für korrekte Hook-Integration

## 1.0.1 (2026-08-10)

### Fix
- Deutsche Übersetzung für Backend-Modul-Label hinzugefügt

## 1.0.0 (2026-08-09)

### Add
- Initialer Release
- Backend-Modul zur Verwaltung benutzerdefinierter Menübereiche
- Icon-Picker mit Font Awesome 6 und Contao Standard Icons
- Möglichkeit, Backend-Module zwischen Bereichen zu verschieben
- Sortierbarkeit von Bereichen und Modulen
- Support für Contao 4.13 und 5.7
- PHPUnit Tests für Services
- Umfassende Dokumentation in README.md
