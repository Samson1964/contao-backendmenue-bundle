<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\Event\MenuEvent;
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Listener für die Manipulation der Backend-Menüstruktur.
 *
 * Reagiert auf das MenuEvent, mit dem Contao (4.13 wie 5.x) das Backend-Menü
 * aufbaut. Die Priorität 20 liegt bewusst über der des Core-Listeners
 * (Priorität 10, baut den Menübaum aus $GLOBALS['BE_MOD']): So wird BE_MOD
 * umgebaut, unmittelbar bevor der Core daraus das Menü erzeugt.
 *
 * Der frühere Weg über den Hook "initializeSystem" hatte eine versteckte
 * Falle: Contao feuert diesen Hook nur, wenn das Verzeichnis system/tmp
 * existiert (ContaoFramework::triggerInitializeSystemHook) — fehlt es,
 * bleibt die Manipulation stillschweigend aus. Das MenuEvent kennt keine
 * solche Bedingung und feuert außerdem nur bei Backend-Anfragen mit
 * angemeldetem Benutzer, sodass auch kein Scope-Filter mehr nötig ist.
 */
#[AsEventListener(priority: 20)]
class BackendMenuListener
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param BackendMenuManipulator $manipulator Der Service zur Menü-Manipulation
     */
    public function __construct(
        private readonly BackendMenuManipulator $manipulator,
    ) {
    }

    /**
     * Manipuliert BE_MOD, bevor der Core-Listener das Hauptmenü daraus baut.
     *
     * Das MenuEvent wird auch für das Kopfzeilen-Menü ("headerMenu")
     * ausgelöst — dort ist nichts zu tun.
     *
     * @param MenuEvent $event Das Menü-Ereignis mit Factory und Baum
     *
     * @return void
     */
    public function __invoke(MenuEvent $event): void
    {
        if ('mainMenu' !== $event->getTree()->getName()) {
            return;
        }

        $this->manipulator->manipulateBackendMenu();
    }
}
