<?php

return array(
    'plugins' => array(
        'meliscmspagehistoric' => array(
            'tools' => array(
                'tool_meliscmspagehistoric' => array(
                    'conf' => array(
                        'title' => 'tr_melispagehistoric_page_tab_historic_Historic',
                        'id' => 'id_meliscmspagehistoric'
                    ),
                    'table' => array(
                        // table ID
                        'target' => '',
                        'ajaxUrl' => '/melis/MelisCmsPageHistoric/PageHistoric/getPageHistoricData',
                        'dataFunction' => 'initHistoric',
                        'ajaxCallback' => '',
                        'filters' => array(
                            'left' => array(
                            ),
                            'center' => array(
                            ),
                            'right' => array(
                                'pagehistoric-refresh' => array(
                                    'module' => 'MelisCmsPageHistoric',
                                    'controller' => 'PageHistoric',
                                    'action' => 'render-page-historic-table-refresh',
                                ),
                            ),
                        ),
                        'columns' => array(
                            'hist_user_id' => array(
                                'text' => 'tr_melispagehistoric_table_head_User',
                                'css' => array('width' => '20%', 'padding-right' => '0', 'cursor' => 'auto'),
                                'sortable' => false,
        
                            ),
                            'hist_action' => array(
                                'text' => 'tr_melispagehistoric_table_head_Action',
                                'css' => array('width' => '10%', 'padding-right' => '0', 'cursor' => 'auto'),
                                'sortable' => false,
        
                            ),
                            'hist_date' => array(
                                'text' => 'tr_melispagehistoric_table_head_Date',
                                'css' => array('width' => '20%', 'padding-right' => '0', 'cursor' => 'auto'),
                                'sortable' => false,
        
                            ),
                            'hist_description' => array(
                                'text' => 'tr_melispagehistoric_table_head_Description',
                                'css' => array('width' => '40%', 'padding-right' => '0', 'cursor' => 'auto'),
                                'sortable' => false,
        
                            ),
                        ),
        
                        // define what columns can be used in searching, in this tool
                        // we are not searching for anything except the page id of the historic
                        'searchables' => array('hist_page_id'),
                        'actionButtons' => array(),
                    ),
                    'modals' => array(),
                    'forms' => array(),
                ),
            ),

        ), // end tools

    ), 
);