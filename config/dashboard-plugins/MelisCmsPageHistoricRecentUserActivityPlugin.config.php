<?php 
    return array(
        'plugins' => array(
            'meliscore' => [
                'interface' => [
                    'melis_dashboardplugin' => [
                        'conf' => [
                            'dashboard_plugin' => true
                        ],
                        'interface' => [
                            'melisdashboardplugin_section' => [
                                'interface' => [
                                    'MelisCmsPageHistoricRecentUserActivityPlugin' => [
                                        'conf' => [
                                            'type' => '/meliscmspagehistoric/interface/MelisCmsPageHistoricRecentUserActivityPlugin'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'meliscmspagehistoric' => array(
                'ressources' => array(
                    'css' => array(
                    ),
                    'js' => array(
                    )
                ),
                'interface' => array(
                    'MelisCmsPageHistoricRecentUserActivityPlugin' => array(
                        'conf' => array(
                            'name' => 'MelisCmsPageHistoricRecentUserActivityPlugin',
                            'melisKey' => 'MelisCmsPageHistoricRecentUserActivityPlugin'
                        ),
                        'datas' => array(
                            'plugin_id' => 'recentActivity',
                            'name' => 'tr_melispagehistoric_dashboard_recent_activity_Pages',
                            'description' => 'tr_melispagehistoric_dashboard_recent_activity_Pages',
                            'icon' => 'fa fa-users',
                            'thumbnail' => '/MelisCmsPageHistoric/plugins/images/MelisCmsPageHistoricRecentUserActivityPlugin.jpg',
                            'jscallback' => '',
                            'max_lines' => 8,
                            'height' => 4,
                            'width' => 6,
                            'x-axis' => 0,
                            'y-axis' => 0,
                            /*
                            * if set this plugin will belong to a specific marketplace section,
                              *  - available section for dashboard plugins as of 2019-05-16
                              *    - MelisCore
                              *    - MelisCms
                              *    - MelisMarketing
                              *    - MelisSite
                              *    - MelisCommerce
                              *    - Others
                              *    - CustomProjects
                              * if not or the section is not correct it will go directly to ( Others ) section
                            */
                            'section' => 'MelisCms',
                        ),
                        'forward' => array(
                            'module' => 'MelisCmsPageHistoric',
                            'plugin' => 'MelisCmsPageHistoricRecentUserActivityPlugin',
                            'function' => 'recentActivityPages',
                            'jscallback' => '',
                            'jsdatas' => array()
                        ),
                    ),
                ),
            ),
        ),
    );