<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class MemberApplication extends AbstractEntity
{
    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;
    public const STATUS_ACTIVE = 3;

    protected string $applicantName = '';

    protected string $email = '';

    protected string $motivation = '';

    protected int $status = self::STATUS_PENDING;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $documents;

    #[Lazy]
    protected ?Member $member = null;

    public function __construct()
    {
        $this->documents = new ObjectStorage();
    }

    public function getApplicantName(): string
    {
        return $this->applicantName;
    }

    public function setApplicantName(string $applicantName): void
    {
        $this->applicantName = $applicantName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getMotivation(): string
    {
        return $this->motivation;
    }

    public function setMotivation(string $motivation): void
    {
        $this->motivation = $motivation;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getDocuments(): ObjectStorage
    {
        return $this->documents;
    }

    /**
     * @param ObjectStorage<FileReference> $documents
     */
    public function setDocuments(ObjectStorage $documents): void
    {
        $this->documents = $documents;
    }

    public function addDocument(FileReference $document): void
    {
        $this->documents->attach($document);
    }

    public function removeDocument(FileReference $document): void
    {
        $this->documents->detach($document);
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): void
    {
        $this->member = $member;
    }
}
