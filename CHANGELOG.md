# Backend-Menü-Bundle Changelog

## Version 1.5.0 (2026-10-13)

* Add: Menübereiche haben eine **Geltung**. Ein globaler Bereich erscheint bei allen
  Benutzern, ein persönlicher nur bei den ausgewählten. Neue Felder `scope` und `users`
  in `tl_backendmenue_bereiche`.
* Add: Neues Feld „Globale Menübereiche ausblenden" in `tl_user`, einstellbar im eigenen
  Profil (Backend-Einstellungen) und in der Benutzerverwaltung. Ist es gesetzt, gelten für
  diesen Zugang nur die persönlichen Bereiche; die Module der globalen bleiben in ihren
  ursprünglichen Menübereichen.
* Add: Benutzer ohne Administratorrechte verwalten ihre eigenen Bereiche selbst. Sie sehen
  im Modul ausschließlich ihre persönlichen Bereiche, ein neu angelegter gehört automatisch
  ihnen, und die Geltung ist für sie nicht einstellbar. Die Beschränkung wirkt dreifach:
  über den Listen-Filter, die gekürzte Palette und eine Prüfung jeder ID in der Anfrage —
  ein fremder Bereich lässt sich also auch nicht über die Adresszeile öffnen.
* Add: Die Bereichs-Übersicht zeigt die Geltung als Spalte, bei persönlichen Bereichen mit
  den Namen der betroffenen Benutzer.
* Add: Neuer Service `AreaAccess` bündelt die Geltungs- und Rechtelogik, mit Tests.
* Change: Bereiche, aus denen das letzte für den Benutzer sichtbare Modul herausgenommen
  wurde, werden aus dem Menü entfernt. Contao legt solche Gruppen sonst an, füllt sie nicht
  und läuft anschließend in eine Warnung.
* Change: Die neuen Felder `scope` und `users` sind als `exclude` markiert, die übrigen
  ausdrücklich nicht — Contao 5.7 verweigert einem Benutzer sonst das Bearbeiten der ganzen
  Tabelle, sobald kein Feld ausdrücklich freigegeben ist.

**Hinweis zur Rechtevergabe:** Damit ein Benutzer ohne Administratorrechte das Modul nutzen
kann, braucht er neben der Freigabe des Moduls auch **Datensatzrechte** für
`tl_backendmenue_bereiche` und `tl_backendmenue_zuordnungen`. Ohne sie lässt sich das Modul
öffnen, aber kein Datensatz bearbeiten. Das ist das normale Rechtesystem von Contao.

## Version 1.4.1 (2026-10-13)

* Fix: Die Symbole für Bearbeiten, Verschieben, Löschen und Anzeigen steckten in der
  Modul-Übersicht hinter dem Aufklappmenü. Contao 5.7 zeigt nur als `primary` markierte
  Operationen direkt an, sobald überhaupt eine Operation primär ist — und das Backend
  hängt bei sortierbaren Tabellen selbst einen primären (unsichtbaren) Drag-Handle an.
  Alle Operationen beider Tabellen sind jetzt als `primary` markiert und damit dauerhaft
  sichtbar. Contao 4.13 kennt den Schlüssel nicht und ignoriert ihn.
* Change: Der in Contao 5.7 abgekündigte `child_record_callback` ist durch ein
  `label_callback` ersetzt (`DcaCallbackListener::formatAssignmentLabel()`). Contao 4.13
  erreicht es über den `else`-Zweig von `DC_Table::parentView()`; die beiden Fassungen
  rufen den Callback im Eltern-Modus mit unterschiedlich vielen Argumenten auf, weshalb
  `$dc` und `$args` optional sind.
* Add: `package-metadata.yml` für die Beschreibung im Contao Manager.

## Version 1.4.0 (2026-08-10)

* Add: Icon-Farbe frei wählbar (Farbwähler im Bereichs-Formular). Bei Bilddateien wird die
  Farbe per CSS-Maske auf die Silhouette angewendet, sodass sich einfarbige SVG beliebig
  umfärben lassen; bleibt das Feld leer, gilt weiterhin die Farbe des Backend-Themes.
* Add: Eigenes Icon aus der Dateiverwaltung wählbar (SVG, PNG, GIF) — umschaltbar über das
  neue Feld „Icon-Herkunft".
* Add: Reihenfolge der Module per Drag & Drop statt über ein Positionsfeld.
* Change: Die Modul-Zuordnungen nutzen jetzt das Contao-Standardfeld `sorting`; eine
  Migration überführt bestehende Positionswerte, bevor der Schema-Abgleich die alte Spalte
  entfernt.
* Change: Die Bereichs-Übersicht zeigt bei eigenen Icons den Dateipfad statt der rohen UUID.

## Version 1.3.0 (2026-08-10)

* Add: Bedienhinweis in der Bereichs-Übersicht — ein Bereich erscheint erst im Menü, wenn
  ihm mindestens ein Modul zugeordnet wurde.
* Add: Die Position zählt über das Gesamtmenü: 1 = ganz oben (vor den Standardbereichen),
  2 = zweite Stelle usw.; größere Werte hängen den Bereich ans Ende.
* Fix: Der Bereichsname war gegenüber den Standardbereichen nach rechts eingerückt. Die
  Icons liegen jetzt im Icon-Raster der Backend-Themes (`background` bei 3px 2px für SVG,
  absolut positioniertes `::before` für Schrift-Glyphen) statt als Inline-Element vor dem Text.

