<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Service;

/**
 * Service für die Bereitstellung verfügbarer Icons (Font Awesome & Contao-Standard).
 *
 * Beide Listen sind gegen die tatsächlich vorhandenen Bestände geprüft:
 * Die Font-Awesome-Codepoints stammen aus der mitgelieferten Free-5.5-Schrift
 * (src/Resources/public/fonts/fa-solid-900.woff2), die Contao-Icons sind die
 * Schnittmenge der "flexible"-Theme-Icons von Contao 4.13.58 und 5.7.7 —
 * Namen außerhalb dieser Listen würden im Backend-Menü schlicht nichts anzeigen.
 */
class IconProvider
{
    /**
     * Font-Awesome-Icons: Name => [deutsches Label, Unicode-Codepoint].
     *
     * Die Codepoints wurden aus der Original-CSS von Font Awesome Free 5.5.0
     * extrahiert und gehören zur Solid-Schnittfamilie (font-weight 900).
     */
    private const FA_ICONS = [
        'fa-star' => ['Stern', 'f005'],
        'fa-heart' => ['Herz', 'f004'],
        'fa-wrench' => ['Schraubenschlüssel', 'f0ad'],
        'fa-cog' => ['Zahnrad', 'f013'],
        'fa-cube' => ['Würfel', 'f1b2'],
        'fa-cubes' => ['Würfel (mehrere)', 'f1b3'],
        'fa-layer-group' => ['Ebenen', 'f5fd'],
        'fa-folder' => ['Ordner', 'f07b'],
        'fa-folder-open' => ['Ordner (offen)', 'f07c'],
        'fa-folder-plus' => ['Ordner (neu)', 'f65e'],
        'fa-file' => ['Datei', 'f15b'],
        'fa-file-pdf' => ['PDF-Datei', 'f1c1'],
        'fa-file-image' => ['Bilddatei', 'f1c5'],
        'fa-image' => ['Bild', 'f03e'],
        'fa-images' => ['Bilder', 'f302'],
        'fa-chart-line' => ['Liniendiagramm', 'f201'],
        'fa-chart-bar' => ['Balkendiagramm', 'f080'],
        'fa-chart-pie' => ['Kreisdiagramm', 'f200'],
        'fa-list' => ['Liste', 'f03a'],
        'fa-list-ul' => ['Liste (Punkte)', 'f0ca'],
        'fa-list-ol' => ['Liste (nummeriert)', 'f0cb'],
        'fa-table' => ['Tabelle', 'f0ce'],
        'fa-database' => ['Datenbank', 'f1c0'],
        'fa-server' => ['Server', 'f233'],
        'fa-user' => ['Benutzer', 'f007'],
        'fa-users' => ['Benutzer (mehrere)', 'f0c0'],
        'fa-user-cog' => ['Benutzerverwaltung', 'f4fe'],
        'fa-user-tie' => ['Funktionär', 'f508'],
        'fa-bell' => ['Glocke', 'f0f3'],
        'fa-envelope' => ['Briefumschlag', 'f0e0'],
        'fa-comment' => ['Kommentar', 'f075'],
        'fa-comments' => ['Kommentare', 'f086'],
        'fa-check' => ['Haken', 'f00c'],
        'fa-times' => ['Kreuz', 'f00d'],
        'fa-trash' => ['Papierkorb', 'f1f8'],
        'fa-edit' => ['Bearbeiten', 'f044'],
        'fa-pencil-alt' => ['Stift', 'f303'],
        'fa-lock' => ['Schloss (zu)', 'f023'],
        'fa-unlock' => ['Schloss (offen)', 'f09c'],
        'fa-key' => ['Schlüssel', 'f084'],
        'fa-shield-alt' => ['Schild', 'f3ed'],
        'fa-bug' => ['Käfer (Fehler)', 'f188'],
        'fa-code' => ['Quellcode', 'f121'],
        'fa-terminal' => ['Terminal', 'f120'],
        'fa-play' => ['Abspielen', 'f04b'],
        'fa-pause' => ['Pause', 'f04c'],
        'fa-stop' => ['Stopp', 'f04d'],
        'fa-sync' => ['Synchronisieren', 'f021'],
        'fa-download' => ['Herunterladen', 'f019'],
        'fa-upload' => ['Hochladen', 'f093'],
        'fa-cloud' => ['Wolke', 'f0c2'],
        'fa-cloud-upload-alt' => ['Wolke (hochladen)', 'f382'],
        'fa-cloud-download-alt' => ['Wolke (herunterladen)', 'f381'],
        'fa-globe' => ['Globus', 'f0ac'],
        'fa-link' => ['Verknüpfung', 'f0c1'],
        'fa-calendar' => ['Kalender', 'f133'],
        'fa-calendar-alt' => ['Kalender (Tage)', 'f073'],
        'fa-clock' => ['Uhr', 'f017'],
        'fa-home' => ['Haus', 'f015'],
        'fa-map' => ['Karte', 'f279'],
        'fa-compass' => ['Kompass', 'f14e'],
        'fa-search' => ['Lupe', 'f002'],
        'fa-sliders-h' => ['Schieberegler', 'f1de'],
        'fa-paint-brush' => ['Pinsel', 'f1fc'],
        'fa-palette' => ['Farbpalette', 'f53f'],
        'fa-eye' => ['Auge', 'f06e'],
        'fa-eye-slash' => ['Auge (durchgestrichen)', 'f070'],
        'fa-thumbs-up' => ['Daumen hoch', 'f164'],
        'fa-thumbs-down' => ['Daumen runter', 'f165'],
        'fa-chess' => ['Schach', 'f439'],
        'fa-chess-board' => ['Schachbrett', 'f43c'],
        'fa-chess-knight' => ['Springer', 'f441'],
        'fa-chess-rook' => ['Turm', 'f447'],
        'fa-chess-king' => ['König', 'f43f'],
        'fa-chess-queen' => ['Dame', 'f445'],
        'fa-chess-pawn' => ['Bauer', 'f443'],
        'fa-trophy' => ['Pokal', 'f091'],
        'fa-newspaper' => ['Zeitung', 'f1ea'],
        'fa-address-book' => ['Adressbuch', 'f2b9'],
        'fa-gavel' => ['Richterhammer', 'f0e3'],
        'fa-flag' => ['Flagge', 'f024'],
        'fa-bullhorn' => ['Megafon', 'f0a1'],
        'fa-graduation-cap' => ['Ausbildung', 'f19d'],
        'fa-handshake' => ['Handschlag', 'f2b5'],
    ];

