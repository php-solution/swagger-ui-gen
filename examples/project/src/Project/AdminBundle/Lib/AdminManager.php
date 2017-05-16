<?php

namespace Project\AdminBundle\Lib;

use PhpSolution\Doctrine\Aware\DoctrineAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Project\AdminBundle\Entity\Admin;

/**
 * Class AdminManager
 *
 * @package Project\AdminBundle\Lib
 */
class AdminManager
{
    use DoctrineAwareTrait;

    /**
     * @param AdminListModel $adminListModel
     *
     * @return array|Admin[]
     */
    public function bulkCreate(AdminListModel $adminListModel): array
    {
        $result = [];
        $em = $this->doctrine->getManager();
        foreach ($adminListModel->getAdmins() as $model) {
            $result[] = $admin = (new Admin())
                ->setEmail($model->getEmail())
                ->setRoles($model->getRoles());
            $em->persist($admin);
        }
        $em->flush();

        return $result;
    }

    /**
     * @param AdminModel $model
     *
     * @return Admin
     */
    public function create(AdminModel $model): Admin
    {
        $admin = (new Admin())
            ->setEmail($model->getEmail())
            ->setRoles($model->getRoles());

        $em = $this->doctrine->getManager();
        $em->persist($admin);
        $em->flush();

        return $admin;
    }

    /**
     * @param Admin      $admin
     * @param AdminModel $model
     */
    public function update(Admin $admin, AdminModel $model): void
    {
        $admin
            ->setEmail($model->getEmail())
            ->setRoles($model->getRoles());

        $this->doctrine->getManager()->flush();
    }

    /**
     * @param Admin $admin
     */
    public function delete(Admin $admin): void
    {
        $em = $this->doctrine->getManager();
        $em->remove($admin);
        $em->flush();
    }
}