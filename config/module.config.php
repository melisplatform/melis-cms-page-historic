<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'router' => [
        'routes' => [
        	'melis-backoffice' => [
                'child_routes' => [
                    'application-MelisCmsPageHistoric' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'MelisCmsPageHistoric',
                            'defaults' => [
                                '__NAMESPACE__' => 'MelisCmsPageHistoric\Controller',
                                'controller'    => 'PageHistoric',
                                'action'        => 'renderPageHistoric',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'default' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/[:controller[/:action]]',
                                    'constraints' => [
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                    ],
                                ],
                            ],
                        ],
                    ], 
                ],
            ],            
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'MelisPageHistoricTable' => \MelisCmsPageHistoric\Model\Tables\MelisPageHistoricTable::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'MelisCmsPageHistoric\Controller\PageHistoric' => \MelisCmsPageHistoric\Controller\PageHistoricController::class,
    	],
    ],
    'controller_plugins' => [
        'invokables' => [
            'MelisCmsPageHistoricRecentUserActivityPlugin' => \MelisCmsPageHistoric\Controller\DashboardPlugins\MelisCmsPageHistoricRecentUserActivityPlugin::class,
        ]
    ],
    'view_manager' => [
        'doctype'                  => 'HTML5',
        'template_map' => [
            // Dashboard plugin templates
            'melis-cms-page-historic/dashboard-plugin/recent-user-activity'  => __DIR__ . '/../view/melis-cms-page-historic/dashboard-plugins/recent-user-activity.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
