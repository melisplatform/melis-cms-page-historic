<?php

namespace MelisCmsPageHistoric\Controller;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use MelisCore\Controller\MelisAbstractActionController;

/**
 * API REST de l'onglet HISTORIQUE de l'éditeur de page CMS — MODULAIRE : vit dans SON module
 * (melis-cms-page-historic), pas dans melis-cms. Fournit les données de l'historique de page ;
 * le composant React est enregistré côté coquille via le registre __melisRegisterPageTab.
 * Route mergée via MelisCmsPageHistoric\Module::getConfig() (config/react-api.php).
 *
 *   GET /melis/react-api/cms-page/historic?idPage=X → { items: [{id, date, user, action}] }
 */
class MelisReactApiPageHistoricController extends MelisAbstractActionController
{
    public function listAction(): HttpResponse
    {
        if (!$this->getServiceManager()->get('MelisCoreAuth')->hasIdentity()) {
            return $this->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }
        try {
            $idPage = (int) $this->params()->fromQuery('idPage', 0);
            $db = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $rows = iterator_to_array($db->query(
                "SELECT h.hist_id AS id, h.hist_date AS date, h.hist_action AS action,
                        TRIM(CONCAT(COALESCE(u.usr_firstname,''), ' ', COALESCE(u.usr_lastname,''))) AS user,
                        h.hist_user_id AS userId
                 FROM melis_hist_page_historic h
                 LEFT JOIN melis_core_user u ON u.usr_id = h.hist_user_id
                 WHERE h.hist_page_id = ?
                 ORDER BY h.hist_id DESC", [$idPage]));
            // libeller un user supprimé
            foreach ($rows as &$r) {
                if (trim((string) $r['user']) === '') { $r['user'] = 'Utilisateur supprimé (' . $r['userId'] . ')'; }
            }
            return $this->json(['success' => true, 'data' => ['idPage' => $idPage, 'items' => array_values($rows)]]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function json(array $data, int $status = 200): HttpResponse
    {
        /** @var HttpResponse $r */
        $r = $this->getResponse();
        $r->setStatusCode($status);
        $r->getHeaders()->addHeaders(['Content-Type' => 'application/json; charset=utf-8', 'X-Content-Type-Options' => 'nosniff']);
        $r->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $r;
    }
}
