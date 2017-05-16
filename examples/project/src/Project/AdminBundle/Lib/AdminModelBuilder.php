<?php

namespace Project\AdminBundle\Lib;

use PhpSolution\Doctrine\Aware\DoctrineAwareTrait;
use Project\AdminBundle\Entity\Admin;
use Project\AdminBundle\Entity\AdminRole;

/**
 * Class AdminModelBuilder
 *
 * @package Project\AdminBundle\Lib
 */
class AdminModelBuilder
{
    use DoctrineAwareTrait;

    /**
     * @param Admin $admin
     *
     * @return AdminModel
     */
    public function createByAdmin(Admin $admin, array $data = []): AdminModel
    {
        $result = new AdminModel($admin->getEmail(), $admin->getRoles()->toArray());
        if (!empty($data)) {
            $this->handleData($result, $data);
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return AdminModel
     */
    public function createByData(array $data): AdminModel
    {
        return $this->handleData(new AdminModel(null), $data);
    }

    /**
     * @param AdminModel $adminModel
     * @param array      $data
     *
     * @return AdminModel
     */
    public function handleData(AdminModel $adminModel, array $data): AdminModel
    {
        if (!$this->validateHandledData($data)) {
            throw new \InvalidArgumentException();
        }
        $email = $data['email'] ?? null;
        $roleIds = $data['roles'] ?? [];
        $roles = $this->doctrine->getRepository(AdminRole::class)->getListByIds($roleIds);

        $adminModel->setEmail($email);
        $adminModel->setRoles($roles);

        return $adminModel;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function validateHandledData(array $data): bool
    {
        return true;
    }
}