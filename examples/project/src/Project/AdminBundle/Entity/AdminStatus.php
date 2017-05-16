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
 * @ORM\Entity()
 * @ORM\Table(name="admin_status")
 * @ORM\Cache(usage="READ_ONLY")
 */
class AdminStatus
{
    use IdGeneratedTrait, NameUniqueTrait;

    public const ACTIVE = 1;
    public const REMOVED = 2;
}