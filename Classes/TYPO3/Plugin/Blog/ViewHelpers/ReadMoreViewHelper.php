<?php
namespace TYPO3\Plugin\Blog\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Blog".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This view helper crops the text of a blog post in a meaningful way.
 *
 * @api
 */
class ReadMoreViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Render the read more text
	 *
	 * @return string cropped text
	 */
	public function render() {
		$stringToTruncate = $this->renderChildren();
		$jumpPosition = strpos($stringToTruncate, '<!-- read more -->');

		if ($jumpPosition !== FALSE) {
			return $this->stripUnwantedTags(substr($stringToTruncate, 0, ($jumpPosition - 1)));
		}

		$jumpPosition = strpos($stringToTruncate, '</p>');
		if ($jumpPosition !== FALSE && $jumpPosition < 200) {
			return $this->stripUnwantedTags(substr($stringToTruncate, 0, $jumpPosition + 3));
		}

		if (strlen($stringToTruncate) > 200) {
			return $this->stripUnwantedTags(substr($stringToTruncate, 0, 200) . ' ...');
		} else {
			return $this->stripUnwantedTags($stringToTruncate);
		}
	}

	/**
	 * If the content starts with <p> and ends with </p> these tags are stripped.
	 *
	 * @param string $content The original content
	 * @return string The stripped content
	 */
	protected function stripUnwantedTags($content) {
		$content = trim($content);
		$content = preg_replace(array('/\\<a [^\\>]+\\>/', '/\<\\/a\\>/'), '', $content);
		if (substr($content, 0, 3) === '<p>' && substr($content, -4, 4) === '</p>') {
			$content = substr($content, 3, -4);
		}
		return $content;
	}
}


?>