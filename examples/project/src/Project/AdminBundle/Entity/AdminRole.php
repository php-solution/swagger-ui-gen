<?php
namespace Project\AdminBundle\Entity;

use PhpSolution\Doctrine\Entity\IdGeneratedTrait;
use PhpSolution\Doctrine\Entity\NameUniqueTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AdminStatus
 *
 * @package Project\AdminBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Project\AdminBundle\Repository\AdminRoleRepository")
 * @ORM\Table(name="admin_role")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class AdminRole
{
    use IdGeneratedTrait, NameUniqueTrait;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}