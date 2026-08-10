<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Schachbulle\BackendMenueBundle\Service\IconProvider;

/**
 * Stellt alle DCA-Callbacks des Bundles als Symfony-Service bereit.
 *
 * Ersetzt die früheren Inline-Klassen in den DCA-Dateien: Die Registrierung
 * läuft über das #[AsCallback]-Attribut, das in Contao 4.13 und 5.x unter
 * demselben Namespace verfügbar ist.
 */
class DcaCallbackListener
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param IconProvider $iconProvider Der Service für die Icon-Listen
     */
    public function __construct(
        private readonly IconProvider $iconProvider,
    ) {
    }

    /**
     * Zeigt in der Bereichs-Übersicht einen Bedienhinweis an.
     *
     * Ein Bereich erscheint erst im Backend-Menü, wenn ihm mindestens ein
     * Modul zugeordnet wurde — das ist gewolltes Verhalten (Contao blendet
     * leere Gruppen aus), aber ohne Hinweis ein Stolperstein. Die Meldung
     * erscheint nur in der Listenansicht, nicht in den Bearbeitungsformularen.
     *
     * @return void
     */
    #[AsCallback(table: 'tl_backendmenue_bereiche', target: 'config.onload')]
    public function addUsageHint(): void
    {
        if ('' !== (string) Input::get('act')) {
            return;
        }

        Message::addInfo(
            $GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['usageHint']
                ?? 'Ein Bereich erscheint erst dann im Backend-Menü, wenn ihm über „Module verwalten" mindestens ein Modul zugeordnet wurde.'
        );
    }

    /**
     * Liefert die Optionen für das Icon-Auswahlfeld eines Bereichs.
     *
     * @return array Icons gruppiert nach "Font Awesome 6" und "Contao Standard"
     */
    #[AsCallback(table: 'tl_backendmenue_bereiche', target: 'fields.icon.options')]
    public function getIconOptions(): array
    {
        return $this->iconProvider->getIconsForDca();
    }

    /**
     * Liefert die Liste aller registrierten Backend-Module als Auswahl-Optionen.
     *
     * Iteriert über $GLOBALS['BE_MOD'] und gruppiert die Module nach ihrem
     * aktuellen Menübereich, damit die Auswahl im Backend nachvollziehbar bleibt.
     *
     * @return array Modul-Namen gruppiert nach Bereichs-Schlüssel
     */
    #[AsCallback(table: 'tl_backendmenue_zuordnungen', target: 'fields.module.options')]
    public function getModuleOptions(): array
    {
        $options = [];

        foreach (($GLOBALS['BE_MOD'] ?? []) as $group => $modules) {
            if (!\is_array($modules)) {
                continue;
            }

            foreach (array_keys($modules) as $moduleName) {
                $groupLabel = $GLOBALS['TL_LANG']['MOD'][$group] ?? $group;

                if (\is_array($groupLabel)) {
                    $groupLabel = $groupLabel[0] ?? $group;
                }

                $options[$groupLabel][$moduleName] = $GLOBALS['TL_LANG']['MOD'][$moduleName][0] ?? $moduleName;
            }
        }

        return $options;
    }

    /**
     * Rendert eine Modul-Zuordnung in der Eltern-Ansicht (child_record_callback).
     *
     * @param array $row Die Datensatz-Zeile aus tl_backendmenue_zuordnungen
     *
     * @return string HTML für den Listeneintrag
     */
    #[AsCallback(table: 'tl_backendmenue_zuordnungen', target: 'list.sorting.child_record')]
    public function listAssignment(array $row): string
    {
        $label = $GLOBALS['TL_LANG']['MOD'][$row['module']][0] ?? $row['module'];

        return '<div class="tl_content_left">' . StringUtil::specialchars($label)
            . ' <span class="tl_gray">[' . StringUtil::specialchars($row['module']) . ', Position '
            . (int) $row['position'] . ']</span></div>';
    }
}
