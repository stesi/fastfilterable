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
	$i = 0;
	$conditionArray = array();
	foreach ( $filtriElaborati as $filtro => $value ) {
		$i++;
		$conditionName = "fastFilter".$i;
		$conditionArray[] = $conditionName;
		$this->condition( $conditionName, $filtro . " " . $value ['operatore'] . " ?", $value ['valore'] );
	}
	$this -> where($conditionArray,'and');
	return $this;
}

public function fastWith($withs) {
	 
	foreach ( $withs as $with) {
		$this->withColumn ($with );
	}
	return $this;
}

public function fastOwner($entity,$me,$childs,$fathers)
{
$this->condition('sono_proprietario',$entity.".Proprietario = ?", $me)
->condition('nessun_proprietario',$entity.".Proprietario IS NULL")
->condition('proprietario_un_figlio',$entity.".Proprietario IN ?",$childs)
->condition('proprietario_padre_in',$entity.".roprietario IN ?",$fathers)
->condition('proprietario_padre_cond',"$entity.".Condiviso LIKE '%,0,%'")
->combine(array('proprietario_padre_in','proprietario_padre_cond'),'and','proprietario_padre')
->condition('condiviso_con_tutti',$entity.".Condiviso IS NULL")
->condition('condiviso_con_me',$entity.".Condiviso LIKE ?","%,".$me.",%")
->where(array('sono_proprietario','nessun_proprietario','proprietario_un_figlio','proprietario_padre','condiviso_con_tutti','condiviso_con_me'),'or')
}

public function fastGlobalSearch($columns,$globalFilter) {
	$i = 0;
	$conditionArray = array();
	foreach ( $columns as $column) {
		$i++;
		$conditionName = "fastGlobalSearch".$i;
		$conditionArray[] = $conditionName;
		$this->condition($conditionName,$column." LIKE ?",'%'.$globalFilter.'%');
	}
	$this -> where($conditionArray,'or');
	return $this;
}