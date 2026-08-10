<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;
use Schachbulle\BackendMenueBundle\Service\IconProvider;

/**
 * Injiziert die Icon-Styles für die benutzerdefinierten Menübereiche ins Backend.
 *
 * Die Gruppenköpfe der Backend-Navigation tragen in Contao 4.13 und 5.x die
 * CSS-Klasse "group-<schlüssel>" (gesetzt in BackendUser::navigation()); darüber
 * bekommt jeder Bereich sein Icon als ::before-Element. Font-Awesome-Icons werden
 * über die mitgelieferte Schrift gerendert, Contao-Icons als SVG-Hintergrundbild
 * aus dem "flexible"-Backend-Theme.
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
     * @param BackendMenuManipulator $manipulator  Liefert die angelegten Bereiche samt Icons
     * @param IconProvider           $iconProvider Übersetzt Icon-Namen in Codepoints bzw. Pfade
     */
    public function __construct(
        private readonly BackendMenuManipulator $manipulator,
        private readonly IconProvider $iconProvider,
    ) {
    }

    /**
     * Fügt die Icon-CSS-Regeln vor dem schließenden </head> ein.
     *
     * Es werden nur Regeln für Icons erzeugt, die der IconProvider kennt —
     * unbekannte Werte aus der Datenbank landen so niemals im Markup.
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
        $needsFont = false;

        foreach ($areas as $groupKey => $icon) {
            $selector = '#tl_navigation a.group-' . $groupKey . '::before';
            $codepoint = $this->iconProvider->getFaCodepoint($icon);

            if (null !== $codepoint) {
                $needsFont = true;
                $rules[] = $selector . '{content:"\\' . $codepoint . '";font-family:"backendmenue-icons";'
                    . 'font-weight:900;font-style:normal;display:inline-block;width:18px;margin-right:4px;'
                    . 'text-align:center;line-height:1;}';
                continue;
            }

            $path = $this->iconProvider->getContaoIconPath($icon);

            if (null !== $path) {
                $rules[] = $selector . '{content:"";display:inline-block;width:14px;height:14px;'
                    . 'margin-right:6px;vertical-align:-2px;'
                    . 'background:url("' . $path . '") center/contain no-repeat;}';
            }
        }

        if ([] === $rules) {
            return $buffer;
        }

        $css = '';

        // Die Schrift nur laden, wenn mindestens ein Font-Awesome-Icon gebraucht wird;
        // der eigene font-family-Name verhindert Kollisionen mit einem eventuell
        // bereits eingebundenen Font Awesome
        if ($needsFont) {
            $css .= '@font-face{font-family:"backendmenue-icons";font-weight:900;font-style:normal;'
                . 'font-display:block;src:url("bundles/backendmenue/fonts/fa-solid-900.woff2") format("woff2");}';
        }

        $style = "\n<style>/* contao-backendmenue-bundle */\n" . $css . implode("\n", $rules) . "\n</style>\n";

        return str_replace('</head>', $style . '</head>', $buffer);
    }
}
