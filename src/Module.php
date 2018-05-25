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
use Zend\Stdlib\ArrayUtils;
use Zend\Session\Container;

use MelisCmsPageHistoric\Listener\MelisPageHistoricDeletePageListener;
use MelisCmsPageHistoric\Listener\MelisPageHistoricPageEventListener;
/**
 * Class Module
 * @package MelisCmsPageHistoric
 * @require melis-core|melis-cms
 */

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->createTranslations($e);
        
        $sm = $e->getApplication()->getServiceManager();
        $routeMatch = $sm->get('router')->match($sm->get('request'));
        if (!empty($routeMatch))
        {
            $routeName = $routeMatch->getMatchedRouteName();
            $module = explode('/', $routeName);
             
            if (!empty($module[0]))
            {
                if ($module[0] == 'melis-backoffice')
                {
                    $eventManager->getSharedManager()->attach(__NAMESPACE__,
                			MvcEvent::EVENT_DISPATCH, function($e) { 
                				$e->getTarget()->layout('layout/layoutMelisPageHistoric');
            			});
                    
                    $eventManager->attach(new MelisPageHistoricDeletePageListener());
                    $eventManager->attach(new MelisPageHistoricPageEventListener());
                }
            }
        }
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
    	    
	        include __DIR__ . '/../config/dashboard-plugins/MelisCmsPageHistoricRecentUserActivityPlugin.config.php',
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
    	 

    	if (!empty($locale))
    	{
    	    $translationType = array(
    	        'interface',
    	        'forms',
    	    );
    	    
    	    $translationList = array();
    	    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/../module/MelisModuleConfig/config/translation.list.php')){
                $translationList = include 'module/MelisModuleConfig/config/translation.list.php';
            }

            foreach($translationType as $type){
                
                $transPath = '';
                $moduleTrans = __NAMESPACE__."/$locale.$type.php";
                
                if(in_array($moduleTrans, $translationList)){
                    $transPath = "module/MelisModuleConfig/languages/".$moduleTrans;
                }

                if(empty($transPath)){
                    
                    // if translation is not found, use melis default translations
                    $defaultLocale = (file_exists(__DIR__ . "/../language/$locale.$type.php"))? $locale : "en_EN";
                    $transPath = __DIR__ . "/../language/$defaultLocale.$type.php";
                }
                
                $translator->addTranslationFile('phparray', $transPath);
            }
    	}
    }
}
