<?php
namespace TYPO3\Plugin\Blog\OptionsProvider;

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
 */
class PluginViewsConfigurationProcessor {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	public function __construct($contentType, $configuration) {
		$this->contentType = $contentType;
		$this->configuration = $configuration;
	}

	public function getConfiguration() {
		$plugins = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Neos.Plugins');

		$this->configuration['groups']['pluginViews'] = array(
			'label' => 'Plugin Views',
			'priority' => '100'
		);

		$pluginOptions = array(
			'' => array(
				'label' => ''
			)
		);
		$pluginViewOptions = array();
		foreach ($plugins as $plugin => $pluginConfiguration) {
			$contentElements = $this->getContentElements($plugin);
			foreach ($contentElements as $contentElement) {
				$page = $contentElement->getParent('TYPO3.Neos.ContentTypes:Page');
				$pluginOptions[$contentElement->getPath()] = array(
					'label' => $pluginConfiguration['label'] . ' on ' . $page->getProperty('title')
				);
			}
			if (count($contentElements) > 0) {
				foreach ($pluginConfiguration['pluginViews'] as $view => $viewConfiguration) {
					$pluginViewOptions[$view] = array(
						'label' => $viewConfiguration['label']
					);
				}
			}
		}

		$this->configuration['properties']['plugin'] = array(
			'type' => 'enum',
			'label' => 'Master Plugin',
			'group' => 'pluginViews',
			'options' => array(
				'values' => $pluginOptions
			)
		);

		$this->configuration['properties']['view'] = array(
			'type' => 'enum',
			'label' => 'View',
			'group' => 'pluginViews',
			'options' => array(
				'values' => $pluginViewOptions
			)
		);

		return $this->configuration;
	}

	public function getContentElements($contentType) {
		$siteNode = $this->nodeRepository->getContext()->getCurrentSiteNode();
		return $this->nodeRepository->findBeneathParentAndContentType($siteNode->getPath(), $contentType, $this->nodeRepository->getContext()->getWorkspace());
	}
}
?>