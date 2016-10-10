<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

use MelisCore\Listener\MelisCoreGeneralListener;

/**
 * This listener activates when a page is saved, published, unpublished
 * in order to add an entry in the page's historic
 */
class MelisPageHistoricPageEventListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        
        $callBackHandler = $sharedEvents->attach(
            'MelisCms', 
            array(
        		'meliscms_page_save_end',
        		'meliscms_page_publish_end',
        		'meliscms_page_unpublish_end',
        	), 
            function($e) {

            	$sm = $e->getTarget()->getServiceLocator();
                $melisCoreDispatchService = $sm->get('MelisCoreDispatch');

                $params = $e->getParams();
                $eventName = $e->getName();
                
                if ($eventName == 'meliscms_page_save_end')
                	$actionUsed = 'Save';

            	if ($eventName == 'meliscms_page_publish_end')
            		$actionUsed = 'Publish';

        		if ($eventName == 'meliscms_page_unpublish_end')
        			$actionUsed = 'Unpublish';
                    
                // dispatch an event
				$evtTrigger = $e->getTarget();
				$results = $evtTrigger->forward()->dispatch('MelisCmsPageHistoric\Controller\PageHistoric',
								array_merge(array('action' => 'savePageHistoric', 'pageActionUsed' => $actionUsed), $params))->getVariables();
                    
            },
        50);
        
        $this->listeners[] = $callBackHandler;
    }
}