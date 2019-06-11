<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Controller\DashboardPlugins;

use MelisCore\Controller\DashboardPlugins\MelisCoreDashboardTemplatingPlugin;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use MelisCms\Service\MelisCmsRightsService;


class MelisCmsPageHistoricRecentUserActivityPlugin extends MelisCoreDashboardTemplatingPlugin
{
    public function __construct()
    {
        $this->pluginModule = 'meliscmspagehistoric';
        parent::__construct();
    }
    
    /**
     * Adds page's recent activity on the dashboard
     */
    public function recentActivityPages()
    {
        $finalPages = [];
        $pageId     = null;
        $pageMelisKey = null;

        /** @var \MelisCore\Service\MelisCoreDashboardPluginsRightsService $dashboardPluginsService */
        $dashboardPluginsService = $this->getServiceLocator()->get('MelisCoreDashboardPluginsService');
        //get the class name to make it as a key to the plugin
        $path = explode('\\', __CLASS__);
        $className = array_pop($path);

        $isPluginAccessible = $dashboardPluginsService->canAccess($className);
        
        if($this->isCmsActive()) 
        {
            $melisTranslation = $this->getServiceLocator()->get('MelisCoreTranslation');
            $melisAppConfig = $this->getServiceLocator()->get('MelisCoreConfig');
            $melisCoreAuth = $this->getServiceLocator()->get('MelisCoreAuth');
            $melisCmsRights = $this->getServiceLocator()->get('MelisCmsRights');
            
            $translator = $this->getServiceLocator()->get('translator');
            
            $melisKeys = $melisAppConfig->getMelisKeys();
            $fullKeyPage = $melisKeys['meliscms_page'];
            
            $xmlRights = $melisCoreAuth->getAuthRights();
            
            $pageId = '';
            $pageMelisKey = '';
            $itemConfigPage = $melisAppConfig->getItem($fullKeyPage);
            if ($itemConfigPage)
            {
                $pageId = $itemConfigPage['conf']['id'];
                $pageMelisKey = $itemConfigPage['conf']['melisKey'];
            }
            
            $container = new Container('meliscore');
            $locale = $container['melis-lang-locale'];
            
            $maxLines = 8;
            if (!empty($this->pluginConfig['max_lines']))
                $maxLines = $this->pluginConfig['max_lines'];
                
            $melisPageHistoricTable = $this->getServiceLocator()->get('MelisPagehistoricTable');
            $melisPage = $this->getServiceLocator()->get('MelisPagehistoricTable');
            $userTable = $this->getServiceLocator()->get('MelisCoreTableUser');
            
            $pages = $melisPageHistoricTable->getPagesHistoricForDashboard((int)$maxLines);


            $finalPages = array();
            if ($pages)
            {
                $pages = $pages->toArray();
                foreach ($pages as $keyPage => $page)
                {
                    $melisPage = $this->getServiceLocator()->get('MelisEnginePage');
                    $datasPage = $melisPage->getDatasPage($page['pageId'], 'saved');
                    if (!empty($datasPage))
                        $datasPage = $datasPage->getMelisPageTree();


                    $datasPageHistoric = $melisPageHistoricTable->getHistoricById($page['hist_id']);
//                    $datasPageHistoric = $melisPageHistoricTable->getDescendingHistoric($page['pageId'], 1);
                    $datasPageHistoric = $datasPageHistoric->toArray();
                    $datasPageHistoric = $datasPageHistoric[0];
                    $datasUser = $userTable->getEntryById($datasPageHistoric['hist_user_id']);
                    $name = '#' . $datasPageHistoric['hist_user_id'];
                    if ($datasUser)
                    {
                        $datasUser = $datasUser->toArray();
                        if ($datasUser)
                        {
                            $datasUser = $datasUser[0];
                            $name = $datasUser['usr_firstname'] . ' ' . $datasUser['usr_lastname'];
                        }
                    }
                    
                    $date = strftime($melisTranslation->getDateFormatByLocate($locale), strtotime($datasPageHistoric['hist_date']));
                    
                    $data_icon = 'fa fa-file-o';
                    if (!empty($datasPage))
                    {
                        if ($datasPage->page_type == 'PAGE')
                            $data_icon = 'fa fa-file-o';
                        if ($datasPage->page_type == 'SITE')
                            $data_icon = 'fa fa-home';
                        if ($datasPage->page_type == 'FOLDER')
                            $data_icon = 'fa fa-folder-open-o';
                        if ($datasPage->page_type == 'NEWSLETTER')
                            $data_icon = 'fa fa-newspaper-o';
                        if ($datasPage->page_type == 'NEWS_DETAIL')
                            $data_icon = 'fa fa-file-o';
                    }
                    
                    $actionIcon = '';
                    if ($datasPageHistoric['hist_action'] == 'Publish')
                        $actionIcon = 'fa fa-circle fa-color-green';
                    if ($datasPageHistoric['hist_action'] == 'Unpublish')
                        $actionIcon = 'fa fa-circle fa-color-red';
                    if ($datasPageHistoric['hist_action'] == 'Save')
                        $actionIcon = 'fa fa-save';
                    if ($datasPageHistoric['hist_action'] == 'Delete')
                        $actionIcon = 'fa fa-times fa-color-red';

                    $isAccessible = $melisCmsRights->isAccessible($xmlRights, MelisCmsRightsService::MELISCMS_PREFIX_PAGES, $page['pageId']);
                    
                    $pageName = $translator->translate('tr_meliscms_page_Page');
                    if (!empty($datasPage->page_name))
                        $pageName = $datasPage->page_name;
                        
                    $pageFinal = array(
                        'page_id' => $page['pageId'],
                        'page_name' => $pageName,
                        'hist_action' => $translator->translate('tr_melispagehistoric_action_text_'.$datasPageHistoric['hist_action']),
                        'hist_date' => $date,
                        'hist_user_id' => $datasPageHistoric['hist_user_id'],
                        'hist_user_name' => $name,
                        'page_icon' => $data_icon,
                        'hist_action_icon' => $actionIcon,
                        'page_accessible' => (string)$isAccessible,
                    );
                    
                    $finalPages[] = $pageFinal;
                }
            }
        }
        else 
        {
            $config = $this->getServiceLocator()->get('config');
            unset($config['plugins']['meliscore_dashboard']['interface']['meliscore_dashboard_recent_activity']);
        }
        
        $view = new ViewModel();
        $view->setTemplate('melis-cms-page-historic/dashboard-plugin/recent-user-activity');
        $view->pages = $finalPages;
        
        $view->pageId = $pageId;
        $view->pageMelisKey = $pageMelisKey;
        $view->isCmsActive = $this->isCmsActive();
        $view->isAccessable = $isPluginAccessible;
        return $view;
    }
    
    /**
     * Checking if MelisCms modul is activated
     * 
     * @return boolean
     */
    private function isCmsActive()
    {
        $melisCms  = 'MelisCms';
        $moduleSvc = $this->getServiceLocator()->get('ModulesService');
        $modules   = $moduleSvc->getActiveModules();
        
        if(in_array($melisCms, $modules)) {
            return true;
        }
        
        return false;
    }
}