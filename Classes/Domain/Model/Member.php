<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;

class Member extends AbstractEntity
{
    protected string $firstName = '';
    protected string $lastName = '';
    protected string $position = '';
    protected string $email = '';
    protected string $phone = '';
    protected string $status = 'active';
    protected string $slug = '';
    protected int $joinDate = 0;

    #[Lazy]
    protected FileReference|LazyLoadingProxy|null $image = null;

    #[Lazy]
    protected AbstractEntity|LazyLoadingProxy|null $feUser = null;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getJoinDate(): int
    {
        return $this->joinDate;
    }

    public function setJoinDate(int $joinDate): void
    {
        $this->joinDate = $joinDate;
    }

    public function getImage(): ?FileReference
    {
        if ($this->image instanceof LazyLoadingProxy) {
            $this->image->_loadRealInstance();
        }

        return $this->image instanceof FileReference ? $this->image : null;
    }

    public function setImage(?FileReference $image): void
    {
        $this->image = $image;
    }

    public function getFeUser(): ?AbstractEntity
    {
        if ($this->feUser instanceof LazyLoadingProxy) {
            $this->feUser->_loadRealInstance();
        }

        return $this->feUser instanceof AbstractEntity ? $this->feUser : null;
    }

    public function setFeUser(?AbstractEntity $feUser): void
    {
        $this->feUser = $feUser;
    }
}
