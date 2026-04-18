<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Controller\Backend;

use Maispace\MaiBase\Controller\Backend\AbstractBackendController;
use Maispace\MaiBase\Controller\Traits\ResponseHelpersTrait;
use Maispace\MaiMember\Domain\Repository\ApplicationRepository;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;

#[AsController]
class MemberBackendController extends AbstractBackendController
{
    use ResponseHelpersTrait;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory,
        private readonly MemberRepository $memberRepository,
        private readonly ApplicationRepository $applicationRepository,
    ) {
        parent::__construct($moduleTemplateFactory, $iconFactory);
    }

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->createModuleTemplate();
        $this->addShortcutButton($moduleTemplate);

        $this->assignMultiple($moduleTemplate, [
            'members' => $this->memberRepository->findAll(),
            'applications' => $this->applicationRepository->findPending(),
        ]);

        return $this->renderModuleResponse($moduleTemplate, 'Index');
    }

    public function exportCsvAction(): ResponseInterface
    {
        $members = $this->memberRepository->findAll();

        $rows = [['first_name', 'last_name', 'email', 'phone', 'status', 'join_date']];
        foreach ($members as $member) {
            $rows[] = [
                $member->getFirstName(),
                $member->getLastName(),
                $member->getEmail(),
                $member->getPhone(),
                $member->getStatus(),
                $member->getJoinDate() > 0 ? date('Y-m-d', $member->getJoinDate()) : '',
            ];
        }

        return $this->csvResponse($rows, 'members.csv');
    }
}
