<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Service;

/**
 * Service für die Bereitstellung verfügbarer Icons (Font Awesome 6 & Contao-Standard).
 *
 * Stellt eine Liste von verfügbaren Icon-Namen bereit, die in den DCA-Feldern
 * als Optionen oder in der Icon-Picker-Komponente genutzt werden.
 */
class IconProvider
{
    /**
     * Gibt eine Liste der häufigsten Font Awesome 6 Icons zurück.
     *
     * Diese Icons werden als Shortcuts angezeigt; benutzerdefinierte Icons
     * können manuell eingegeben werden.
     *
     * @return array Assoziatives Array mit Icon-ID → Icon-Name
     */
    public function getFontAwesomeIcons(): array
    {
        return [
            'fa-star' => 'Star (Favorit)',
            'fa-heart' => 'Heart (Wichtig)',
            'fa-tools' => 'Tools (Werkzeuge)',
            'fa-wrench' => 'Wrench (Schraubenschlüssel)',
            'fa-cog' => 'Cog (Zahnrad / Einstellungen)',
            'fa-cube' => 'Cube (Würfel / Block)',
            'fa-cubes' => 'Cubes (Mehrere Blöcke)',
            'fa-layers' => 'Layers (Ebenen)',
            'fa-folder-open' => 'Folder Open (Offener Ordner)',
            'fa-folder' => 'Folder (Ordner)',
            'fa-folder-plus' => 'Folder Plus (Ordner erstellen)',
            'fa-file' => 'File (Datei)',
            'fa-file-pdf' => 'File PDF',
            'fa-file-image' => 'File Image (Bild)',
            'fa-image' => 'Image (Bild)',
            'fa-images' => 'Images (Bilder)',
            'fa-chart-line' => 'Chart Line (Liniendiagramm)',
            'fa-chart-bar' => 'Chart Bar (Balkendiagramm)',
            'fa-chart-pie' => 'Chart Pie (Kreisdiagramm)',
            'fa-list' => 'List (Liste)',
            'fa-list-ul' => 'List Unordered',
            'fa-list-ol' => 'List Ordered (Nummeriert)',
            'fa-table' => 'Table (Tabelle)',
            'fa-database' => 'Database (Datenbank)',
            'fa-server' => 'Server',
            'fa-user' => 'User (Benutzer)',
            'fa-users' => 'Users (Benutzer)',
            'fa-user-cog' => 'User Cog (Benutzerverwaltung)',
            'fa-user-tie' => 'User Tie (Admin)',
            'fa-bell' => 'Bell (Glocke / Benachrichtigungen)',
            'fa-envelope' => 'Envelope (Brief)',
            'fa-comment' => 'Comment (Kommentar)',
            'fa-comments' => 'Comments (Kommentare)',
            'fa-check' => 'Check (Häkchen)',
            'fa-times' => 'Times (Schließen)',
            'fa-trash' => 'Trash (Papierkorb)',
            'fa-edit' => 'Edit (Bearbeiten)',
            'fa-pencil' => 'Pencil (Stift)',
            'fa-lock' => 'Lock (Gesperrt)',
            'fa-unlock' => 'Unlock (Entsperrt)',
            'fa-key' => 'Key (Schlüssel)',
            'fa-shield' => 'Shield (Schutz)',
            'fa-bug' => 'Bug (Fehler)',
            'fa-code' => 'Code',
            'fa-terminal' => 'Terminal',
            'fa-play' => 'Play (Abspielen)',
            'fa-pause' => 'Pause',
            'fa-stop' => 'Stop (Stopp)',
            'fa-refresh' => 'Refresh (Aktualisieren)',
            'fa-sync' => 'Sync (Synchronisieren)',
            'fa-download' => 'Download',
            'fa-upload' => 'Upload',
            'fa-cloud' => 'Cloud (Wolke)',
            'fa-cloud-upload' => 'Cloud Upload',
            'fa-cloud-download' => 'Cloud Download',
            'fa-globe' => 'Globe (Globus / Website)',
            'fa-link' => 'Link (Verknüpfung)',
            'fa-chain' => 'Chain (Kette)',
            'fa-calendar' => 'Calendar (Kalender)',
            'fa-clock' => 'Clock (Uhr)',
            'fa-home' => 'Home (Startseite)',
            'fa-map' => 'Map (Karte)',
            'fa-compass' => 'Compass (Kompass)',
            'fa-search' => 'Search (Suche)',
            'fa-sliders-h' => 'Sliders (Schieberegler)',
            'fa-paint-brush' => 'Paint Brush (Pinsel)',
            'fa-palette' => 'Palette (Farbpalette)',
            'fa-eye' => 'Eye (Ansehen)',
            'fa-eye-slash' => 'Eye Slash (Verbergen)',
            'fa-thumbs-up' => 'Thumbs Up (Daumen hoch)',
            'fa-thumbs-down' => 'Thumbs Down (Daumen runter)',
        ];
    }

