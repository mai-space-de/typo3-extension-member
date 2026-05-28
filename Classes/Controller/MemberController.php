<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Controller;

use Maispace\MaiBase\Controller\AbstractActionController;
use Maispace\MaiBase\Controller\Traits\PaginationTrait;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Psr\Http\Message\ResponseInterface;

class MemberController extends AbstractActionController
{
    use PaginationTrait;

    public function __construct(
        private readonly MemberRepository $memberRepository,
    ) {}

    public function listAction(): ResponseInterface
    {
        $settings = $this->getSettings();
        $limit = (int) ($settings['listLimit'] ?? 12);

        $members = $this->memberRepository->findActive();
        $pagination = $this->paginateQueryResult($members, $limit);

        $this->view->assignMultiple([
            'members' => $pagination['paginator'],
            'pagination' => $pagination['pagination'],
        ]);

        return $this->htmlResponse();
    }

    public function detailAction(?\Maispace\MaiMember\Domain\Model\Member $member = null): ResponseInterface
    {
        if ($member === null) {
            return $this->redirect('list');
        }

        $isLoggedIn = $this->isFrontendUserLoggedIn();
        $hasFeUser = $member->getFeUser() !== null;

        $this->view->assignMultiple([
            'member' => $member,
            'isLoggedIn' => $isLoggedIn,
            'hasFeUser' => $hasFeUser,
        ]);

        return $this->htmlResponse();
    }

    private function isFrontendUserLoggedIn(): bool
    {
        if (!isset($GLOBALS['TSFE'])) {
            return false;
        }

        $feUser = $GLOBALS['TSFE']->fe_user;
        if (!$feUser instanceof \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication) {
            return false;
        }

        $user = $feUser->user;
        return is_array($user) && !empty($user['uid']);
    }
}
