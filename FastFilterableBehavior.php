<?php

namespace Stesi\Behavior;

use Propel\Generator\Model\Behavior;

class FastFilterableBehavior extends Behavior {
	public function queryMethods($builder) {
		$script = '
	/**
 * Restituisce la query applicando tutti i filtri
 *
 * @param      array \$filtri array dei filtri passati
 *
 * @return     \$this|$queryClassName La query filtrata
 */
	 	public function fastFilter($filtri) {
		$filtriElaborati = array ();
		foreach ( $filtri as $filtro => $value ) {
			// Se il contenuto del filtro è un array allora faccio sempre IN
			if (is_array ( $value )) {
				$filtriElaborati [$filtro] [\'operatore\'] = "IN";
				$filtriElaborati [$filtro] [\'valore\'] = $value;
			} else {
				$primoCarettere = substr ( $value, 0, 1 );
				$ultimoCarattere = substr ( $value, - 1 );
				if ($primoCarettere == \'%\' or $ultimoCarattere == \'%\') {
					$filtriElaborati [$filtro] [\'operatore\'] = "like";
				} else {
					$filtriElaborati [$filtro] [\'operatore\'] = "=";
				}
				$filtriElaborati [$filtro] [\'valore\'] = $value;
			}
		}
		foreach ( $filtriElaborati as $filtro => $value ) {
			$this->where ( $filtro . " " . $value [\'operatore\'] . " ?", $value [\'valore\'] );
		}
		return $this;
	}';
	
		return $script;
	}
}