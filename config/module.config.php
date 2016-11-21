<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

return array(
    'router' => array(
        'routes' => array(
        	'melis-backoffice' => array(
                'child_routes' => array(
                    'application-MelisCmsPageHistoric' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'MelisCmsPageHistoric',
                            'defaults' => array(
                                '__NAMESPACE__' => 'MelisCmsPageHistoric\Controller',
                                'controller'    => 'PageHistoric',
                                'action'        => 'renderPageHistoric',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'default' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:action]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                    ),
                                ),
                            ),
                        ),
                    ), 
                ),
            ),            
        ),
    ),
    'translator' => array(
    	'locale' => 'en_EN',
	),
    'service_manager' => array(
    	'invokables' => array(
    	),
        'aliases' => array(
            'MelisPagehistoricTable' => 'MelisCmsPageHistoric\Model\Tables\MelisPageHistoricTable',
        ),
        'factories' => array(
            'MelisCmsPageHistoric\Model\Tables\MelisPageHistoricTable' => 'MelisCmsPageHistoric\Model\Tables\Factory\MelisPageHistoricTableFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'MelisCmsPageHistoric\Controller\PageHistoric' => 'MelisCmsPageHistoric\Controller\PageHistoricController',
            'MelisCmsPageHistoric\Controller\Dashboard' => 'MelisCmsPageHistoric\Controller\DashboardController',
            'MelisCmsPageHistoric\Controller\Diagnostic' => 'MelisCmsPageHistoric\Controller\DiagnosticController'
    	),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'aliases' => array(
                'MelisCmsPageHistoric/' => __DIR__ . '/../public/',
            ),
        ),
    ),
);
