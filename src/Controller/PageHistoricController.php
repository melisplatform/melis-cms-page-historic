<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Controller;

use Laminas\Form\Factory;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Session\Container;
use Laminas\Json\Json;
use Laminas\View\View;
use MelisCore\Controller\MelisAbstractActionController;

/**
 * Page Historic Controller
 * Adds the historic tabulation on the page's edition
 */
class PageHistoricController extends MelisAbstractActionController
{ 
    const PLUGIN_INDEX = 'meliscmspagehistoric';
    const TOOL_KEY = 'tool_meliscmspagehistoric';
    const USER_FILTER_CONFIG_PATH = 'meliscmspagehistoric/forms/mcph_search_user_form';
    
    /**
     * Renders the view inside the Page Historic tab
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderPageHistoricAction()
    {
		$idPage = $this->params()->fromRoute('idPage', $this->params()->fromQuery('idPage', ''));
    	$melisKey = $this->params()->fromRoute('melisKey', '');

    	$view = new ViewModel();
    	$view->idPage = $idPage;
    	$view->melisKey = $melisKey;
    	
    	 
    	return $view;
    }
    
    /**
     * Renders the Page Historic table view
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderPageHistoricTableAction()
    {

        $translator = $this->getServiceManager()->get('translator');
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $melisTool = $this->getServiceManager()->get('MelisCoreTool');
        $melisTool->setMelisToolKey(self::PLUGIN_INDEX, self::TOOL_KEY);
        $columns = $melisTool->getColumns();

        $idPage = $this->params()->fromRoute('idPage', $this->params()->fromQuery('idPage', ''));

        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->tableColumns = $columns;
        $view->getToolDataTableConfig = $melisTool->getDataTableConfiguration('#tableHistoricPageId'.$idPage, true);
        $view->idPage = $idPage;
        $view->tableId = 'tableHistoricPageId'.$idPage;

        return $view;
    }

    /**
     * Renders the select limit for the datatable
     * @return ViewModel
     */
    public function renderPageHistoricTableLimitAction()
    {
        return new ViewModel();
    }

    /**
     * User Filter renderer
     * @return ViewModel
     */
    public function renderPageHistoricContentFiltersSearchUserAction()
    {
        $factory = new Factory();
        $view = new ViewModel();
        $translator = $this->getServiceManager()->get('translator');
        $pageId = $this->params()->fromRoute('idPage', $this->params()->fromQuery('idPage', ''));

        $melisConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $formElementMgr = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElementMgr);
        $formConfig = $melisConfig->getItem(self::USER_FILTER_CONFIG_PATH);
        /** @var \Laminas\Form\Form $form */
        $form = $factory->createForm($formConfig);
        $form->setAttribute('id', $form->getAttribute('id') . '_' . $pageId);

        $view->userSearchForm = $form;
        $view->pageId = $pageId;
        $view->label = $translator->translate('tr_melispagehistoric_table_head_User');