    /**
     * Gibt eine Liste der Contao Standard-Icons zurück.
     *
     * Diese Icons stammen aus dem Contao-Core und sind immer verfügbar.
     *
     * @return array Assoziatives Array mit Icon-Dateiname → Icon-Label
     */
    public function getContaoStandardIcons(): array
    {
        return [
            'settings.svg' => 'Settings (Einstellungen)',
            'page.svg' => 'Page (Seite)',
            'article.svg' => 'Article (Artikel)',
            'news.svg' => 'News (Nachrichten)',
            'calendar.svg' => 'Calendar (Kalender)',
            'gallery.svg' => 'Gallery (Galerie)',
            'table.svg' => 'Table (Tabelle)',
            'form.svg' => 'Form (Formular)',
            'user.svg' => 'User (Benutzer)',
            'group.svg' => 'Group (Gruppe)',
            'member.svg' => 'Member (Mitglied)',
            'login.svg' => 'Login',
            'database.svg' => 'Database (Datenbank)',
            'file.svg' => 'File (Datei)',
            'folder.svg' => 'Folder (Ordner)',
            'image.svg' => 'Image (Bild)',
            'html5.svg' => 'HTML5',
            'text.svg' => 'Text',
            'headline.svg' => 'Headline (Überschrift)',
            'module.svg' => 'Module (Modul)',
            'template.svg' => 'Template (Vorlage)',
            'css.svg' => 'CSS',
            'js.svg' => 'JavaScript',
            'config.svg' => 'Config (Konfiguration)',
            'cache.svg' => 'Cache',
            'log.svg' => 'Log (Protokoll)',
            'info.svg' => 'Info (Information)',
            'help.svg' => 'Help (Hilfe)',
            'update.svg' => 'Update (Aktualisierung)',
            'backup.svg' => 'Backup',
            'system.svg' => 'System',
            'maintenance.svg' => 'Maintenance (Wartung)',
        ];
    }

    /**
     * Gibt alle verfügbaren Icons kombiniert zurück (Font Awesome + Contao).
     *
     * @return array Assoziatives Array mit Icon-ID → Icon-Label
     */
    public function getAllIcons(): array
    {
        return \array_merge(
            $this->getFontAwesomeIcons(),
            $this->getContaoStandardIcons()
        );
    }

    /**
     * Gibt die verfügbaren Icons als Contao DCA-Optionen zurück.
     *
     * Gruppiert Font Awesome und Contao Standard Icons separat.
     *
     * @return array Assoziatives Array für DCA `options` oder `options_callback`
     */
    public function getIconsForDca(): array
    {
        return [
            'Font Awesome 6' => $this->getFontAwesomeIcons(),
            'Contao Standard' => $this->getContaoStandardIcons(),
        ];
    }

    /**
     * Gibt den Namen eines Icons validiert zurück.
     *
     * Prüft, ob das Icon in der Liste der verfügbaren Icons vorhanden ist.
     *
     * @param string $iconName Der Icon-Name zu validieren
     *
     * @return string Der validierte Icon-Name oder leerer String wenn ungültig
     */
    public function validateIcon(string $iconName): string
    {
        $allIcons = $this->getAllIcons();

        return isset($allIcons[$iconName]) ? $iconName : '';
    }
}
