<?php
namespace TYPO3\Plugin\Blog\Service;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Blog".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * A notification service
 *
 * @Flow\Scope("singleton")
 */
class Notification {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Send a new notification that a comment has been created
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $commentNode The comment node
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $postNode The post node
	 * @return void
	 */
	public function sendNewCommentNotification(NodeInterface $commentNode, NodeInterface $postNode) {
		if (!isset($this->settings['notifications']['to']['email']) || $this->settings['notifications']['to']['email'] === '') {
			return;
		}
		if (!$this->packageManager->isPackageActive('TYPO3.SwiftMailer')) {
			$this->systemLogger->logException(new \TYPO3\Flow\Exception('the package "TYPO3.SwiftMailer" is required to send notifications!', 1359473932));
			return;
		}

		try {
			$mail = new \TYPO3\SwiftMailer\Message();
			$mail
				->setFrom(array($commentNode->getProperty('emailAddress') => $commentNode->getProperty('author')))
				->setTo(array($this->settings['notifications']['to']['email'] => $this->settings['notifications']['to']['name']))
				->setSubject('New comment on blog post "' . $postNode->getProperty('title') . '"' . ($commentNode->getProperty('spam') ? ' (SPAM)' : ''))
				->setBody($commentNode->getProperty('text'))
				->send();
		} catch (\Exception $e) {
			$this->systemLogger->logException($e);
		}
	}

}

?>