    /**
     * Contao-Standard-Icons aus dem "flexible"-Backend-Theme: Dateiname => Label.
     *
     * Enthält nur Icons, die sowohl in Contao 4.13 als auch in 5.7 existieren.
     */
    private const CONTAO_ICONS = [
        'admin.svg' => 'Administrator',
        'alert.svg' => 'Warnung',
        'article.svg' => 'Artikel',
        'articles.svg' => 'Artikel (Liste)',
        'clipboard.svg' => 'Zwischenablage',
        'contao.svg' => 'Contao-Logo',
        'content.svg' => 'Inhalte',
        'debug.svg' => 'Fehlersuche',
        'diff.svg' => 'Vergleich',
        'edit.svg' => 'Stift (Bearbeiten)',
        'editor.svg' => 'Editor',
        'featured.svg' => 'Stern (hervorgehoben)',
        'filemanager.svg' => 'Dateiverwaltung',
        'filemounts.svg' => 'Dateiablage',
        'folderC.svg' => 'Ordner',
        'group.svg' => 'Benutzergruppe',
        'header.svg' => 'Kopfzeile',
        'help.svg' => 'Hilfe',
        'hints.svg' => 'Hinweise',
        'important.svg' => 'Wichtig',
        'layout.svg' => 'Seitenlayout',
        'lock-locked.svg' => 'Schloss',
        'magnify.svg' => 'Lupe',
        'manager.svg' => 'Verwaltung',
        'manual.svg' => 'Handbuch',
        'member.svg' => 'Mitglied',
        'mgroup.svg' => 'Mitgliedergruppe',
        'modules.svg' => 'Module',
        'monitor.svg' => 'Bildschirm',
        'new.svg' => 'Neu (Plus)',
        'newfolder.svg' => 'Neuer Ordner',
        'ok.svg' => 'Haken',
        'person.svg' => 'Person',
        'preview.svg' => 'Vorschau',
        'profile.svg' => 'Profil',
        'regular.svg' => 'Seite',
        'rss.svg' => 'RSS-Feed',
        'settings.svg' => 'Einstellungen',
        'share.svg' => 'Teilen',
        'show.svg' => 'Auge (Anzeigen)',
        'sizes.svg' => 'Bildgrößen',
        'store.svg' => 'Speicher',
        'sync.svg' => 'Synchronisieren',
        'tablewizard.svg' => 'Tabelle',
        'themes.svg' => 'Themes',
        'user.svg' => 'Benutzer',
        'visible.svg' => 'Sichtbar',
        'wrench.svg' => 'Schraubenschlüssel',
    ];

