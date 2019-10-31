<?php


return array(
	'plugins' => array(
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
                /**
                 * the "build" configuration compiles all assets into one file to make
                 * lesser requests
                 */
                'build' => [
                    'use_bundle' => true,
                    // lists of assets that will be loaded in the layout
                    'css' => [
                        '/MelisCmsPageHistoric/build/css/bundle.css',
                    ],
                    'js' => [
                        '/MelisCmsPageHistoric/build/js/bundle.js',
                    ]
                ]

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
