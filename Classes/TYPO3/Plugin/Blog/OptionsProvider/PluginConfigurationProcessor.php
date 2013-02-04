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
class PluginConfigurationProcessor {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	public function __construct($contentType, $configuration) {
		$this->contentType = $contentType;
		$this->configuration = $configuration;
	}

	public function getConfiguration() {
		// $this->configuration['properties']['spam'] = array(
		// 	'type' => 'enum',
		// 	'label' => 'Spam',
		// 	'group' => 'pluginViews',
		// 	'optionsProvider' => '\TYPO3\Plugin\Blog\OptionsProvider\TestOptionsProvider'
		// );
		return $this->configuration;
	}
}
?>