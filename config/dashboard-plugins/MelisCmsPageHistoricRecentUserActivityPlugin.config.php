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
                            'name' => 'tr_melispagehistoric_dashboard_recent_activity_Pages',
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
                            'width' => 4,
                            'x-axis' => 0,
                            'y-axis' => 0,
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