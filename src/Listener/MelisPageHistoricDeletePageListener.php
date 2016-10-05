<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use MelisCore\Listener\MelisCoreGeneralListener;
class MelisPageHistoricDeletePageListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        
        $callBackHandler = $sharedEvents->attach(
            'MelisCms', 
            'meliscms_page_delete_end', 
            function($e) {

            	$sm = $e->getTarget()->getServiceLocator();
            	
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