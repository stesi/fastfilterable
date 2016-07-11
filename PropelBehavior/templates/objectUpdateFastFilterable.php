/**
 * Restituisce la query applicando tutti i filtri
 *
 * @param      array \$filtri array dei filtri passati
 *
 * @return     \$this|$queryClassName La query filtrata
 */
     public function fastFilter($filtri,$having=false) {
        $filtriElaborati = array ();
        foreach ( $filtri as $filtro => $value ) {
        	if (!empty($value))
        	{
            // Se il contenuto del filtro è un array allora faccio sempre IN
            if (is_array ( $value )) {
                $filtriElaborati [$filtro] ['operatore'] = "IN";
                $filtriElaborati [$filtro] ['valore'] = $value;
            } else {
                $primoCarettere = substr ( $value, 0, 1 );
                $ultimoCarattere = substr ( $value, - 1 );
                if ($primoCarettere == '%' or $ultimoCarattere == '%') {
                    $filtriElaborati [$filtro] ['operatore'] = "like";
                } else {
                    $filtriElaborati [$filtro] ['operatore'] = "=";
                }
                $filtriElaborati [$filtro] ['valore'] = $value;
            }
        	}
        }
        if (count($filtriElaborati)>0)
        {
        $i = 0;
        $conditionArray = array();
        foreach ( $filtriElaborati as $filtro => $value ) {
            $i++;
            $conditionName = "fastFilter".$i;
            $conditionArray[] = $conditionName;
            $this->condition( $conditionName, $filtro . " " . $value ['operatore'] . " ?", $value ['valore'] );
        }
        if(!$having)
        	$this -> where($conditionArray,'and');
        else 
        	$this -> having($conditionArray,'and');
        }
        return $this;
    }
    
public function fastWith($withs) {
	 
	foreach ( $withs as $with) {
		if(!empty($with['alias'])){
			$this->withColumn ($with['name'],$with['alias'] );
		}else{
			$this->withColumn ($with['name'] );
		}
	}
	return $this;
}

public function fastGlobalSearch($columns,$globalFilterValue,$having=false) {
	if (!empty($globalFilterValue))
	{
	$i = 0;
	$conditionArray = array();
	foreach ( $columns as $column) {
			$i++;
			$conditionName = "fastGlobalSearch".$i;
			$conditionArray[] = $conditionName;
			$this->condition($conditionName,$column." LIKE ?",'%'.$globalFilterValue.'%');
	}
	if(!$having)
		$this -> where($conditionArray,'or');
	else
		$this -> having($conditionArray,'or');
	}
	return $this;
}