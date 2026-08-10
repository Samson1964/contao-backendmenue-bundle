<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FilesModel;
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;
use Schachbulle\BackendMenueBundle\Service\IconProvider;

/**
 * Injiziert die Icon-Styles für die benutzerdefinierten Menübereiche ins Backend.
 *
 * Die Gruppenköpfe der Backend-Navigation tragen in Contao 4.13 und 5.x die
 * CSS-Klasse "group-<schlüssel>" (gesetzt in BackendUser::navigation()) und
 * reservieren per padding-left einen 22 Pixel breiten Icon-Platz. Genau dort
 * wird das Icon als absolut positioniertes ::before-Element eingesetzt, damit
 * der Bereichsname bündig mit den Standardbereichen steht.
 *
 * Drei Darstellungsarten:
 *   - Schrift-Glyphen (Font Awesome, Lucide) über die mitgelieferten Webfonts
 *   - Bilder in Originalfarbe als background-image
 *   - Bilder eingefärbt über eine CSS-Maske, sodass die Silhouette der Datei
 *     in der gewählten Farbe erscheint (funktioniert bei SVG, PNG und GIF,
 *     weil alle drei einen Alphakanal mitbringen)
 *
 * Der Hook "parseBackendTemplate" existiert in beiden Contao-Fassungen; die
 * Hauptseite läuft dort als Template "be_main" durch. Relative URLs im
 * eingefügten <style>-Block funktionieren, weil das Backend eine <base>-URL setzt.
 */
