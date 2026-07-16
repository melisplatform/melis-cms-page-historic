<?php

/**
 * Capacités React apportées par MelisCmsPageHistoric — droits avancés du back-office React.
 *
 * MODULARITÉ : ce module CONTRIBUE son onglet « Historique » à l'éditeur de page CMS sous la MÊME
 * clé `meliscms_page` que MelisCms/SmallBusiness — ArrayUtils::merge fusionne les `tabs` (append).
 * L'onglet devient gatable dans Users→Droits (nœud « Edition de page »). La `key` = melisKey de
 * l'onglet (= son cap côté gating React). Mergé dans Module::getConfig().
 */

return [
    'melisReactToolCapabilities' => [
        'meliscms_page' => [
            'tabs' => [
                ['key' => 'melispagehistoric_historic', 'label' => 'tr_melispagehistoric_page_tab_historic_Historic'],
            ],
        ],
    ],
];
