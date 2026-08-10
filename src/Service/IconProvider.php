<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Service;

/**
 * Service für die Bereitstellung verfügbarer Icons (Font Awesome 6, Lucide, Contao-Standard).
 *
 * Alle Listen sind gegen die tatsächlich mitgelieferten Bestände geprüft:
 * Die Font-Awesome-Codepoints stammen aus der CSS von Font Awesome Free 6.7.2,
 * die Lucide-Codepoints aus dem offiziellen lucide-static-Paket (beide Schriften
 * liegen unter src/Resources/public/fonts/), die Contao-Icons sind die Schnittmenge
 * der "flexible"-Theme-Icons von Contao 4.13.58 und 5.7.7. Namen außerhalb dieser
 * Listen würden im Backend-Menü schlicht nichts anzeigen.
 */
class IconProvider
{
    /**
     * Font-Awesome-6-Icons (Solid): Name => [deutsches Label, Unicode-Codepoint].
     */
    private const FA_ICONS = [
        'fa-star' => ['Stern', 'f005'],
        'fa-heart' => ['Herz', 'f004'],
        'fa-wrench' => ['Schraubenschlüssel', 'f0ad'],
        'fa-gear' => ['Zahnrad', 'f013'],
        'fa-screwdriver-wrench' => ['Werkzeuge', 'f7d9'],
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
        'fa-chart-column' => ['Säulendiagramm', 'e0e3'],
        'fa-chart-pie' => ['Kreisdiagramm', 'f200'],
        'fa-list' => ['Liste', 'f03a'],
        'fa-list-ul' => ['Liste (Punkte)', 'f0ca'],
        'fa-list-ol' => ['Liste (nummeriert)', 'f0cb'],
        'fa-table' => ['Tabelle', 'f0ce'],
        'fa-database' => ['Datenbank', 'f1c0'],
        'fa-server' => ['Server', 'f233'],
        'fa-user' => ['Benutzer', 'f007'],
        'fa-users' => ['Benutzer (mehrere)', 'f0c0'],
        'fa-user-gear' => ['Benutzerverwaltung', 'f4fe'],
        'fa-user-tie' => ['Funktionär', 'f508'],
        'fa-bell' => ['Glocke', 'f0f3'],
        'fa-envelope' => ['Briefumschlag', 'f0e0'],
        'fa-comment' => ['Kommentar', 'f075'],
        'fa-comments' => ['Kommentare', 'f086'],
        'fa-check' => ['Haken', 'f00c'],
        'fa-xmark' => ['Kreuz', 'f00d'],
        'fa-trash' => ['Papierkorb', 'f1f8'],
        'fa-pen-to-square' => ['Bearbeiten', 'f044'],
        'fa-pencil' => ['Stift', 'f303'],
        'fa-lock' => ['Schloss (zu)', 'f023'],
        'fa-unlock' => ['Schloss (offen)', 'f09c'],
        'fa-key' => ['Schlüssel', 'f084'],
        'fa-shield' => ['Schild', 'f132'],
        'fa-shield-halved' => ['Schild (geteilt)', 'f3ed'],
        'fa-bug' => ['Käfer (Fehler)', 'f188'],
        'fa-code' => ['Quellcode', 'f121'],
        'fa-terminal' => ['Terminal', 'f120'],
        'fa-play' => ['Abspielen', 'f04b'],
        'fa-pause' => ['Pause', 'f04c'],
        'fa-stop' => ['Stopp', 'f04d'],
        'fa-arrows-rotate' => ['Synchronisieren', 'f021'],
        'fa-download' => ['Herunterladen', 'f019'],
        'fa-upload' => ['Hochladen', 'f093'],
        'fa-cloud' => ['Wolke', 'f0c2'],
        'fa-cloud-arrow-up' => ['Wolke (hochladen)', 'f0ee'],
        'fa-cloud-arrow-down' => ['Wolke (herunterladen)', 'f0ed'],
        'fa-globe' => ['Globus', 'f0ac'],
        'fa-link' => ['Verknüpfung', 'f0c1'],
        'fa-calendar' => ['Kalender', 'f133'],
        'fa-calendar-days' => ['Kalender (Tage)', 'f073'],
        'fa-clock' => ['Uhr', 'f017'],
        'fa-house' => ['Haus', 'f015'],
        'fa-map' => ['Karte', 'f279'],
        'fa-compass' => ['Kompass', 'f14e'],
        'fa-magnifying-glass' => ['Lupe', 'f002'],
        'fa-sliders' => ['Schieberegler', 'f1de'],
        'fa-paintbrush' => ['Pinsel', 'f1fc'],
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
        'fa-medal' => ['Medaille', 'f5a2'],
        'fa-newspaper' => ['Zeitung', 'f1ea'],
        'fa-address-book' => ['Adressbuch', 'f2b9'],
        'fa-gavel' => ['Richterhammer', 'f0e3'],
        'fa-flag' => ['Flagge', 'f024'],
        'fa-bullhorn' => ['Megafon', 'f0a1'],
        'fa-graduation-cap' => ['Ausbildung', 'f19d'],
        'fa-handshake' => ['Handschlag', 'f2b5'],
        'fa-scale-balanced' => ['Waage', 'f24e'],
        'fa-lightbulb' => ['Glühbirne', 'f0eb'],
        'fa-rocket' => ['Rakete', 'f135'],
        'fa-puzzle-piece' => ['Puzzleteil', 'f12e'],
    ];

