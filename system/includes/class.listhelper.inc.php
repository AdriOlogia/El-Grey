<?php
/*
	Clase para ayudar con los listados del sistema.
	
	Modif: 2020-02-10
	Desc: Agregada propiedad ListadoMgr para poner el nombre (identificador) de la instancia del objeto JavaScript mgrListadoCreator que maneja el listado (se usa al armar el paginador).
	
	Modif: 2020-03-10
	Desc: Agregado método MakeOrderBy para armar la lista de campos para la cláusula ORDER BY.
	
*/

class ListHelper {
	public $ordenes = null;
	public $camposorden = null;
	public $ordenClass = 'ord';
	public $OrdenwithTitle = false;
	public $ItemsPorPagina = 25; // cuántos registros hay en la consulta.
	public $PaginaActual = 1; // el índice de la página actual.
	public $Rango = 3; // cuántos items hay que mostrar en cada página.
	public $CantExtremos = 5; // cuántas páginas mostrar al inicio y al final.
	public $ItemsTotales = 0;
	public $ListadoMgr = 'listado'; // Cómo se llama la instancia de mgrListadoCreator (JS)

	function Orden($indice) {
		echo $this->ordenClass;
		if (isset($this->camposorden[$indice])) {
			$field_name = $this->camposorden[$indice];
			if (array_key_exists($field_name,$this->ordenes)) {
				echo ' '.strtolower($this->ordenes[$field_name]);
			}
		}
		if ($this->OrdenwithTitle) {
			echo '" title="Ordenar por esta columna';
		}
	}
/*
	Esto arma el componente de paginación de los listados.
*/
	function Paginar() {
		if ($this->ItemsTotales == 0) { return false; }
		$result = array();
		// $MedioRango = ($this->Rango-1)/2;
		$MedioRango = $this->Rango;
		$TotalDePagina = ceil($this->ItemsTotales / $this->ItemsPorPagina);
		$ContPuntos = false;
		for ($p = 1; $p <= $TotalDePagina; $p++) {
			// do we display a link for this page or not?
			if ($p == 1 and ($TotalDePagina > 1)) {
				if (($this->PaginaActual-1) > 1) {
					$result[] = array("p"=>$this->PaginaActual-1,"act"=>false,"arr"=>"prev");
				}
			}
			if (
				($p <= $this->CantExtremos) or
				($p > ($TotalDePagina - $this->CantExtremos)) or
				(($p >= $this->PaginaActual - $MedioRango) and ($p <= $this->PaginaActual + $MedioRango)) or
				($p == $this->CantExtremos + 1 and $p == $this->PaginaActual - $MedioRango - 1) or
				($p == $TotalDePagina - $this->CantExtremos and $p == $this->PaginaActual + $MedioRango + 1 )
				)
				{
				$ContPuntos = false;
				if ($p == $this->PaginaActual) {
						$result[] = array("p"=>$p, "act"=>true);
				} else {
					$result[] = array("p"=>$p, "act"=>false);
				}
				echo "\n";
			// if not, have we already shown the elipses? 
			} elseif ($ContPuntos == false) { 
				$result[] = array("p"=>null, "act"=>false);
				$ContPuntos=true; // make sure we only show it once
			}
			if ($p == $TotalDePagina and ($TotalDePagina > 1)) {
				if (($this->PaginaActual+1) < $TotalDePagina) {
					$result[] = array("p"=>$this->PaginaActual+1,"act"=>false,"arr"=>"next");
				}
			}
		}
		$this->paginacion = $result;
		return $result;
	}
/*
	Esto escribe los tags de paginación directamente.
*/
	public function MostrarPaginacion() {
		if (!CanUseArray($this->paginacion)) { return; }
		?><nav aria-label="Page navigation example"><ul class="pagination"><?php
			foreach($this->paginacion as $value) {
			?><li class="page-item<?php echo ($value['act'])?' active':'';?>"><?php
				if (empty($value['p'])) {
				?><span class="page-link">&hellip;</span><?php
				} else {
					if ($value['act']) {
						?><span class="page-link active"><?php echo $value['p']; ?></span><?php
					} else {
						if (isset($value['arr'])) {
							if ($value['arr'] == 'prev') {
								?><a class="page-link" href="#!" aria-label="Anterior" onClick="<?php echo $this->ListadoMgr; ?>.Get({name:'pag',value:<?php echo $value['p'];?>});return false;"><span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span></a><?php
							} else {
								?><a class="page-link" href="#!" aria-label="Siguiente" onClick="<?php echo $this->ListadoMgr; ?>.Get({name:'pag',value:<?php echo $value['p'];?>});return false;"><span aria-hidden="true">&raquo;</span><span class="sr-only">Siguiente</span></a><?php
							}
						} else {
							?><a class="page-link" href="#!" title="Ir a página <?php echo $value['p']; ?>" onClick="<?php echo $this->ListadoMgr; ?>.Get({name:'pag',value:<?php echo $value['p'];?>});return false;"><?php echo $value['p']; ?></a><?php
						}
					}
				}
				?></li><?php
			}
		?></ul></nav><?php
	}  // MostrarPaginacion
/*
	Esto resalta una cadena encerrándo la parte coincidente con el tag indicado.
*/
/*
	Resalta una parte de una cadena.
*/
	public function Replace($str, $b = null) {
		if (is_null($b)) { $b = @$this->buscar; }
		if (!empty($b)) {
			$b = mb_strtolower($b);
			$str = mb_ereg_replace('('.$b.')','[tag]\\1[/tag]',$str,'i');
			if (!isset($this->ReplaceTag)) {
				$str = str_replace(array('[tag]','[/tag]'), array('<b>','</b>'),$str);
			} else {
				$str = str_replace(array('[tag]','[/tag]'), $this->ReplaceTag,$str);
			}
		}
		return $str;
	} // Replace

/*
	Devuelve la lista de campos para ser usuados en la cláusula ORDER BY.
	$ordenes es el array que tiene el nombre de los campos que deben incluirse en la cláusula ORDER BY. Tiene en cuenta que cada campo puede estar en una tabla diferente.
*/
	public function MakeOrderBy($ordenes) {
		$result = '';
		foreach ($ordenes as $campo => $orden) {
			$p = explode('>',$campo);
			if (count($p) > 1) {
				$result .= SQLQuote($p[0]).".".SQLQuote($p[1])." ".$orden.",";
			} else {
				$result .= SQLQuote($campo)." ".$orden.",";
			}
		}
		$result[strlen($result)-1] = " ";
		return $result;
	}
} // class
?>