<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;

use MelisCore\Listener\MelisCoreGeneralListener;

/**
 * This listener activates when a page is deleted so that an entry
 * is added in the page's historic
 *
 */
class MelisPageHistoricDeletePageListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();
        
        $callBackHandler = $sharedEvents->attach(
            'MelisCms', 
            'meliscms_page_delete_end', 
            function($e) {

            	$sm = $e->getTarget()->getServiceManager();
            	
            	$params = $e->getParams();

            	if ($params['success'] == 1)
            	{
	                $melisCoreDispatchService = $sm->get('MelisCoreDispatch');
	                
                    // dispatch an event
                    $evtTrigger = $e->getTarget();
                    $results = $evtTrigger->forward()->dispatch('MelisCmsPageHistoric\Controller\PageHistoric',
                        array_merge(array('action' => 'deletePageHistoric'), $params))->getVariables();
            	}
                
            },
        50);
        
        $this->listeners[] = $callBackHandler;
    }
}