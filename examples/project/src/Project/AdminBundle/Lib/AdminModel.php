<?php

namespace Project\AdminBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Project\AdminBundle\Entity\AdminRole;

/**
 * Class AdminModel
 *
 * @package Project\AdminBundle\Lib
 */
class AdminModel
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var Collection|AdminRole[]
     */
    private $roles;

    /**
     * AdminModel constructor.
     *
     * @param string|null     $email
     * @param Collection|null $roles
     */
    public function __construct(string $email = null, Collection $roles = null)
    {
        $this->email = $email;
        $this->roles = $roles ?: new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEmail():? string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return AdminModel
     */
    public function setEmail(?string $email): AdminModel
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|AdminRole[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param Collection|AdminRole[] $roles
     *
     * @return AdminModel
     */
    public function setRoles(Collection $roles): AdminModel
    {
        $this->roles = $roles;

        return $this;
    }
}