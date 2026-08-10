<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\Attributes\AsHook;

/**
 * Listener für die Initialisierung des Systems.
 *
 * Stellt sicher, dass die DCA-Dateien korrekt geladen sind.
 */
#[AsHook('initializeSystem')]
class InitializeSystemListener
{
    /**
     * Wird beim Initialisieren des Systems aufgerufen.
     *
     * Lädt die DCA-Dateien und registriert sie im Contao-System.
     *
     * @return void
     */
    public function __invoke(): void
    {
        // Lade die DCA-Dateien explizit
        $GLOBALS['TL_DCA']['tl_backendmenue_bereiche'] = $GLOBALS['TL_DCA']['tl_backendmenue_bereiche'] ?? [];
        $GLOBALS['TL_DCA']['tl_backendmenue_zuordnungen'] = $GLOBALS['TL_DCA']['tl_backendmenue_zuordnungen'] ?? [];
    }
}
