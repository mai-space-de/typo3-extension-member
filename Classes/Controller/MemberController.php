<?php

declare(strict_types = 1);

namespace Maispace\MaiMember\Controller;

use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class MemberController extends ActionController
{
    public function __construct(
        private readonly MemberRepository $memberRepository
    ) {
    }

    /**
     * Display a list of active members, optionally filtered by interest.
     */
    public function listAction(string $interest = ''): ResponseInterface
    {
        $members = $this->memberRepository->findActiveMembers($interest);

        $this->view->assignMultiple([
            'members'         => $members,
            'currentInterest' => $interest,
        ]);

        return $this->htmlResponse();
    }

    /**
     * Display the profile of a single member.
     */
    public function showAction(Member $member): ResponseInterface
    {
        $this->view->assign('member', $member);

        return $this->htmlResponse();
    }
}
