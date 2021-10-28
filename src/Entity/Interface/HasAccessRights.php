<?php

namespace App\Entity\Interface;

interface HasAccessRights
{
    public function getId(): ?int;
    public function getAccessRightOwnerName(): string;
}
