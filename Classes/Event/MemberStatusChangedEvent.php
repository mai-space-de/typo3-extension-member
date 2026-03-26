<?php

declare(strict_types = 1);

namespace Maispace\MaiMember\Event;

use Maispace\MaiMember\Domain\Model\Member;

final class MemberStatusChangedEvent
{
    public function __construct(
        private readonly Member $member,
        private readonly int $previousStatus,
        private readonly int $newStatus
    ) {
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getPreviousStatus(): int
    {
        return $this->previousStatus;
    }

    public function getNewStatus(): int
    {
        return $this->newStatus;
    }
}
