<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Controller;

use Maispace\MaiBase\Controller\AbstractActionController;
use Maispace\MaiBase\Controller\Traits\PaginationTrait;
use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * List over Member doktype pages. Detail renders the current Member page.
 */
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

        $pageUids = $this->resolveParentPageUids();
        if ($pageUids !== []) {
            $members = $this->memberRepository->findFromPagesByStatus($pageUids, 'active');
        } else {
            $members = $this->memberRepository->findActive();
        }

        $pagination = $this->paginateQueryResult($members, $limit);

        $this->view->assignMultiple([
            'members' => $pagination['paginator'],
            'pagination' => $pagination['pagination'],
            'settings' => $settings,
        ]);

        return $this->htmlResponse();
    }

    public function detailAction(?Member $member = null): ResponseInterface
    {
        if ($member === null) {
            $member = $this->resolveMemberFromCurrentPage();
        }

        if ($member === null) {
            $message = LocalizationUtility::translate('member.notFound', 'mai_member') ?? 'Member not found.';

            return $this->htmlResponse('<p class="mai-member__empty">' . htmlspecialchars($message) . '</p>');
        }

        $isLoggedIn = $this->isFrontendUserLoggedIn();
        $hasFeUser = $member->getFeUser() !== null;

        $this->view->assignMultiple([
            'member' => $member,
            'isLoggedIn' => $isLoggedIn,
            'hasFeUser' => $hasFeUser,
            'settings' => $this->getSettings(),
        ]);

        return $this->htmlResponse();
    }

    private function resolveMemberFromCurrentPage(): ?Member
    {
        $routing = $this->request->getAttribute('routing');
        $pageUid = $routing instanceof PageArguments ? $routing->getPageId() : 0;
        if ($pageUid <= 0) {
            return null;
        }

        return $this->memberRepository->findByUid($pageUid);
    }

    /**
     * @return list<int>
     */
    private function resolveParentPageUids(): array
    {
        $pages = trim((string) ($this->settings['pages'] ?? ''));
        if ($pages === '') {
            return [];
        }

        return array_values(array_filter(
            array_map(static fn(string $uid): int => (int) $uid, explode(',', $pages)),
            static fn(int $uid): bool => $uid > 0,
        ));
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