    /**
     * Alt-Namen aus Font Awesome 5 => kanonischer FA6-Name.
     *
     * Damit bleiben Datenbankwerte aus Bundle-Fassungen vor 1.2.0 gültig,
     * die noch FA5-Namen gespeichert haben.
     */
    private const FA_ALIASES = [
        'fa-cog' => 'fa-gear',
        'fa-tools' => 'fa-screwdriver-wrench',
        'fa-times' => 'fa-xmark',
        'fa-edit' => 'fa-pen-to-square',
        'fa-pencil-alt' => 'fa-pencil',
        'fa-shield-alt' => 'fa-shield-halved',
        'fa-sync' => 'fa-arrows-rotate',
        'fa-cloud-upload-alt' => 'fa-cloud-arrow-up',
        'fa-cloud-download-alt' => 'fa-cloud-arrow-down',
        'fa-calendar-alt' => 'fa-calendar-days',
        'fa-home' => 'fa-house',
        'fa-search' => 'fa-magnifying-glass',
        'fa-sliders-h' => 'fa-sliders',
        'fa-paint-brush' => 'fa-paintbrush',
        'fa-user-cog' => 'fa-user-gear',
    ];

    /**
     * Lucide-Icons: Name => [deutsches Label, Unicode-Codepoint].
     *
     * Die Codepoints stammen aus der lucide.css des lucide-static-Pakets
     * und gehören zur mitgelieferten Schrift lucide.woff2.
     */
    private const LUCIDE_ICONS = [
        'lucide-activity' => ['Aktivität', 'e038'],
        'lucide-alarm-clock' => ['Wecker', 'e03a'],
        'lucide-archive' => ['Archiv', 'e041'],
        'lucide-award' => ['Auszeichnung', 'e04f'],
        'lucide-banknote' => ['Geldschein', 'e052'],
        'lucide-bell' => ['Glocke', 'e059'],
        'lucide-book' => ['Buch', 'e05e'],
        'lucide-book-open' => ['Buch (offen)', 'e05f'],
        'lucide-bookmark' => ['Lesezeichen', 'e060'],
        'lucide-box' => ['Kiste', 'e061'],
        'lucide-boxes' => ['Kisten', 'e2d0'],
        'lucide-briefcase' => ['Aktentasche', 'e062'],
        'lucide-brush' => ['Pinsel', 'e1d3'],
        'lucide-bug' => ['Käfer (Fehler)', 'e20c'],
        'lucide-building' => ['Gebäude', 'e1cc'],
        'lucide-building-2' => ['Gebäude (Hochhaus)', 'e290'],
        'lucide-calendar' => ['Kalender', 'e063'],
        'lucide-calendar-days' => ['Kalender (Tage)', 'e2b9'],
        'lucide-camera' => ['Kamera', 'e064'],
        'lucide-castle' => ['Burg', 'e3e0'],
        'lucide-check' => ['Haken', 'e06c'],
        'lucide-chart-bar' => ['Balkendiagramm', 'e2a2'],
        'lucide-chart-line' => ['Liniendiagramm', 'e2a5'],
        'lucide-chart-pie' => ['Kreisdiagramm', 'e06b'],
        'lucide-clipboard' => ['Zwischenablage', 'e085'],
        'lucide-clock' => ['Uhr', 'e087'],
        'lucide-cloud' => ['Wolke', 'e088'],
        'lucide-cloud-download' => ['Wolke (herunterladen)', 'e089'],
        'lucide-cloud-upload' => ['Wolke (hochladen)', 'e091'],
        'lucide-code' => ['Quellcode', 'e093'],
        'lucide-cog' => ['Zahnrad', 'e30b'],
        'lucide-compass' => ['Kompass', 'e09b'],
        'lucide-contact' => ['Kontakt', 'e09c'],
        'lucide-cpu' => ['Prozessor', 'e0a9'],
        'lucide-crown' => ['Krone', 'e1d6'],
        'lucide-database' => ['Datenbank', 'e0ad'],
        'lucide-dices' => ['Würfel (Spiel)', 'e2c5'],
        'lucide-download' => ['Herunterladen', 'e0b2'],
        'lucide-eye' => ['Auge', 'e0ba'],
        'lucide-eye-off' => ['Auge (aus)', 'e0bb'],
        'lucide-file' => ['Datei', 'e0c0'],
        'lucide-file-text' => ['Textdatei', 'e0cc'],
        'lucide-files' => ['Dateien', 'e0cf'],
        'lucide-film' => ['Film', 'e0d0'],
        'lucide-filter' => ['Filter', 'e0dc'],
        'lucide-flag' => ['Flagge', 'e0d1'],
        'lucide-folder' => ['Ordner', 'e0d7'],
        'lucide-folder-open' => ['Ordner (offen)', 'e247'],
        'lucide-folder-plus' => ['Ordner (neu)', 'e0d9'],
        'lucide-gamepad-2' => ['Gamepad', 'e0df'],
        'lucide-gauge' => ['Tacho', 'e1bf'],
        'lucide-gavel' => ['Richterhammer', 'e0e0'],
        'lucide-gift' => ['Geschenk', 'e0e1'],
        'lucide-globe' => ['Globus', 'e0e8'],
        'lucide-graduation-cap' => ['Ausbildung', 'e234'],
        'lucide-hammer' => ['Hammer', 'e0ec'],
        'lucide-handshake' => ['Handschlag', 'e5c0'],
        'lucide-heart' => ['Herz', 'e0f2'],
        'lucide-house' => ['Haus', 'e0f5'],
        'lucide-image' => ['Bild', 'e0f6'],
        'lucide-images' => ['Bilder', 'e5c4'],
        'lucide-inbox' => ['Posteingang', 'e0f7'],
        'lucide-info' => ['Information', 'e0f9'],
        'lucide-key' => ['Schlüssel', 'e0fd'],
        'lucide-landmark' => ['Amtsgebäude', 'e23a'],
        'lucide-layers' => ['Ebenen', 'e529'],
        'lucide-layout-dashboard' => ['Dashboard', 'e1c1'],
        'lucide-library' => ['Bibliothek', 'e100'],
        'lucide-lightbulb' => ['Glühbirne', 'e1c2'],
        'lucide-link' => ['Verknüpfung', 'e102'],
        'lucide-list' => ['Liste', 'e106'],
        'lucide-list-ordered' => ['Liste (nummeriert)', 'e1d1'],
        'lucide-lock' => ['Schloss (zu)', 'e10b'],
        'lucide-lock-open' => ['Schloss (offen)', 'e10c'],
        'lucide-mail' => ['E-Mail', 'e10f'],
        'lucide-map' => ['Karte', 'e110'],
        'lucide-map-pin' => ['Kartenmarker', 'e111'],
        'lucide-medal' => ['Medaille', 'e36f'],
        'lucide-megaphone' => ['Megafon', 'e235'],
        'lucide-menu' => ['Menü', 'e115'],
        'lucide-message-circle' => ['Nachricht (rund)', 'e116'],
        'lucide-message-square' => ['Nachricht (eckig)', 'e117'],
        'lucide-monitor' => ['Bildschirm', 'e11d'],
        'lucide-moon' => ['Mond', 'e11e'],
        'lucide-newspaper' => ['Zeitung', 'e348'],
        'lucide-package' => ['Paket', 'e129'],
        'lucide-palette' => ['Farbpalette', 'e1dd'],
        'lucide-paperclip' => ['Büroklammer', 'e12d'],
        'lucide-pencil' => ['Stift', 'e1f9'],
        'lucide-phone' => ['Telefon', 'e133'],
        'lucide-play' => ['Abspielen', 'e13c'],
        'lucide-printer' => ['Drucker', 'e141'],
        'lucide-puzzle' => ['Puzzleteil', 'e29c'],
        'lucide-rocket' => ['Rakete', 'e286'],
        'lucide-rss' => ['RSS-Feed', 'e14a'],
        'lucide-save' => ['Speichern', 'e14d'],
        'lucide-scale' => ['Waage', 'e212'],
        'lucide-search' => ['Lupe', 'e151'],
        'lucide-send' => ['Senden', 'e152'],
        'lucide-server' => ['Server', 'e153'],
        'lucide-settings' => ['Einstellungen', 'e154'],
        'lucide-share-2' => ['Teilen', 'e156'],
        'lucide-shield' => ['Schild', 'e158'],
        'lucide-shield-check' => ['Schild (Haken)', 'e1ff'],
        'lucide-shopping-cart' => ['Einkaufswagen', 'e15c'],
        'lucide-sliders-horizontal' => ['Schieberegler', 'e29a'],
        'lucide-smartphone' => ['Smartphone', 'e163'],
        'lucide-sparkles' => ['Funkeln', 'e412'],
        'lucide-star' => ['Stern', 'e176'],
        'lucide-sun' => ['Sonne', 'e178'],
        'lucide-sword' => ['Schwert', 'e2b3'],
        'lucide-swords' => ['Schwerter', 'e2b4'],
        'lucide-table' => ['Tabelle', 'e17d'],
        'lucide-tag' => ['Etikett', 'e17f'],
        'lucide-target' => ['Zielscheibe', 'e180'],
        'lucide-terminal' => ['Terminal', 'e181'],
        'lucide-thumbs-up' => ['Daumen hoch', 'e18a'],
        'lucide-thumbs-down' => ['Daumen runter', 'e189'],
        'lucide-ticket' => ['Ticket', 'e20f'],
        'lucide-timer' => ['Timer', 'e1e0'],
        'lucide-trash-2' => ['Papierkorb', 'e18e'],
        'lucide-trending-up' => ['Aufwärtstrend', 'e191'],
        'lucide-trophy' => ['Pokal', 'e373'],
        'lucide-truck' => ['Lastwagen', 'e194'],
        'lucide-upload' => ['Hochladen', 'e19e'],
        'lucide-user' => ['Benutzer', 'e19f'],
        'lucide-user-cog' => ['Benutzerverwaltung', 'e342'],
        'lucide-users' => ['Benutzer (mehrere)', 'e1a4'],
        'lucide-video' => ['Video', 'e1a5'],
        'lucide-wallet' => ['Geldbörse', 'e204'],
        'lucide-wrench' => ['Schraubenschlüssel', 'e1b1'],
        'lucide-zap' => ['Blitz', 'e1b4'],
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
     * Gibt die Lucide-Icons als Auswahl-Liste zurück.
     *
     * @return array Icon-Name => Label (mit Icon-Name als Zusatz für die Suche)
     */
    public function getLucideIcons(): array
    {
        $icons = [];

        foreach (self::LUCIDE_ICONS as $name => [$label]) {
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
     * Gibt alle verfügbaren Icons kombiniert zurück.
     *
     * @return array Icon-ID => Label
     */
    public function getAllIcons(): array
    {
        return array_merge(
            $this->getFontAwesomeIcons(),
            $this->getLucideIcons(),
            $this->getContaoStandardIcons()
        );
    }

    /**
     * Gibt die verfügbaren Icons als gruppierte DCA-Optionen zurück.
     *
     * @return array Optionen gruppiert nach Icon-Satz
     */
    public function getIconsForDca(): array
    {
        return [
            'Font Awesome 6' => $this->getFontAwesomeIcons(),
            'Lucide' => $this->getLucideIcons(),
            'Contao Standard' => $this->getContaoStandardIcons(),
        ];
    }

    /**
     * Liefert den Unicode-Codepoint eines Font-Awesome-Icons.
     *
     * Alt-Namen aus Font Awesome 5 (z. B. "fa-cog") werden auf ihren
     * kanonischen FA6-Namen aufgelöst.
     *
     * @param string $icon Der Icon-Name, z. B. "fa-chess"
     *
     * @return string|null Der Codepoint ohne Backslash (z. B. "f439"),
     *                     oder null wenn es kein bekanntes FA-Icon ist
     */
    public function getFaCodepoint(string $icon): ?string
    {
        $icon = self::FA_ALIASES[$icon] ?? $icon;

        return self::FA_ICONS[$icon][1] ?? null;
    }

    /**
     * Liefert den Unicode-Codepoint eines Lucide-Icons.
     *
     * @param string $icon Der Icon-Name, z. B. "lucide-crown"
     *
     * @return string|null Der Codepoint ohne Backslash (z. B. "e1d6"),
     *                     oder null wenn es kein bekanntes Lucide-Icon ist
     */
    public function getLucideCodepoint(string $icon): ?string
    {
        return self::LUCIDE_ICONS[$icon][1] ?? null;
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
        if (
            isset(self::FA_ICONS[$iconName])
            || isset(self::FA_ALIASES[$iconName])
            || isset(self::LUCIDE_ICONS[$iconName])
            || isset(self::CONTAO_ICONS[$iconName])
        ) {
            return $iconName;
        }

        return '';
    }
}
