<?php

declare(strict_types=1);

namespace Maispace\Member\Event;

use Maispace\Member\Domain\Model\Member;
use Maispace\Member\Domain\Model\MemberApplication;

final class MemberApprovedEvent
{
    public function __construct(
        private readonly Member $member,
        private readonly MemberApplication $application
    ) {}

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getApplication(): MemberApplication
    {
        return $this->application;
    }
}
