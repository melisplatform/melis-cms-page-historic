<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Listener;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener activates when a page is saved, published, unpublished
 * in order to add an entry in the page's historic
 */
class MelisPageHistoricPageEventListener extends MelisGeneralListener implements ListenerAggregateInterface
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();

        $priority = 50;
        $identifier = 'MelisCms';
        $eventsName = [
            'meliscms_page_save_end',
            'meliscms_page_publish_end',
            'meliscms_page_unpublish_end',
        ];

        foreach ($eventsName As $event)
            $this->listeners[] = $sharedEvents->attach($identifier, $event, [$this, 'savePageHistoric'], $priority);
    }

    /**
     * Save page historic
     * @param EventInterface $event]
     */
    public function savePageHistoric(EventInterface $event)
    {
        $sm = $event->getTarget()->getServiceManager();
            $melisCoreDispatchService = $sm->get('MelisCoreDispatch');

            $params = $event->getParams();
            $eventName = $event->getName();

            if ($eventName == 'meliscms_page_save_end')
                $actionUsed = 'Save';

            if ($eventName == 'meliscms_page_publish_end')
                $actionUsed = 'Publish';

            if ($eventName == 'meliscms_page_unpublish_end')
                $actionUsed = 'Unpublish';

            // dispatch an event
            $evtTrigger = $event->getTarget();
            $results = $evtTrigger->forward()->dispatch('MelisCmsPageHistoric\Controller\PageHistoric',
                            array_merge(array('action' => 'savePageHistoric', 'pageActionUsed' => $actionUsed), $params))->getVariables();
    }
}