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
        
                'fileCreationTest' => array(
                    'controller' => 'Diagnostic',
                    'action' => 'fileCreationTest',
                    'payload' => array(
                        'label' => 'tr_melis_module_rights_dir',
                        'path' => MELIS_MODULES_FOLDER.'MelisCmsPageHistoric/public/',
                        'file' => 'test_file_creation.txt'
                    ),
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