        return $view;
    }

    /**
     * Renders the date range filter
     * @return ViewModel
     */
    public function renderPageHistoricContentFiltersDateAction()
    {
        return new ViewModel();
    }

    /**
     * Renders the actions filter
     * @return ViewModel
     */
    public function renderPageHistoricContentFiltersActionsAction()
    {
        $melisPageHistoricTable = $this->getServiceManager()->get('MelisPageHistoricTable');
        //get distinct actions on database
        $actions = $melisPageHistoricTable->getPageHistoricListOfActions()->toArray();
        $translator = $this->getServiceManager()->get('translator');

        $options = '<option value="">' . $translator->translate('tr_melispagehistoric_filter_action_select') . '</option>';
        foreach ($actions as $action) {
            if($action['action'] != "Delete")
                $options .= '<option value="' . $action['action'] . '">' . $translator->translate('tr_melispagehistoric_action_text_' . $action['action']) . '</option>';
        }

        $view = new ViewModel();
        $view->options = $options;

        return $view;
    }

    /**
     * Renders to the refresh button in the datatable
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderPageHistoricTableRefreshAction()
    {
        return new ViewModel();
    }
    
    /**
     * Returns a JSON format of Page Historic Data from DB
     * @return \Laminas\View\Model\JsonModel
     */
    public function getPageHistoricDataAction()
    {
        $melisPageHistoricTable = $this->getServiceManager()->get('MelisPageHistoricTable');
        $melisUserTable = $this->getServiceManager()->get('MelisCoreTableUser');
        
        $translator = $this->getServiceManager()->get('translator');
        $melisTool = $this->getServiceManager()->get('MelisCoreTool');
        $melisTool->setMelisToolKey(self::PLUGIN_INDEX, self::TOOL_KEY);

        $melisTranslation = $this->getServiceManager()->get('MelisCoreTranslation');
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];
        
        $colId = array();
        $dataCount = 0;
        $draw = 0;
        $tableData = array();
        if ($this->getRequest()->isPost()) {
            $pageId = (int) $this->getRequest()->getPost('pageId');
            $userName = $this->getRequest()->getPost('user_name', null);
            $action = $this->getRequest()->getPost('action', null);

            $colId = array_keys($melisTool->getColumns());
            $selCol = $this->getRequest()->getPost('order');
            $selCol = $colId[$selCol[0]['column']];

            $startDate = $this->getRequest()->getPost('startDate');
            $endDate = $this->getRequest()->getPost('endDate');

            if ($selCol != 'hist_id') {
                $sortOrder = $this->getRequest()->getPost('order');
                $sortOrder = $sortOrder[0]['dir'];
            }
            else {
                $sortOrder = 'desc';
            }
        
            $draw = $this->getRequest()->getPost('draw');
            $start = $this->getRequest()->getPost('start');
            $length =  $this->getRequest()->getPost('length');

            $dataCount = $melisPageHistoricTable->getTotalData('hist_page_id',$pageId);

            $option = array(
                'where' => array(
                    'key' => 'hist_page_id',
                    'value' => $pageId,
                ),
                // fixed order, since page historic is sorting descendingly using history ID
                'order' => array(
                    'key' => 'hist_id',
                    'dir' => 'desc',
                ),
                'start' => $start,
                'limit' => $length,
                'columns' => $melisTool->getSearchableColumns(),
                'date_filter' => array(

                ),
            );

            if ($startDate != NULL && $endDate != NULL) {
                // detect language to convert dates to proper formats
                if ($locale == 'fr_FR') {
                    // From dd/mm/yyyy to mm/dd/yyyy
                    $startDate = explode('/', $startDate);
                    $startDate = "$startDate[1]/$startDate[0]/$startDate[2]";
                    $endDate = explode('/', $endDate);
                    $endDate = "$endDate[1]/$endDate[0]/$endDate[2]";
                }

                $option['date_filter'] = [
                    'key' => 'hist_date',
                    'startDate' => date('Y-m-d', strtotime($startDate)),
                    'endDate' => date('Y-m-d', strtotime($endDate))
                ];
            }

            $getData = $melisPageHistoricTable->getPageHistoricData($option, null, $userName, $action);

        
            // store fetched Object Data into array so we can apply any string modifications
            foreach($getData as $key => $values)
            {
                $tableData[$key] = (array) $values;
            }

            for($ctr = 0; $ctr < count($tableData); $ctr++)
            {
                // Initialize as Deleted User As Default
                $histUserId = $tableData[$ctr]['hist_user_id'];
                $tableData[$ctr]['hist_user_id'] = $translator->translate('tr_meliscore_user_deleted').' ('.$tableData[$ctr]['hist_user_id'].')';
                
                $histUserData = $melisUserTable->getEntryById($histUserId);
                
                if (!empty($histUserData)){
                    $histUserData = $histUserData->current();
                    if(!empty($histUserData)) {
                        $tableData[$ctr]['hist_user_id'] = ucfirst(mb_strtolower($histUserData->usr_firstname, 'UTF-8')).' '.ucfirst(mb_strtolower($histUserData->usr_lastname, 'UTF-8'));
                    }
                }
                
                $tableData[$ctr]['DT_RowId'] = $tableData[$ctr]['hist_id'];
                $tableData[$ctr]['hist_action'] = $translator->translate('tr_melispagehistoric_action_text_'.$tableData[$ctr]['hist_action']);
                $tableData[$ctr]['hist_date'] = date($melisTranslation->getDateFormatByLocate($locale), strtotime($tableData[$ctr]['hist_date']));
                $tableData[$ctr]['hist_description'] =$translator->translate($tableData[$ctr]['hist_description']);
            }
        }
      
        return new JsonModel(array(
            'draw' => (int) $draw,
            'recordsTotal' => $dataCount,
            'recordsFiltered' =>  $melisPageHistoricTable->getTotalFiltered(),
            'data' => $tableData,
        ));
    }
    /**
     * Returns latest page historic
     *
     * return array
     */
    public  function getLatestPageHistoric()
    {
        $responseData = $this->params()->fromRoute('datas', $this->params()->fromQuery('datas', ''));
        $melisPageHistoricTable = $this->getServiceManager()->get('MelisPageHistoricTable');

        $histDatas = array(
            'hist_page_id' => $idPage,
            'hist_action'  => 'Delete',
            'hist_date'     => date('Y-m-d H:i:s'),
            'hist_user_id' => $userId,
            'hist_description' => 'tr_melispagehistoric_action_text_Delete'
        );

        return $histDatas;
    }
    
    /**
     * Saves the action taken from MelisCMS Page Actions 
     */
    public function savePageHistoricAction()
    {
        $responseData = $this->params()->fromRoute('datas', $this->params()->fromQuery('datas', ''));

    	$idPage = isset($responseData['idPage']) ? $responseData['idPage'] : (!empty($responseData[0]['idPage'])?($responseData[0]['idPage']):0);
        $isNew =  isset($responseData['isNew']) ? $responseData['isNew'] : (!empty($responseData[0]['isNew'])?($responseData[0]['isNew']):0);
        
        $response = array(
        		'idPage' => $idPage,
        		'isNew' => $isNew
        );
        $this->getEventManager()->trigger('meliscmspagehistoric_historic_save_start', $this, $response);
        
        $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
    	$melisPageHistoricTable = $this->getServiceManager()->get('MelisPageHistoricTable');

    	$pageAction = $this->params()->fromRoute('pageActionUsed', $this->params()->fromQuery('pageActionUsed',''));
    	$histDatas  = array();
    	
    	$container = new Container('meliscms');
    	$datas = array();
    	
    	if (isset($container['action-page-tmp']['success'])
    		&& $container['action-page-tmp']['success'] == 0)
    		return;
    	
    	// Update from the different save actions done
    	if (!empty($container['action-page-tmp']))
    	{
            if (!empty($container['action-page-tmp']['datas']))
                $datas = $container['action-page-tmp']['datas'];
    	}
    	
    	$description = '';

    	switch ($pageAction) {
    	    case 'Save':
    	        if ($isNew) {
    	            $description = 'tr_melispagehistoric_description_text_new';
    	        } else {
    	            $description = 'tr_melispagehistoric_description_text_save';
    	        }
	            break;
    	    case 'Publish':
    	        $description = 'tr_melispagehistoric_description_text_publish';
	            break;
    	    case 'Unpublish':
    	        $description = 'tr_melispagehistoric_description_text_unpublished';
	            break;
    	}

    	if ($idPage) {
    	    $userId = (int) null;
    	    $userAuthDatas =  $melisCoreAuth->getStorage()->read();

    	    if ($userAuthDatas)
    	       $userId = $userAuthDatas->usr_id;
    	     
    	    $histDatas = array(
    	        'hist_page_id' => $idPage,
    	        'hist_action'  => $pageAction,
    	        'hist_date'     => date('Y-m-d H:i:s'),
    	        'hist_user_id' => $userId,
    	        'hist_description' => $description
    	    );
    	    
    	    $melisPageHistoricTable->save($histDatas);
    	}

    	$this->getEventManager()->trigger('meliscmspagehistoric_historic_save_end', $this, $histDatas);
    }
    
    /**
     * Removes page historic entries
     */
    public function deletePageHistoricAction() 
    {
        $responseData = $this->params()->fromRoute('datas', $this->params()->fromQuery('datas', ''));

        $idPage       = $responseData[0]['idPage'];

        $response = array('idPage' => $idPage);
        $this->getEventManager()->trigger('meliscmspagehistoric_historic_delete_start', $this, $response);
        
    	$melisPageHistoricTable = $this->getServiceManager()->get('MelisPageHistoricTable');
    	

    	$melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');

    	$userId = (int) null;
        $userAuthDatas =  $melisCoreAuth->getStorage()->read();
    	if($userAuthDatas)
    	   $userId = $userAuthDatas->usr_id;
    	    	     
        $histDatas = array(
            'hist_page_id' => $idPage,
    	    'hist_action'  => 'Delete',
            'hist_date'     => date('Y-m-d H:i:s'),
    	    'hist_user_id' => $userId,
    	    'hist_description' => 'tr_melispagehistoric_action_text_Delete' 
        );
        $melisPageHistoricTable->save($histDatas);
    	
        $this->getEventManager()->trigger('meliscmspagehistoric_historic_delete_end', $this, $responseData);
    }

    /**
     * Gets all back office users that has a record in database
     * @return JsonModel
     */
    public function getBackOfficeUsersAction()
    {
        $melisPageHistoricTable = $this->getServiceManager()->get('MelisPageHistoricTable');
        $users = $melisPageHistoricTable->getUsers()->toArray();

        return new JsonModel(array(
            'users' => $users,
        ));
    }

    /**
     * Mainly serves users for the tool's  user search table filter
     * @return JsonModel
     */
    public function getBOUsersAction()
    {
        $success = 0;
        $errors = [];
        $request = $this->getRequest();
        $results = [];
        $morePages = false;
        $pagination = ["more" => $morePages];
        $searchableColumns = ['usr_firstname', 'usr_lastname'];

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $searchValue = empty($post['search']) ? '' : $post['search'];
            $limit = empty($post['length']) ? 5 : (int)$post['length'];
            $orderDirection = 'ASC';

            /**
             * Filter Suggestion Pagination
             * - if no 'page' or page1 : do not offset
             * - otherwise, get offset.
             */
            $start = (empty($post['page']) || $post['page'] == 1) ? null : ((int)$post['page'] - 1) * $limit;

            /** @var \MelisCmsPageHistoric\Model\Tables\MelisPageHistoricTable $melisPageHistoricTable */
            $melisPageHistoricTable = $this->getServiceManager()->get('MelisPageHistoricTable');
            $where = [
                'search' => $searchValue,
                'searchableColumns' => $searchableColumns,
                'orderBy' => 'usr_firstname',
                'orderDirection' => $orderDirection,
                'start' => $start,
                'limit' => $limit,
            ];
            $users = $melisPageHistoricTable->getBOUsers($where)->toArray();
            foreach ($users as $user) {
                array_push($results, [
                    'id' => $user['usr_id'], // Option's value
                    'text' => $user['fullname'], // Option's label
                ]);
            }
            $success = true;
        }

        $response = [
            'results' => $results,
            'pagination' => $pagination,
            'success' => $success,
            'errors' => $errors,
        ];

        return new JsonModel($response);
    }
}
