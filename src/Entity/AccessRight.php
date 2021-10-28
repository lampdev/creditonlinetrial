<?php

namespace App\Entity;

use App\Entity\Interface\HasAccessRights;
use App\Repository\AccessRightRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AccessRightRepository::class)
 * @UniqueEntity(
 *     fields={"owner_type", "owner_id", "module", "function"},
 *     errorPath="port",
 *     message="This access permission is already set to this entity."
 * )
 * @ORM\Table(name="access_rights",indexes={
 *     @ORM\Index(name="owner_idx", columns={"owner_type", "owner_id"})
 * },uniqueConstraints={
 *     @ORM\UniqueConstraint(name="owner_target_idx",
 *     columns={"owner_type", "owner_id", "module", "function"})
 * })
 */
class AccessRight
{
    public const FUNCTION_WILDCARD = '*';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $owner_type;

    /**
     * @ORM\Column(type="integer")
     */
    private $owner_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $module;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $function;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnerType(): ?string
    {
        return $this->owner_type;
    }

    public function setOwnerType(string $owner_type): self
    {
        $this->owner_type = $owner_type;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->owner_id;
    }

    public function setOwnerId(int $owner_id): self
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    public function getOwner(): ?int
    {
        return $this->owner_id;
    }

    public function setOwner(HasAccessRights $owner): self
    {
        $this->owner_id = $owner->getId();

        return $this;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }
}
