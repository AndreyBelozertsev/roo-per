<?php

namespace Portal\ContentBundle\Repository;

class SliderToBannerRepository extends \Doctrine\ORM\EntityRepository
{
    public function getUsedBannerIds($id)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT sb.banner, b.title'
            . ' FROM slider_to_banner as sb'
            . ' JOIN banner AS b ON sb.banner = b.id'
            . ' WHERE slider = ' . (int)$id
            . ' ORDER BY sort_order ASC';

        return $dc->fetchAll($sql) ?: [];
    }

    public function setBannersToSlider($orderIds, $sliderId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sliderId = (int)$sliderId;

        $dc->query("DELETE FROM slider_to_banner WHERE slider = $sliderId");

        $value = [];
        foreach (explode(',', $orderIds) as $k => $id) {
            $id = (int)$id;
            if ($id !== 0) {
                $value[] = '(' . $sliderId . ',' . $id . ',' . $k . ')';
            }
        }
        if (!empty($value)) {
            $values = implode(',', $value);

            $dc->query("INSERT INTO slider_to_banner (slider, banner, sort_order) VALUES $values");
        }
    }
}