    /**
     * Gibt die Font-Awesome-Icons als Auswahl-Liste zurück.
     *
     * @return array Icon-Name => Label (mit Icon-Name als Zusatz für die Suche)
     */
    public function getFontAwesomeIcons(): array
    {
        $icons = [];

        foreach (self::FA_ICONS as $name => [$label]) {
            $icons[$name] = $label . ' (' . $name . ')';
        }

        return $icons;
    }

    /**
     * Gibt die Contao-Standard-Icons als Auswahl-Liste zurück.
     *
     * @return array Icon-Dateiname => Label (mit Dateiname als Zusatz für die Suche)
     */
    public function getContaoStandardIcons(): array
    {
        $icons = [];

        foreach (self::CONTAO_ICONS as $file => $label) {
            $icons[$file] = $label . ' (' . $file . ')';
        }

        return $icons;
    }

    /**
     * Gibt alle verfügbaren Icons kombiniert zurück (Font Awesome + Contao).
     *
     * @return array Icon-ID => Label
     */
    public function getAllIcons(): array
    {
        return array_merge($this->getFontAwesomeIcons(), $this->getContaoStandardIcons());
    }

    /**
     * Gibt die verfügbaren Icons als gruppierte DCA-Optionen zurück.
     *
     * @return array Optionen gruppiert nach "Font Awesome" und "Contao Standard"
     */
    public function getIconsForDca(): array
    {
        return [
            'Font Awesome' => $this->getFontAwesomeIcons(),
            'Contao Standard' => $this->getContaoStandardIcons(),
        ];
    }

    /**
     * Liefert den Unicode-Codepoint eines Font-Awesome-Icons.
     *
     * @param string $icon Der Icon-Name, z. B. "fa-chess"
     *
     * @return string|null Der Codepoint ohne Backslash (z. B. "f439"),
     *                     oder null wenn es kein bekanntes FA-Icon ist
     */
    public function getFaCodepoint(string $icon): ?string
    {
        return self::FA_ICONS[$icon][1] ?? null;
    }

    /**
     * Liefert den relativen URL-Pfad eines Contao-Standard-Icons.
     *
     * Der Pfad ist relativ zur Basis-URL des Backends; das Verzeichnis
     * system/themes/flexible/ ist in Contao 4.13 und 5.x öffentlich verlinkt.
     *
     * @param string $icon Der Icon-Dateiname, z. B. "settings.svg"
     *
     * @return string|null Der Pfad, oder null wenn es kein bekanntes Contao-Icon ist
     */
    public function getContaoIconPath(string $icon): ?string
    {
        if (!isset(self::CONTAO_ICONS[$icon])) {
            return null;
        }

        return 'system/themes/flexible/icons/' . $icon;
    }

    /**
     * Gibt den Namen eines Icons validiert zurück.
     *
     * @param string $iconName Der zu prüfende Icon-Name
     *
     * @return string Der validierte Icon-Name oder leerer String wenn unbekannt
     */
    public function validateIcon(string $iconName): string
    {
        if (isset(self::FA_ICONS[$iconName]) || isset(self::CONTAO_ICONS[$iconName])) {
            return $iconName;
        }

        return '';
    }
}
