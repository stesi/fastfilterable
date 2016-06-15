<?php

namespace Stesi\PropelBehavior;

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
            $i = 0;
            $conditionArray = array(); 
            foreach ( $filtriElaborati as $filtro => $value ) {
            	$i++;
            	$conditionName = "fastFilter".$i;
            	$conditionArray[] = $conditionName;
                $this->condition( $conditionName, $filtro . " " . $value [\'operatore\'] . " ?", $value [\'valore\'] );
            }
            $this -> where($conditionArray,\'and\');
            return $this;
        }
        
        public function fastWith($withs) {
 
        	foreach ( $withs as $with) {
        		$this->withColumn ($with );
        	}
        	return $this;
        }
        
        public function fastGlobalSearch($columns,$globalFilter) {
        	$i = 0;
        	$conditionArray = array();
        	foreach ( $columns as $column) {
        		$i++;
        		$conditionName = "fastGlobalSearch".$i;
        		$conditionArray[] = $conditionName;
        		$this->condition($conditionName,$column." LIKE ?",\'%\'.$globalFilter.\'%\');
        	}
        	$this -> where($conditionArray,\'or\');
        	return $this;
        }		
				
				
				';
	
		return $script;
	}
}