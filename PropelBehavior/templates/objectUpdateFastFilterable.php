	/**
	 * Restituisce la query applicando tutti i filtri
	 *
	 * @param
	 *        	array \$filtri array dei filtri passati
	 *        	
	 * @return \$this|$queryClassName La query filtrata
	 */
	   public function fastFilter($filtri, $having = false) {
            $filtriElaborati = array ();
            foreach ( $filtri as $filtro => $value ) {
                if (! empty ( $value )) {
                    $name = explode ( "_", $filtro );
                    $type = $name [0];
                    array_shift ( $name );
                    $filtro = implode ( "_", $name );
                    switch ($type) {
                        
                        case "TextBox" :
                            $primoCarettere = substr ( $value, 0, 1 );
                                $ultimoCarattere = substr ( $value, - 1 );
                                if ($primoCarettere == '=' or $ultimoCarattere == '=') {
                                    $value=($value[0]=="="?substr($value,1):substr($value,0,strlen($value)-1));
                                    $filtriElaborati [$filtro] ['operatore'] = "=";
                                } else {
                                    $value="%".$value."%";
                                    $filtriElaborati [$filtro] ['operatore'] = "like";
                                }
                                $filtriElaborati [$filtro] ['valore'] = $value;
                                break;
                        case "Date" :
                            $date = explode ( "/", $value );
                            if (count ( $date ) == 2) {
                                $filtriElaborati [$filtro] ['operatore'] = "BETWEEN";
                                $filtriElaborati [$filtro] ['valore'] = $date;
                            } else if (count ( $date ) == 1) {
                                $filtriElaborati [$filtro] ['operatore'] = "=";
                                $filtriElaborati [$filtro] ['valore'] = date ( 'Y-m-d', strtotime ( implode ( $date ) ) );
                            }
                            break;
                          case "Select" :
                                if (! is_array ( $value ))
                                    $value = explode ( ",", $value );
                                if (is_array ( $value )) {
                                    $values = array ();
                                    //Se tra gli id della select trovo il valore *
                                    // non filtro per quella clausola
                                    
                                    $clausolaTutti=false;
                                    $clausolaYESNO=false;
                                    if(!in_array("*",$value)){
                                        foreach ( $value as $v ) {
                                            if (! empty ( $v )) {

                                                $v = explode ( ",", $v );
                                                if (is_array ( $v )){
                                                     if(in_array("*",$v)){
                                                         $clausolaTutti=true;
                                                         break;
                                                     }else {
                                                         if(in_array("YESNO",$v)){
                                                            unset($v[0]);
                                                         }
                                                         $clausolaYESNO=true;

                                                         $values = array_merge($values, $v);
                                                     }
                                                }
                                                else
                                                    $values [] = $v;
                                            }
                                        }
                                        if($clausolaYESNO){
                                            if(count($values)==1){
                                                if($values[0]=="0") {
                                                    $filtriElaborati [$filtro] ['operatore'] = "=";
                                                    $filtriElaborati [$filtro] ['valore'] = "0";
                                                }else{
                                                    $filtriElaborati [$filtro] ['operatore'] = ">=";
                                                    $filtriElaborati [$filtro] ['valore'] = "1";
                                                }
                                            }
                                        }else {
                                            if (!$clausolaTutti && count($values) > 0) {
                                                $filtriElaborati [$filtro] ['operatore'] = "IN";
                                                $filtriElaborati [$filtro] ['valore'] = $values;
                                            }
                                        }
                                    }
                                } else {
                                    if($value!="*"){
                                        $filtriElaborati [$filtro] ['operatore'] = "=";
                                        $filtriElaborati [$filtro] ['valore'] = $value;
                                    }
                                }
                                break;
                    }
                }
            }
            if (count ( $filtriElaborati ) > 0) {
                $i = 0;
                $conditionArray = array ();
                foreach ( $filtriElaborati as $filtro => $value ) {
                    $i ++;
                    $conditionName = "fastFilter" . $i;
                    $conditionArray [] = $conditionName;
                    if ($value ['operatore'] == "BETWEEN") {
                        $this->condition ( $conditionName, $filtro . " >=" . "?", date ( 'Y-m-d', strtotime ( $date [0] ) ) . " 00:00:00" );
                        $i ++;
                        $conditionArray [] = $conditionName . "_" . $i;
                        $this->condition ( $conditionName . "_" . $i, $filtro . " <=" . "?", date ( 'Y-m-d', strtotime ( $date [1] ) ) . " 23:59:59" );
                    } else {
                        $this->condition ( $conditionName, $filtro . " " . $value ['operatore'] . " ?", $value ['valore'] );
                    }
                }
                if (! $having) {
                    $this->where ( $conditionArray, 'and' );
                } else {
                    $this->having ( $conditionArray, 'and' );
                }
                
            }
            return $this;
        }
	        public function fastWith($withs) {
             $columnNames=array();
            foreach ( $withs as $with) {
                if(!empty($with['alias'])){
                    $this->withColumn ($with['name'],$with['alias'] );
                    $columnNames[]=$with['alias'];
                }else{
                    $this->withColumn ($with['name'] );
                    $columnNames[]=str_replace(".","",$with['name']);//$with['name'];
                }
            }
            if(count($columnNames)>0)
                $this->select($columnNames);
            
            return $this;
        }
	public function fastGlobalSearch($columns, $globalFilterValue, $having = false) {
		if (! empty ( $globalFilterValue )) {
			$i = 0;
			$conditionArray = array ();
			foreach ( $columns as $column ) {
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