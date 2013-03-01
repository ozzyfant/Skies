<?php

namespace skies\util;

/**
 * @author    Janek Ostendorf (ozzy) <ozzy2345de@gmail.com>
 * @copyright Copyright (c) Janek Ostendorf
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @package   skies.util
 */
class SessionUtil {


	public static function cleanUp() {

		$query = \Skies::$db->prepare('DELETE FROM `session` WHERE `sessionLong` = :long AND (`sessionLastActivity` + :length) < :now');

		// Normal sessions
		$query->execute([
			':long' => false,
		    ':length' => (30 * 60),
		    ':now' => NOW
		]);

		// Long sessions
		$query->execute([
	        ':long' => true,
	        ':length' => (365 * 86400),
	        ':now' => NOW
        ]);

	}

}

?>
