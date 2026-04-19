<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Service;

use Maispace\MaiMail\Service\MailService;
use Maispace\MaiMember\Domain\Model\Application;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class MemberMailer
{
    public function __construct(
        private readonly MailService $mailService,
    ) {
    }

    public function sendApplicationReceived(Application $application): void
    {
        if ($application->getEmail() === '') {
            return;
        }

        $view = $this->createView('ApplicationReceived');
        $view->assign('application', $application);

        $subject = (string)(LocalizationUtility::translate('email.applicationReceived.subject', 'mai_member')
            ?: 'We received your application');

        $this->mailService->queue($application->getEmail(), $subject, $view->render());
    }

    public function sendApplicationApproved(Application $application): void
    {
        if ($application->getEmail() === '') {
            return;
        }

        $view = $this->createView('ApplicationApproved');
        $view->assign('application', $application);

        $subject = (string)(LocalizationUtility::translate('email.applicationApproved.subject', 'mai_member')
            ?: 'Your application has been approved');

        $this->mailService->queue($application->getEmail(), $subject, $view->render());
    }

    public function sendApplicationRejected(Application $application): void
    {
        if ($application->getEmail() === '') {
            return;
        }

        $view = $this->createView('ApplicationRejected');
        $view->assign('application', $application);

        $subject = (string)(LocalizationUtility::translate('email.applicationRejected.subject', 'mai_member')
            ?: 'Update on your application');

        $this->mailService->queue($application->getEmail(), $subject, $view->render());
    }

    private function createView(string $templateName): StandaloneView
    {
        $view = new StandaloneView();
        $view->setTemplateRootPaths(['EXT:mai_member/Resources/Private/Templates/Email/']);
        $view->setPartialRootPaths(['EXT:mai_member/Resources/Private/Partials/']);
        $view->setLayoutRootPaths(['EXT:mai_member/Resources/Private/Layouts/']);
        $view->setTemplate($templateName);
        $view->setFormat('html');

        return $view;
    }
}
