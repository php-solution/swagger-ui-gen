<?php
namespace Project\AdminBundle\Entity;

use PhpSolution\Doctrine\Entity\IdGeneratedTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Admin
 *
 * @package Project\AdminBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Project\AdminBundle\Repository\AdminRepository")
 * @ORM\Table(name="admin")
 */
class Admin
{
    use IdGeneratedTrait;

    /**
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     *
     * @var string
     */
    private $email;
    /**
     * @ORM\ManyToOne(targetEntity="AdminStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     *
     * @var AdminStatus|null
     */
    private $status;
    /**
     * @ORM\ManyToMany(targetEntity="AdminRole")
     * @ORM\JoinTable(
     *      name="admin_role_list",
     *      joinColumns={@ORM\JoinColumn(name="admin_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     *
     *
     * @var Collection|AdminRole[]
     */
    private $roles;

    /**
     * Admin constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Admin
     */
    public function setEmail(string $email): Admin
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|AdminStatus
     */
    public function getStatus():? AdminStatus
    {
        return $this->status;
    }

    /**
     * @param null|AdminStatus $status
     *
     * @return Admin
     */
    public function setStatus(?AdminStatus $status): Admin
    {
        $this->status = $status;

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
     * @return Admin
     */
    public function setRoles(Collection $roles): Admin
    {
        $this->roles = $roles;

        return $this;
    }
}