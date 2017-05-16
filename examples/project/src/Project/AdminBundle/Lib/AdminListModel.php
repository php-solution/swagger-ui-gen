<?php

namespace Project\AdminBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class AdminListModel
 *
 * @package Project\AdminBundle\Lib
 */
class AdminListModel
{
    /**
     * @var Collection
     */
    private $admins;

    /**
     * AdminListModel constructor.
     */
    public function __construct()
    {
        $this->admins = new ArrayCollection();
    }

    /**
     * @return Collection|AdminModel[]
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    /**
     * @param Collection $admins
     */
    public function setAdmins(Collection $admins)
    {
        $this->admins = $admins;
    }
}