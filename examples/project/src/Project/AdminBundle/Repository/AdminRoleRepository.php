<?php

namespace Project\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Project\AdminBundle\Entity\AdminRole;

/**
 * Class AdminRoleRepository
 *
 * @package Project\AdminBundle\Repository
 */
class AdminRoleRepository extends EntityRepository
{
    /**
     * @param array|int[] $ids
     *
     * @return array|AdminRole[]
     */
    public function getListByIds(array $ids): array
    {
        return (0 === count($ids))
            ? []
            : array_filter(
                $this->findAll(),
                function (AdminRole $role) use ($ids) {
                    return in_array($role->getId(), $ids, true);
                }
            );
    }
}