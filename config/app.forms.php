<?php

return [
	'plugins' => [
		'meliscmspagehistoric' => [
			'forms' => [
                /** Search user functionality in latest comments dashboard plugin */
                'mcph_search_user_form' => [
                    'attributes' => [
                        'name' => 'mcph_search_user_form',
                        'id' => 'id_mcph_search_user_form',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator' => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => [
                        // Users
                        [
                            'spec' => [
                                'name' => 'mcph_user',
                                'type' => 'MelisCoreUsersSelect',
                                'options' => [
                                    'empty_option' => 'All User',
                                    'disable_inarray_validator' => true,
                                    // 'value_options' => [],
                                ],
                                'attributes' => [
                                    'id' => 'id_mcph_user_search',
                                    'class' => 'mcph-user-search',
                                    'style' => 'width: 100%',
                                ],
                            ],
                        ],
                    ],
                    'input_filter' => [
                        // Users
                        'mcph_user' => [
                            'required' => false,
                            'validators' => [],
                            'filters' => []
                        ],
                    ]
                ]
			],
		],
	],
];