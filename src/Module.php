<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\ArrayUtils;
use Zend\Session\Container;
use MelisCmsPageHistoric\Model\MelisPageHistoric;
use MelisCmsPageHistoric\Model\Tables\MelisPageHistoricTable;

use MelisCmsPageHistoric\Listener\MelisPageHistoricDeletePageListener;
use MelisCmsPageHistoric\Listener\MelisPageHistoricPageEventListener;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->createTranslations($e);
        
        $eventManager->getSharedManager()->attach(__NAMESPACE__,
    			MvcEvent::EVENT_DISPATCH, function($e) { 
    				$e->getTarget()->layout('layout/layoutMelisPageHistoric');
			});
        
        $eventManager->attach(new MelisPageHistoricDeletePageListener());
        $eventManager->attach(new MelisPageHistoricPageEventListener());
    }
    
    public function getConfig()
    {
    	$config = array();
    	$configFiles = array(
			include __DIR__ . '/../config/module.config.php',
			include __DIR__ . '/../config/app.interface.php',
			include __DIR__ . '/../config/app.forms.php',
            include __DIR__ . '/../config/app.tools.php',
	        include __DIR__ . '/../config/diagnostic.config.php',
    	);
    	
    	foreach ($configFiles as $file) {
    		$config = ArrayUtils::merge($config, $file);
    	} 
    	
    	return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        ); 
    }
    
    public function createTranslations($e)
    {
    	$sm = $e->getApplication()->getServiceManager();
    	$translator = $sm->get('translator');

    	$container = new Container('meliscore');
    	$locale = $container['melis-lang-locale'];
    	 
    	$translator->addTranslationFile('phparray', __DIR__ . '/../language/' . $locale . '.interface.php');
    	$translator->addTranslationFile('phparray', __DIR__ . '/../language/' . $locale . '.forms.php');
    }
    
    public function setTable($tableName)
    {
    	$tableGateway = $sm->get($tableName . "Gateway");
    	$table = new $tableName($tableGateway);
    	return $table;
    }
    
    public function getViewHelperConfig()
	{
		return array(
			'factories' => array(
    							)
    				);
    }
    
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
					'MelisCmsPageHistoric\Model\Tables\MelisPageHistoricTable' =>  function($sm) {
						return new MelisPageHistoricTable($sm->get('MelisPageHistoricGateway'));
					},
    			    'MelisPageHistoricGateway' => function($sm) {
    			         $hydratingResultSet = new HydratingResultSet(new ObjectProperty(), new MelisPageHistoric());
    			         return new TableGateway('melis_hist_page_historic', $sm->get('Zend\Db\Adapter\Adapter'), null, $hydratingResultSet);
    			    },
    			),
    	);
    }
    
    
}
