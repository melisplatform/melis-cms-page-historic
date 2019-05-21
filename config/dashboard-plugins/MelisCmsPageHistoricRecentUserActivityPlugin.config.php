<?php 
    return array(
        'plugins' => array(
            'meliscmspagehistoric' => array(
                'ressources' => array(
                    'css' => array(
                    ),
                    'js' => array(
                    )
                ),
                'dashboard_plugins' => array(
                    'MelisCmsPageHistoricRecentUserActivityPlugin' => array(
                        'plugin_id' => 'recentActivity',
                        'name' => 'tr_melispagehistoric_dashboard_recent_activity_Pages',
                        'description' => 'tr_melispagehistoric_dashboard_recent_activity_Pages description',
                        'icon' => 'fa fa-users',
                        'thumbnail' => '/MelisCmsPageHistoric/plugins/images/MelisCmsPageHistoricRecentUserActivityPlugin.jpg',
                        'jscallback' => '',
                        'max_lines' => 8,
                        'height' => 4,
                        'section' => 'MelisCms',
                        'interface' => array(
                            'meliscmspagehistoric_events' => array(
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
            ),
        ),
    );