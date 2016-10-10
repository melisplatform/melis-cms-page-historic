<?php

return array(

    'plugins' => array(
        'diagnostic' => array(
            'MelisCmsPageHistoric' => array(
                
                // tests to execute
                'basicTest' => array(
                    'controller' => 'Diagnostic',
                    'action' => 'basicTest',
                    'payload' => array(
                        'label' => 'tr_melis_module_basic_action_test',
                        'module' => 'MelisCmsPageHistoric'
                    )
                ),
        
                'testModuleTables' => array(
                    'controller' => 'Diagnostic',
                    'action' => 'testModuleTables',
                    'payload' => array(
                        'label' => 'tr_melis_module_db_test',
                        'tables' => array(
                            'melis_hist_page_historic', 
                        )
                    ),
                ),
            ),
        
        ),
    ),


);

