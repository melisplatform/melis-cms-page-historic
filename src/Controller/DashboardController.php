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
use Zend\Session\Container;
use MelisCms\Service\MelisCmsRightsService;

/**
 * Dashboard controller for MelisCmsPageHistoric
 * 
 * Used to render dashboard components in MelisPlatform Back Office
 *
 */
class DashboardController extends AbstractActionController
{
    /**
     * Adds page's recent activity on the dashboard
     */
	public function recentActivityPagesAction()
	{
		$melisKey = $this->params()->fromRoute('melisKey', '');
        $finalPages = [];
        $pageId     = null;
        $pageMelisKey = null;
		if($this->isCmsActive()) {

            $melisTranslation = $this->getServiceLocator()->get('MelisCoreTranslation');
            $melisAppConfig = $this->getServiceLocator()->get('MelisCoreConfig');
            $melisCoreAuth = $this->getServiceLocator()->get('MelisCoreAuth');
            $melisCmsRights = $this->getServiceLocator()->get('MelisCmsRights');

            $translator = $this->getServiceLocator()->get('translator');

            $melisKeys = $melisAppConfig->getMelisKeys();
            $fullKey = $melisKeys['melispagehistoric_dashboard_recent_activity_pages'];
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
            $itemConfig = $melisAppConfig->getItem($fullKey);
            $maxLines = 8;
            if (!empty($itemConfig['conf']['maxLines']))
                $maxLines = $itemConfig['conf']['maxLines'];

            $melisPageHistoricTable = $this->getServiceLocator()->get('MelisPagehistoricTable');
            $melisPage = $this->getServiceLocator()->get('MelisPagehistoricTable');
            $userTable = $this->getServiceLocator()->get('MelisCoreTableUser');

            $pages = $melisPageHistoricTable->getPagesHistoricForDashboard($maxLines);

            $finalPages = array();
            if ($pages)
            {
                $pages = $pages->toArray();

                foreach ($pages as $keyPage => $page)
                {
                    $melisPage = $this->serviceLocator->get('MelisEnginePage');
                    $datasPage = $melisPage->getDatasPage($page['pageId'], 'saved');
                    if (!empty($datasPage))
                        $datasPage = $datasPage->getMelisPageTree();

                    $datasPageHistoric = $melisPageHistoricTable->getDescendingHistoric($page['pageId'], 1);
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
        else {
            $config = $this->getServiceLocator()->get('config');
            unset($config['plugins']['meliscore_dashboard']['interface']['meliscore_dashboard_recent_activity']);
        }


		
		$view = new ViewModel();
		$view->melisKey = $melisKey;
		$view->pages = $finalPages;
		
		$view->pageId = $pageId;
		$view->pageMelisKey = $pageMelisKey;
		$view->isCmsActive = $this->isCmsActive();
		return $view;
	}

    private function isCmsInActive()
    {
        $melisCms  = 'MelisCms';
        $moduleSvc = $this->getServiceLocator()->get('ModulesService');
        $modules   = $moduleSvc->getInActiveModules();

        if(in_array($melisCms, $modules)) {
            return true;
        }

        return false;
    }

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
