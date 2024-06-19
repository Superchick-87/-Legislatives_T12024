<?php
	/*----------  Sert à remplacer le nom de l'éditeur dans la phrase d'intro  ----------*/
	/**
	 *
	 * Récupération via l'url $_GET['editeur']
	 *
	 */
	
	function personalisation($x){
		if ($x == 'So' ) {
			$x = 'Sud Ouest';
			return $x;
		}
		if ($x == 'Pp' ) {
			$x = 'La République des Pyrénées';
			return $x;
		}
		if ($x == 'Cl' ) {
			$x = 'Charente Libre';
			return $x;
		}
	}

?>