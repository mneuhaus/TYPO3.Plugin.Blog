<?php
namespace TYPO3\Plugin\Blog\Controller;

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
use TYPO3\Flow\Mvc\Controller\ActionController;

/**
 * The posts controller for the Blog package
 *
 * @Flow\Scope("singleton")
 */
class PostController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 */
	protected $nodeRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Service\ContentTypeManager
	 */
	protected $contentTypeManager;

	/**
	 * List action for this controller. Displays latest posts
	 *
	 * @return void
	 */
	public function indexAction() {
		$currentNode = $this->nodeRepository->getContext()->getCurrentNode();
		$this->view->assign('currentNode', $currentNode);
	}

	public function showAction() {
		return 'Some Details DAWG';
	}

	/**
	 * @return void
	 */
	public function createAction() {
		$currentNode = $this->nodeRepository->getContext()->getCurrentNode();
		$postNode = $currentNode->createNode(uniqid('post-'), $this->contentTypeManager->getContentType('TYPO3.Plugin.Blog:Post'));
		$mainRequest = $this->request->getMainRequest();
		$mainUriBuilder = new \TYPO3\Flow\Mvc\Routing\UriBuilder();
		$mainUriBuilder->setRequest($mainRequest);
		$uri = $mainUriBuilder
			->reset()
			->setCreateAbsoluteUri(TRUE)
			->uriFor('show', array('node' => $postNode));
		$this->redirectToUri($uri);
	}
}
?>