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
            	if (!empty($value))
            	{
            	$name=explode("_",$filtro);
            	$type=$name[0];
            	array_shift($name);
            	$filtro=implode("_",$name);
            	switch($type){
            		
            		case "TextBox":
            			$primoCarettere = substr ( $value, 0, 1 );
            			$ultimoCarattere = substr ( $value, - 1 );
            			if ($primoCarettere == '%' or $ultimoCarattere == '%') {
            				$filtriElaborati [$filtro] ['operatore'] = "like";
            			} else {
            				$filtriElaborati [$filtro] ['operatore'] = "=";
            			}
            			$filtriElaborati [$filtro] ['valore'] = $value;
            			break;
            		case "Date":
            			$date=explode("/",$value);
            			if(count($date)==2){
            				$filtriElaborati [$filtro] ['operatore'] = "BETWEEN";
            				$filtriElaborati [$filtro]['valore']=$date;
            			}else if(count($date)==1){
            				$filtriElaborati [$filtro] ['operatore'] = "=";
            				$filtriElaborati [$filtro]['valore']=implode($date);
            			}
            			break;
            		case "Select":            
            			if(!is_array($value))
            				$value=explode(",",$value);
            			if (is_array ( $value )) {
            				$filtriElaborati [$filtro] ['operatore'] = "IN";
            				$filtriElaborati [$filtro] ['valore'] = $value;
            			} else {
            				$filtriElaborati [$filtro] ['operatore'] = "=";
            				$filtriElaborati [$filtro] ['valore'] = $value;
            			}
            			break;
            		
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
                if($value['operatore']=="BETWEEN"){
                	$this->condition( $conditionName,$filtro . " >=" . "?", $value ['valore'][0] );
                	$conditionArray[] = $conditionName."_".$i;
                	$this->condition( $conditionName."_".$i,$filtro . " <=" . "?", $value ['valore'][1] );
                }
                else{
                	$this->condition( $conditionName, $filtro . " " . $value ['operatore'] . " ?", $value ['valore'] );
                }
            }
            $this -> where($conditionArray,'and');
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