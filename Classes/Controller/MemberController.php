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
        $limit = (int)($settings['listLimit'] ?? 12);

        $members = $this->memberRepository->findActive();
        $pagination = $this->paginateQueryResult($members, $this->request, $limit);

        $this->view->assignMultiple([
            'members' => $pagination['paginatedItems'],
            'pagination' => $pagination['pagination'],
        ]);

        return $this->htmlResponse();
    }

    public function detailAction(?\Maispace\MaiMember\Domain\Model\Member $member = null): ResponseInterface
    {
        if ($member === null) {
            return $this->redirect('list');
        }

        $this->view->assign('member', $member);

        return $this->htmlResponse();
    }
}