## Version 1.2.2 (2026-08-10)

* Fix: Die Menü-Manipulation lief über den Hook `initializeSystem`, den Contao nur auslöst,
  wenn das Verzeichnis `system/tmp` existiert — fehlte es, blieb die Manipulation
  stillschweigend aus. Sie hängt jetzt am `MenuEvent` (`contao.backend_menu_build`,
  Priorität 20), das in Contao 4.13 wie 5.7 unmittelbar vor dem Core-Listener läuft.

## Version 1.2.1 (2026-08-10)

* Fix: Die fehlende DependencyInjection-Extension ergänzt. Ohne sie wurde die
  `services.yaml` nie geladen, wodurch sämtliche Services, Hooks und DCA-Callbacks
  wirkungslos blieben (leere Icon- und Modul-Auswahl, keine Menü-Manipulation).

## Version 1.2.0 (2026-08-10)

* Add: Lucide-Icons als dritter Icon-Satz (132 Icons, Schrift aus dem offiziellen
  lucide-static-Paket, ISC-Lizenz).
* Change: Font Awesome von 5.5 auf 6.7.2 aktualisiert (92 Icons mit kanonischen FA6-Namen,
  Codepoints aus der Original-CSS extrahiert). Alte FA5-Namen wie `fa-cog` oder `fa-search`
  werden automatisch auf ihre FA6-Entsprechung aufgelöst, bestehende Werte bleiben gültig.
* Change: Icon-Schriften werden nur geladen, wenn mindestens ein Bereich sie nutzt.

## Version 1.1.0 (2026-08-10)

* Add: Die Icons der Bereiche erscheinen in der Backend-Navigation (als `::before` an den
  Gruppenköpfen, eingefügt über den Hook `parseBackendTemplate`).
* Add: Font Awesome Free (Solid) wird als Webfont mitgeliefert — es wird nichts von
  externen Servern geladen.
* Change: Die Icon-Listen gegen die echten Bestände geprüft: Font-Awesome-Icons mit
  verifizierten Unicode-Codepoints, 48 Contao-Theme-Icons aus der Schnittmenge von 4.13.58
  und 5.7.7. Die bisherigen Listen enthielten teils nicht existierende Namen.
* Change: Die Icon-Auswahl zeigt deutsche Bezeichnungen mit dem technischen Namen als
  Suchhilfe.

## Version 1.0.5 (2026-08-10)

* Fix: DataContainer-Klasse korrigiert auf `Contao\DC_Table`; die in 1.0.3 eingetragene
  Klasse `Contao\DataContainer\Table` existiert nicht. Behebt den ClassNotFoundError
  endgültig, kompatibel mit 4.13 und 5.x.
* Fix: Attribut-Namespace korrigiert auf `Contao\CoreBundle\DependencyInjection\Attribute`
  — durch den falschen Namespace wurden die Hooks nie registriert.
* Fix: Die Menü-Manipulation auf einen real existierenden Hook umgestellt; der zuvor
  verwendete Hook `loadBackendModule` gibt es in Contao nicht.
* Fix: Verwaisten `child_record_callback` auf eine gelöschte Inline-Klasse durch einen
  `#[AsCallback]`-Listener ersetzt, der auch die Icon- und Modul-Optionen liefert.
* Fix: Im Manipulator wurden Module erst aus dem Menü entfernt und danach gesucht —
  umgehängte Module gingen dadurch verloren.
* Fix: BE_MOD-Gruppenschlüssel auf `backendmenue_<id>` umgestellt; Bereichsnamen mit
  Leerzeichen oder Umlauten zerstörten als CSS-Klasse das Backend-Markup.
* Fix: Vor der ersten Migration fehlende Tabellen blockieren das Backend nicht mehr.
* Change: Operationen nach Konvention — `edit` öffnet die Modul-Zuordnungen, `editheader`
  bearbeitet den Bereich.
* Change: PHP-Anforderung auf `^8.1` gesetzt (readonly-Promotion), `contao/manager-plugin`
  als Abhängigkeit ergänzt, ungenutzte SQL-Datei entfernt.

## Version 1.0.4 (2026-08-10)

* Fix: Manuelle Hook-Registrierung entfernt, die beim `cache:warmup` zu
  „ArgumentCountError: Too few arguments" führte; die Registrierung läuft über das
  `#[AsHook]`-Attribut.

## Version 1.0.3 (2026-08-10)

* Fix: Versuch, den ClassNotFoundError über einen vollständigen DataContainer-Namespace
  zu beheben (in 1.0.5 richtiggestellt).

## Version 1.0.2 (2026-08-10)

* Fix: DCA-Callbacks aus den DCA-Dateien in einen Listener verschoben.

## Version 1.0.1 (2026-08-10)

* Fix: Deutsche Beschriftung des Backend-Moduls ergänzt.

## Version 1.0.0 (2026-08-09)

* Add: Backend-Modul zur Verwaltung eigener Menübereiche mit Name, Icon und Position.
* Add: Beliebige Backend-Module lassen sich den Bereichen zuordnen; die Standardbereiche
  von Contao bleiben erhalten.
* Add: Unterstützung für Contao 4.13 und 5.7, Tests für die Services, Dokumentation in der
  README.
