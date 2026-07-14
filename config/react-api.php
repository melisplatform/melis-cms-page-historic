<?php

/**
 * Route react-api de l'onglet Historique — MODULAIRE (dans melis-cms-page-historic).
 * S'attache aux child_routes du bridge générique melis-react-api (merge Laminas). L'URL
 * reste sous /melis/react-api/cms-page/historic. Mergé via MelisCmsPageHistoric\Module::getConfig().
 */

return [
    'router' => [
        'routes' => [
            'melis-backoffice' => [
                'child_routes' => [
                    'melis-react-api' => [
                        'child_routes' => [
                            'cms-page-historic' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/cms-page/historic[/]',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'MelisCmsPageHistoric\Controller',
                                        'controller'    => 'MelisReactApiPageHistoric',
                                        'action'        => 'list',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'MelisCmsPageHistoric\Controller\MelisReactApiPageHistoric' => \MelisCmsPageHistoric\Controller\MelisReactApiPageHistoricController::class,
        ],
    ],
];
