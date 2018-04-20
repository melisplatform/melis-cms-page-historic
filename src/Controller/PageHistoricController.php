<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;
use Zend\Json\Json;

/**
 * Page Historic Controller
 * Adds the historic tabulation on the page's edition
 */
class PageHistoricController extends AbstractActionController
{ 
    const PLUGIN_INDEX = 'meliscmspagehistoric';
    const TOOL_KEY = 'tool_meliscmspagehistoric';
    
    /**
     * Renders the view inside the Page Historic tab
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
     */
    public function renderPageHistoricTableAction()
    {

        $translator = $this->getServiceLocator()->get('translator');
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
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
     * Renders to the refresh button in the datatable
     * @return \Zend\View\Model\ViewModel
     */
    public function renderPageHistoricTableRefreshAction()
    {
        return new ViewModel();
    }
    
    /**
     * Returns a JSON format of Page Historic Data from DB
     * @return \Zend\View\Model\JsonModel
     */
    public function getPageHistoricDataAction()
    {
        
        $melisPageHistoricTable = $this->getServiceLocator()->get('MelisPagehistoricTable');
        $melisUserTable = $this->serviceLocator->get('MelisCoreTableUser');
        
        $translator = $this->getServiceLocator()->get('translator');
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $melisTool->setMelisToolKey(self::PLUGIN_INDEX, self::TOOL_KEY);

        $melisTranslation = $this->getServiceLocator()->get('MelisCoreTranslation');
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];
        
        $colId = array();
        $dataCount = 0;
        $draw = 0;
        $tableData = array();
        if($this->getRequest()->isPost())
        {
        
            $pageId = (int) $this->getRequest()->getPost('pageId');
            
            $colId = array_keys($melisTool->getColumns());

            $selCol = $this->getRequest()->getPost('order');
            $selCol = $colId[$selCol[0]['column']];

            if($selCol != 'hist_id') {
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
        
            $getData = $melisPageHistoricTable->getPageHistoricData(array(
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
                'date_filter' => array(),
            ));
        
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
                $tableData[$ctr]['hist_date'] = strftime($melisTranslation->getDateFormatByLocate($locale), strtotime($tableData[$ctr]['hist_date']));
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
        $melisPageHistoricTable = $this->getServiceLocator()->get('MelisPageHistoricTable');

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
        
        $melisCoreAuth = $this->getServiceLocator()->get('MelisCoreAuth');
    	$melisPageHistoricTable = $this->getServiceLocator()->get('MelisPageHistoricTable');	

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
    	switch($pageAction)
    	{
    	    case 'Save':
    	        if($isNew)
    	        {
    	            $description = 'tr_melispagehistoric_description_text_new';
    	        }
    	        else
    	        {
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

    	if($idPage) 
    	{
    	    $userId = (int) null;
    	    $userAuthDatas =  $melisCoreAuth->getStorage()->read();
    	    if($userAuthDatas)
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
        
    	$melisPageHistoricTable = $this->getServiceLocator()->get('MelisPageHistoricTable');
    	

    	$melisCoreAuth = $this->getServiceLocator()->get('MelisCoreAuth');

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


    

}
