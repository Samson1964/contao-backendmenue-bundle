# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

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
