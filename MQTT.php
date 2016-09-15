<?php
require("phpMQTT/phpMQTT.php");
class MQTT {
	/**
	 * Occurs after the save page request has been processed.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/PageContentSaveComplete
	 *
	 * @param WikiPage $article
	 * @param User $user
	 * @param Content $content
	 * @param string $summary
	 * @param boolean $isMinor
	 * @param boolean $isWatch
	 * @param $section Deprecated
	 * @param integer $flags
	 * @param {Revision|null} $revision
	 * @param Status $status
	 * @param integer $baseRevId
	 *
	 * @return boolean
	 */
	public static function onPageContentSaveComplete( $article, $user, $content, $summary,
			$isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId ) {

		global $wgMQTTTopic, $wgMQTTServerAdress, $wgMQTTServerPort, $wgMQTTClientId, $wgMQTTUsername, $wgMQTTPassword;

		$output['article'] = $article;
		$output['user'] = array(
			"mId" => $user->mId,
			"mName" => $user->mName,
			"mRealName" => $user->mRealName
		);
		$output['summary'] = $summary;
		$output['isMinor'] = $isMinor;
		$output['flags'] = $flags;
		$output['revision'] = $revision;
		$output['status'] = $status;
		$output['baseRevId'] = $baseRevId;
		$json = json_encode($output, JSON_PRETTY_PRINT);

		$mqtt = new phpMQTT($wgMQTTServerAdress, $wgMQTTServerPort, $wgMQTTClientId);
		if ($mqtt->connect(true, NULL, $wgMQTTUsername, $wgMQTTPassword)) {
			$mqtt->publish($wgMQTTTopic, $json, 1, false);
			$mqtt->close();
		}

		return true;	

	}
}
