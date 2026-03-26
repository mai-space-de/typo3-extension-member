<?php

declare(strict_types = 1);

namespace Maispace\MaiMember\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Member extends AbstractEntity
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_SUSPENDED = 2;

    protected string $name = '';
    protected int $status = self::STATUS_INACTIVE;
    protected ?\DateTimeImmutable $entryDate = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $photo;

    protected string $interests = '';

    #[Lazy]
    protected ?FrontendUser $feUser = null;

    public function __construct()
    {
        $this->photo = new ObjectStorage();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getEntryDate(): ?\DateTimeImmutable
    {
        return $this->entryDate;
    }

    public function setEntryDate(?\DateTimeImmutable $entryDate): void
    {
        $this->entryDate = $entryDate;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getPhoto(): ObjectStorage
    {
        return $this->photo;
    }

    /**
     * @param ObjectStorage<FileReference> $photo
     */
    public function setPhoto(ObjectStorage $photo): void
    {
        $this->photo = $photo;
    }

    public function addPhoto(FileReference $photo): void
    {
        $this->photo->attach($photo);
    }

    public function removePhoto(FileReference $photo): void
    {
        $this->photo->detach($photo);
    }

    public function getInterests(): string
    {
        return $this->interests;
    }

    public function setInterests(string $interests): void
    {
        $this->interests = $interests;
    }

    /**
     * Returns interests as an array of trimmed strings.
     *
     * @return list<string>
     */
    public function getInterestsArray(): array
    {
        if ($this->interests === '') {
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim', explode(',', $this->interests))
            )
        );
    }

    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }

    public function setFeUser(?FrontendUser $feUser): void
    {
        $this->feUser = $feUser;
    }
}
