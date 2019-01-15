<?php
	// Assign the correct language
	if (isset($_SESSION['id_user']) && !empty($_SESSION['sys_lang'])) {
		$usr_lang = $_SESSION['sys_lang'];
			
		if (!file_exists(DOCROOT . '/languages/' . $usr_lang . '.php')) {
			if (DEBUG) echo 'Language file "' . $usr_lang . '.php" not found!  Defaulting to English.<br />';
			require(DOCROOT . '/languages/english.php');
		} else {
			unset($lang);
			require(DOCROOT . '/languages/' . $usr_lang . '.php');
		}
	} else {
		require(DOCROOT.'/languages/' . $settings['def_lang'] . '.php');
	}
?>
