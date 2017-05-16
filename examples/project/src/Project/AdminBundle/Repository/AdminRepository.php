<?php

namespace Project\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Project\AdminBundle\Entity\Admin;

/**
 * Class AdminRepository
 *
 * @package Project\AdminBundle\Repository
 */
final class AdminRepository extends EntityRepository
{
    private const LIST_LIMIT = 10;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $offset
     *
     * @return array|Admin[]
     */
    public function getList(int $offset = 0): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'status', 'roles')
            ->leftJoin('a.status', 'status')
            ->leftJoin('a.roles', 'roles')
            ->setMaxResults(self::LIST_LIMIT)
            ->setFirstResult($offset)
            ->getQuery()
            ->getArrayResult();
    }
}