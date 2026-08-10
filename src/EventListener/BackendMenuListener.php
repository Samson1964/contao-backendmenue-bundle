<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listener für die Manipulation der Backend-Menüstruktur.
 *
 * Läuft über den Hook "initializeSystem", weil zu diesem Zeitpunkt alle
 * config.php-Dateien der Bundles geladen sind und $GLOBALS['BE_MOD'] damit
 * vollständig befüllt ist — aber noch bevor das Backend-Menü gerendert wird.
 * Der zuvor verwendete Hook "loadBackendModule" existiert in Contao nicht.
 */
#[AsHook('initializeSystem')]
class BackendMenuListener
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param BackendMenuManipulator $manipulator  Der Service zur Menü-Manipulation
     * @param ScopeMatcher           $scopeMatcher Erkennt, ob die Anfrage ans Backend geht
     * @param RequestStack           $requestStack Zugriff auf die aktuelle Anfrage
     */
    public function __construct(
        private readonly BackendMenuManipulator $manipulator,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Manipuliert das Backend-Menü nach der System-Initialisierung.
     *
     * Läuft nur bei Backend-Anfragen — im Frontend und auf der Kommandozeile
     * (z. B. cache:warmup) wird nichts getan, damit dort weder unnötige
     * Datenbankabfragen laufen noch der Cache-Aufbau scheitert.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request || !$this->scopeMatcher->isBackendRequest($request)) {
            return;
        }

        $this->manipulator->manipulateBackendMenu();
    }
}
