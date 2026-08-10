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
 * bekommt jeder Bereich sein Icon als ::before-Element. Font-Awesome- und
 * Lucide-Icons werden über die mitgelieferten Schriften gerendert, Contao-Icons
 * als SVG-Hintergrundbild aus dem "flexible"-Backend-Theme.
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
        $needsFaFont = false;
        $needsLucideFont = false;

        foreach ($areas as $groupKey => $icon) {
            $selector = '#tl_navigation a.group-' . $groupKey . '::before';
            $faCodepoint = $this->iconProvider->getFaCodepoint($icon);

            if (null !== $faCodepoint) {
                $needsFaFont = true;
                $rules[] = $selector . '{content:"\\' . $faCodepoint . '";font-family:"backendmenue-fa";'
                    . 'font-weight:900;font-style:normal;display:inline-block;width:18px;margin-right:4px;'
                    . 'text-align:center;line-height:1;}';
                continue;
            }

            $lucideCodepoint = $this->iconProvider->getLucideCodepoint($icon);

            if (null !== $lucideCodepoint) {
                $needsLucideFont = true;
                $rules[] = $selector . '{content:"\\' . $lucideCodepoint . '";font-family:"backendmenue-lucide";'
                    . 'font-weight:400;font-style:normal;display:inline-block;width:18px;margin-right:4px;'
                    . 'text-align:center;line-height:1;vertical-align:-1px;}';
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
}
