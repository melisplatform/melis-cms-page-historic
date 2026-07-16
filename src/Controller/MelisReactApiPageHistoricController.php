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
 *   GET /melis/react-api/cms-page/historic?idPage=X&page=N&perPage=25
 *     → { items:[{id,date,user,action}], page, perPage, total }
 *
 * PAGINATION SERVEUR : l'historique d'une page peut grossir → on ne charge JAMAIS tout. La liste
 * est bornée par LIMIT/OFFSET (perPage, défaut 25, plafonné) ; `total` (COUNT indexé) pilote les
 * pages côté React.
 */
class MelisReactApiPageHistoricController extends MelisAbstractActionController
{
    public function listAction(): HttpResponse
    {
        if (!$this->getServiceManager()->get('MelisCoreAuth')->hasIdentity()) {
            return $this->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }
        try {
            $idPage  = (int) $this->params()->fromQuery('idPage', 0);
            $perPage = max(1, min(200, (int) $this->params()->fromQuery('perPage', 25)));
            $page    = max(1, (int) $this->params()->fromQuery('page', 1));
            $offset  = ($page - 1) * $perPage;
            $action  = trim((string) $this->params()->fromQuery('action', '')); // filtre SERVEUR par type d'action

            $db = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');

            // Filtre optionnel : appliqué à la fois au COUNT et à la page → filtre sur TOUT l'historique.
            $where  = 'WHERE h.hist_page_id = ?';
            $params = [$idPage];
            if ($action !== '') { $where .= ' AND h.hist_action = ?'; $params[] = $action; }

            $cnt = iterator_to_array($db->query("SELECT COUNT(*) AS total FROM melis_hist_page_historic h $where", $params));
            $total = (int) ($cnt[0]['total'] ?? 0);
            // $perPage/$offset = entiers validés → interpolation sûre (LIMIT/OFFSET ne se bindent pas
            // de façon portable). La page ne contient QUE perPage lignes.
            $rows = iterator_to_array($db->query(
                "SELECT h.hist_id AS id, h.hist_date AS date, h.hist_action AS action,
                        TRIM(CONCAT(COALESCE(u.usr_firstname,''), ' ', COALESCE(u.usr_lastname,''))) AS user,
                        h.hist_user_id AS userId
                 FROM melis_hist_page_historic h
                 LEFT JOIN melis_core_user u ON u.usr_id = h.hist_user_id
                 $where
                 ORDER BY h.hist_id DESC LIMIT $perPage OFFSET $offset", $params));
            // libeller un user supprimé
            foreach ($rows as &$r) {
                if (trim((string) $r['user']) === '') { $r['user'] = 'Utilisateur supprimé (' . $r['userId'] . ')'; }
            }
            unset($r);
            // Types d'action DISTINCTS (non filtrés) pour alimenter le sélecteur du filtre côté React.
            $types = iterator_to_array($db->query(
                'SELECT DISTINCT hist_action AS a FROM melis_hist_page_historic WHERE hist_page_id = ? ORDER BY hist_action', [$idPage]));
            $actionTypes = array_values(array_filter(array_map(static fn ($t) => (string) $t['a'], $types), static fn ($a) => $a !== ''));

            return $this->json(['success' => true, 'data' => [
                'idPage'      => $idPage,
                'items'       => array_values($rows),
                'page'        => $page,
                'perPage'     => $perPage,
                'total'       => $total,
                'action'      => $action,
                'actionTypes' => $actionTypes,
            ]]);
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
