<?php
namespace TYPO3\Plugin\Blog\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Plugin.Blog".	  *
 *                                                                        *
 *                                                                        */

use TYPO3\Neos\Domain\Service\ContentContext;
use TYPO3\Flow\Annotations as Flow;

/**
 * Blog command controller for the TYPO3.Plugin.Blog package
 *
 * @Flow\Scope("singleton")
 */
class BlogCommandController extends \TYPO3\Flow\Cli\CommandController {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Http\Client\Browser
	 */
	protected $browser;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Http\Client\CurlEngine
	 */
	protected $browserRequestEngine;

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
	 * Import Posts from an Feed
	 *
	 * Use this command to import an RSS feed into NEOS
	 *
	 * ./flow blog:import --pagePath '/sites/neosdemotypo3org/homepage/blog' --url 'http://www.planetflow3.com/rss.xml'
	 *
	 * @param string $url This argument is required
	 * @param string $pagePath
	 * @return void
	 */
	public function importCommand($url, $pagePath = '/sites/neosdemotypo3org/homepage/blog') {
		$this->browser->setRequestEngine($this->browserRequestEngine);
		$response = $this->browser->request($url);
		$feed = new \SimpleXMLElement($response->getContent());

		if ($this->nodeRepository->getContext() === NULL) {
			$contentContext = new ContentContext('live');
			$contentContext->setInvisibleContentShown(TRUE);
			$this->nodeRepository->setContext($contentContext);
		} else {
			$contentContext = $this->nodeRepository->getContext();
		}

		$workspace = $contentContext->getWorkspace();
		$blogNode = $this->nodeRepository->findOneByPath($pagePath, $workspace);
		foreach ($feed->channel->item as $item) {
			$postNode = $blogNode->createNode(uniqid('post-'), $this->contentTypeManager->getContentType('TYPO3.Plugin.Blog:Post'));
			$postNode->setProperty('datePublished', new \DateTime($item->pubDate));
			$postNode->setProperty('title', strval($item->title));
			$postNode->setProperty('description', strval($item->description));
			$postNode->setProperty('permalink', strval($item->link));

			$sectionNode = $postNode->createNode('main', $this->contentTypeManager->getContentType('TYPO3.Neos.ContentTypes:Section'));

			$htmlNode = $sectionNode->createNode(uniqid('postContent-'), $this->contentTypeManager->getContentType('TYPO3.Neos.ContentTypes:Html'));

			$content = $item->children('http://purl.org/rss/1.0/modules/content/');
			if (strlen($content->encoded) > 0) {
				$content = $content->encoded;
			} else {
				$content = $item->description;
			}
			$htmlNode->setProperty('source', strval($content));
		}
	}

}

?>