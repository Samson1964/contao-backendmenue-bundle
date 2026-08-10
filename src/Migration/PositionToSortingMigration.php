<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Überführt die alte Positionsnummer der Modul-Zuordnungen in das Sortierfeld.
 *
 * Bis Fassung 1.3.0 wurde die Reihenfolge der Module über ein eigenes Feld
 * "position" gepflegt; seit 1.4.0 übernimmt das Contao-Standardfeld "sorting",
 * das im Backend per Drag & Drop verändert wird. Diese Migration läuft vor
 * dem Schema-Abgleich, der die Spalte "position" anschließend entfernt —
 * sonst ginge die vom Benutzer festgelegte Reihenfolge verloren.
 */
class PositionToSortingMigration extends AbstractMigration
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param Connection $connection Die Doctrine-Verbindung zur Contao-Datenbank
     */
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Prüft, ob die Migration nötig ist.
     *
     * Ausschlaggebend ist bewusst der Datenbestand und nicht das Schema: Die
     * Spalte "position" verschwindet erst, wenn der Schema-Abgleich mit
     * --with-deletes läuft. Eine Prüfung auf ihr bloßes Vorhandensein würde
     * sonst dauerhaft wahr bleiben und die Migration bei jedem Aufruf erneut
     * anbieten.
     *
     * @return bool True, wenn noch Zuordnungen mit alter Position und ohne
     *              Sortierwert existieren
     */
    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_backendmenue_zuordnungen'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_backendmenue_zuordnungen');

        if (!isset($columns['position'], $columns['sorting'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM tl_backendmenue_zuordnungen WHERE sorting = 0 AND position > 0'
        );
    }

    /**
     * Schreibt die alten Positionswerte in das Sortierfeld.
     *
     * Contao vergibt Sortierwerte üblicherweise in 128er-Schritten, damit
     * zwischen zwei Datensätzen Platz zum Einfügen bleibt; die alten
     * Positionsnummern werden entsprechend hochgerechnet. Übergangen werden
     * Datensätze, die bereits einen Sortierwert besitzen.
     *
     * @return MigrationResult Das Ergebnis mit einer Meldung für die Konsole
     */
    public function run(): MigrationResult
    {
        $count = $this->connection->executeStatement(
            'UPDATE tl_backendmenue_zuordnungen SET sorting = position * 128 WHERE sorting = 0 AND position > 0'
        );

        return $this->createResult(
            true,
            \sprintf('%d Modul-Zuordnung(en) von "position" auf "sorting" umgestellt.', $count)
        );
    }
}
