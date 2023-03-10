<?php

namespace Portal\ContentBundle\Repository;

/**
 * WidgetToPanelRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WidgetToPanelRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllWidgetToPanelForPagination()
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql =
            " SELECT w2p.id as widget_to_panel_id "
            . ", w.label as widget_label, w2p.title as widget2panel_label "
            . ", w2p.is_published as widget_to_panel_is_published "
            . " FROM widget2panel AS w2p "
            . " INNER JOIN widget AS w ON w2p.widget_id = w.id "
            . " ORDER BY w2p.widget_order, w.id ASC "
        ;
        $result = $dc->executeQuery($sql)->fetchAll();

        return $result;
    }
    
    public function setWidgetToPanelOrder($orderIds)
    {
        $dc = $this->getEntityManager()->getConnection();

        $values = '';
        foreach (explode(',', $orderIds) as $i => $id) {
            $id = (int)$id;
            if ($id !== 0) {
                $values .= "WHEN id = {$id} THEN {$i} ";
            }
        }
        if ($values) {
            $dc->query("UPDATE widget2panel SET widget_order = CASE " . $values . 
                        "END
                        WHERE id IN ({$orderIds})");
            return true;
        }
        return false;
    }

    /**
     * 
     * 
     * @param type $codePanel
     * @param type $codePageTemplate
     * @return type
     */
    public function getListWidgetForPanel($codePanel, $codePageTemplate)
    {

        $dc = $this->getEntityManager()->getConnection();
        $sql =
            " SELECT w2p.id as widget_to_panel_id, w.label as widget_label "
            . " FROM widget2panel AS w2p "
            . " INNER JOIN widget AS w ON w2p.widget_id = w.id "
            . " INNER JOIN widget_panel AS wp ON w2p.panel_id = wp.id "
            . " INNER JOIN widget2panel2structure_template AS w2p2s_t ON w2p.id = w2p2s_t.widget2panel_id "
            . " INNER JOIN structure_template AS s_t ON s_t.id = w2p2s_t.structure_template_id "
            . " WHERE wp.slug LIKE '" . $codePanel . "' "
                . " AND w.is_published is true "
                . " AND w2p.is_published is true "
                . " AND s_t.code = '" . $codePageTemplate . "' "
            . " ORDER BY w2p.widget_order ASC "
        ;
        $result = $dc->executeQuery($sql)->fetchAll();

        return $result ?: false;
    }

    public function getCodeWidget($id)
    {

        $dc = $this->getEntityManager()->getConnection();
        $sql =
            " SELECT w.slug as widget_slug "
            . " FROM widget2panel AS w2p "
            . " INNER JOIN widget AS w ON w2p.widget_id = w.id "
            . " WHERE w2p.id = " . $id . " "
            . " AND w.is_published is true "
            . " AND w2p.is_published is true "
        ;
        $result = $dc->executeQuery($sql)->fetch(\PDO::FETCH_COLUMN);

        return $result ?: false;
    }
}
