<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Controller;

use Maispace\MaiBase\Controller\AbstractActionController;
use Maispace\MaiMember\Domain\Model\Application;
use Maispace\MaiMember\Domain\Repository\ApplicationRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ApplicationController extends AbstractActionController
{
    public function __construct(
        private readonly ApplicationRepository $applicationRepository,
    ) {}

    public function formAction(): ResponseInterface
    {
        $application = GeneralUtility::makeInstance(Application::class);
        $this->view->assign('application', $application);

        return $this->htmlResponse();
    }

    public function submitAction(Application $application): ResponseInterface
    {
        $application->setSubmittedAt((int)$GLOBALS['EXEC_TIME']);
        $application->setStatus('pending');

        $this->applicationRepository->add($application);

        $this->addFlashMessage(
            $this->translate('application.success.message'),
            $this->translate('application.success.title'),
        );

        return $this->redirect('form');
    }

    private function translate(string $key): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:mai_member/Resources/Private/Language/locallang.xlf:' . $key
        ) ?: $key;
    }

    private function getLanguageService(): \TYPO3\CMS\Core\Localization\LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