#[AsHook('parseBackendTemplate')]
class BackendAssetsListener
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param BackendMenuManipulator $manipulator  Liefert die angelegten Bereiche samt Icon-Angaben
     * @param IconProvider           $iconProvider Übersetzt Icon-Namen in Codepoints bzw. Pfade
     * @param ContaoFramework        $framework    Zugriff auf FilesModel für eigene Icon-Dateien
     */
    public function __construct(
        private readonly BackendMenuManipulator $manipulator,
        private readonly IconProvider $iconProvider,
        private readonly ContaoFramework $framework,
    ) {
    }

    /**
     * Fügt die Icon-CSS-Regeln vor dem schließenden </head> ein.
     *
     * Es werden nur Regeln für Icons erzeugt, die der IconProvider kennt bzw.
     * für Dateien, die in der Dateiverwaltung existieren — unbrauchbare Werte
     * aus der Datenbank landen so niemals im Markup.
     *
     * @param string $buffer   Der gerenderte HTML-Puffer des Templates
     * @param string $template Der Name des Templates (z. B. "be_main")
     *
     * @return string Der (gegebenenfalls ergänzte) HTML-Puffer
     */
    public function __invoke(string $buffer, string $template): string
    {
        if ('be_main' !== $template) {
            return $buffer;
        }

        $areas = $this->manipulator->getAppliedAreas();

        if ([] === $areas) {
            return $buffer;
        }

        $rules = [];
        $needsFaFont = false;
        $needsLucideFont = false;

        foreach ($areas as $groupKey => $area) {
            $color = $this->sanitizeColor($area['iconColor'] ?? '');
            $declarations = null;

            if ('file' === ($area['iconType'] ?? 'library')) {
                $path = $this->resolveIconFile($area['iconFile'] ?? null);
                $declarations = null !== $path ? $this->buildImageDeclarations($path, $color) : null;
            } else {
                $icon = (string) ($area['icon'] ?? '');
                $faCodepoint = $this->iconProvider->getFaCodepoint($icon);
                $lucideCodepoint = $this->iconProvider->getLucideCodepoint($icon);

                if (null !== $faCodepoint) {
                    $needsFaFont = true;
                    $declarations = $this->buildGlyphDeclarations($faCodepoint, 'backendmenue-fa', 900, $color);
                } elseif (null !== $lucideCodepoint) {
                    $needsLucideFont = true;
                    $declarations = $this->buildGlyphDeclarations($lucideCodepoint, 'backendmenue-lucide', 400, $color);
                } else {
                    $path = $this->iconProvider->getContaoIconPath($icon);
                    $declarations = null !== $path ? $this->buildImageDeclarations($path, $color) : null;
                }
            }

            if (null === $declarations) {
                continue;
            }

            $link = '#tl_navigation a.group-' . $groupKey;

            $rules[] = $link . '{position:relative}';
            $rules[] = $link . '::before{' . $declarations . '}';
        }

        if ([] === $rules) {
            return $buffer;
        }

        $css = '';

        // Die Schriften nur laden, wenn sie tatsächlich gebraucht werden; die eigenen
        // font-family-Namen verhindern Kollisionen mit eventuell bereits
        // eingebundenen Icon-Schriften
        if ($needsFaFont) {
            $css .= '@font-face{font-family:"backendmenue-fa";font-weight:900;font-style:normal;'
                . 'font-display:block;src:url("bundles/backendmenue/fonts/fa-solid-900.woff2") format("woff2");}';
        }

        if ($needsLucideFont) {
            $css .= '@font-face{font-family:"backendmenue-lucide";font-weight:400;font-style:normal;'
                . 'font-display:block;src:url("bundles/backendmenue/fonts/lucide.woff2") format("woff2");}';
        }

        $style = "\n<style>/* contao-backendmenue-bundle */\n" . $css . implode("\n", $rules) . "\n</style>\n";

        return str_replace('</head>', $style . '</head>', $buffer);
    }

    /**
     * Baut die CSS-Deklarationen für ein Schrift-Glyph.
     *
     * @param string      $codepoint Der Unicode-Codepoint ohne Backslash, z. B. "f439"
     * @param string      $font      Der font-family-Name der einzubindenden Schrift
     * @param int         $weight    Das Schriftgewicht (900 für Font Awesome Solid, 400 für Lucide)
     * @param string|null $color     Die geprüfte Hex-Farbe ohne Raute, oder null für die Theme-Farbe
     *
     * @return string Die CSS-Deklarationen ohne umschließende Klammern
     */
    private function buildGlyphDeclarations(string $codepoint, string $font, int $weight, ?string $color): string
    {
        return 'content:"\\' . $codepoint . '";font-family:"' . $font . '";font-weight:' . $weight . ';'
            . 'font-style:normal;position:absolute;left:3px;top:2px;width:16px;height:16px;'
            . 'font-size:13px;line-height:16px;text-align:center;'
            . (null !== $color ? 'color:#' . $color . ';' : '');
    }

    /**
     * Baut die CSS-Deklarationen für ein Bild-Icon.
     *
     * Ohne Farbe wird das Bild unverändert als Hintergrund gezeichnet. Mit
     * Farbe dient es als Maske: Gezeichnet wird dann eine Fläche in der
     * gewählten Farbe, die auf die Silhouette des Bildes beschnitten wird.
     * Das -webkit-Präfix bleibt für ältere Safari-Fassungen erhalten.
     *
     * @param string      $path  Der Pfad relativ zum Projektverzeichnis
     * @param string|null $color Die geprüfte Hex-Farbe ohne Raute, oder null für Originalfarben
     *
     * @return string Die CSS-Deklarationen ohne umschließende Klammern
     */
    private function buildImageDeclarations(string $path, ?string $color): string
    {
        $url = 'url("' . $this->encodePath($path) . '")';

        $base = 'content:"";position:absolute;left:3px;top:2px;width:16px;height:16px;';

        if (null === $color) {
            return $base . 'background:' . $url . ' center/contain no-repeat;';
        }

        return $base . 'background-color:#' . $color . ';'
            . '-webkit-mask:' . $url . ' center/contain no-repeat;'
            . 'mask:' . $url . ' center/contain no-repeat;';
    }

    /**
     * Prüft eine Farbangabe aus der Datenbank.
     *
     * @param string $color Der Rohwert des Feldes iconColor
     *
     * @return string|null Die Hex-Farbe ohne Raute, oder null wenn nichts
     *                     gesetzt ist oder der Wert kein gültiger Hex-Code ist
     */
    private function sanitizeColor(string $color): ?string
    {
        $color = ltrim(trim($color), '#');

        if (!preg_match('/^(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color)) {
            return null;
        }

        return $color;
    }

    /**
     * Bereitet einen Dateipfad für die Verwendung in url() auf.
     *
     * Jedes Pfadsegment wird einzeln kodiert, damit Leerzeichen und Umlaute
     * in Dateinamen nicht das CSS zerbrechen und kein Anführungszeichen aus
     * der url()-Angabe ausbrechen kann.
     *
     * @param string $path Der Pfad relativ zum Projektverzeichnis
     *
     * @return string Der kodierte Pfad
     */
    private function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    /**
     * Löst die UUID einer Icon-Datei in einen Pfad auf.
     *
     * @param mixed $uuid Die binäre UUID aus dem fileTree-Feld, oder null
     *
     * @return string|null Der Pfad relativ zum Projektverzeichnis, oder null
     *                     wenn keine UUID gesetzt ist, die Datei nicht mehr
     *                     existiert oder ihre Endung nicht erlaubt ist
     */
    private function resolveIconFile($uuid): ?string
    {
        if (empty($uuid)) {
            return null;
        }

        $model = $this->framework->getAdapter(FilesModel::class)->findByUuid($uuid);

        if (null === $model || '' === (string) $model->path) {
            return null;
        }

        // Nur Bildformate mit Alphakanal zulassen — dieselbe Beschränkung wie
        // im DCA, hier aber gegen manipulierte Datenbankwerte abgesichert
        if (!preg_match('/\.(?:svg|png|gif)$/i', $model->path)) {
            return null;
        }

        return $model->path;
    }
}
