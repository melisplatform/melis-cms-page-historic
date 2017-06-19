<?php


return array(
	'plugins' => array(
		'meliscore_dashboard' => array(
			'interface' => array(
				'meliscore_dashboard_recent_activity' => array(
					'interface' => array(
						'melispagehistoric_dashboard_recent_activity_pages' => array(
							'conf' => array(
								'id' => 'id_melispagehistoric_dashboard_recent_activity_pages',
								'name' => 'tr_melispagehistoric_dashboard_recent_activity_Pages',
								'melisKey' => 'melispagehistoric_dashboard_recent_activity_pages',
								'icon' => 'file',
								'maxLines' => 8,
							),
							'forward' => array(
								'module' => 'MelisCmsPageHistoric',
								'controller' => 'Dashboard',
								'action' => 'recentActivityPages',
								'jscallback' => 'initDashboardPageHistoric();',
								'jsdatas' => array()
							),
						),
					),
				),
			),
		),
		'meliscms' => array(
			'interface' => array(
				'meliscms_page' => array(
					'interface' => array(
						'meliscms_tabs' => array(
							'interface' => array(
								'melispagehistoric_page_historic' =>  array(
									'conf' => array(
										'id' => 'id_meliscms_center_page_tabs_historic',
										'type' => '/meliscmspagehistoric/interface/melispagehistoric_historic',
										'name' => 'tr_melispagehistoric_page_tab_historic_Historic',
										'icon' => 'history',
									),
								)
							)
						)
					)
				)
			)	
		),
		'meliscmspagehistoric' => array(
			'conf' => array(
				'name' => 'tr_melispagehistoric_melispagehistoric',
				'rightsDisplay' => 'none',
			),
		    'ressources' => array(
		          'js' => array(
		              '/MelisCmsPageHistoric/js/melispagehistoric.js',
		          ),
		        'css' => array(
		              
		        ),
		    ),
			'datas' => array(
			),
			'interface' => array(
				'melispagehistoric_historic' =>  array(
					'conf' => array(
						'id' => 'id_meliscms_center_page_tabs_historic',
						'name' => 'tr_melispagehistoric_tab',
						'melisKey' => 'melispagehistoric_historic',
						'icon' => 'history',
						'rightsDisplay' => 'referencesonly',
					),
					'forward' => array(
						'module' => 'MelisCmsPageHistoric',
						'controller' => 'PageHistoric',
						'action' => 'render-page-historic',
						'jscallback' => '',
						'jsdatas' => array()
					),
                    'interface' => array(                        
                        'melispagehistoric_table' => array(
                            'conf' => array(
                                'id' =>  'id_meliscms_center_page_tabs_historic_table',
                                'melisKey' => 'melispagehistoric_table',
								'name' => 'tr_meliscore_tool_gen_content',
                            ),
        					'forward' => array(
        						'module' => 'MelisCmsPageHistoric',
        						'controller' => 'PageHistoric',
        						'action' => 'render-page-historic-table',
        						'jscallback' => '',
        						'jsdatas' => array()
        					),
                        ),
                    )
				),

			),
		    

		),
	)
);
