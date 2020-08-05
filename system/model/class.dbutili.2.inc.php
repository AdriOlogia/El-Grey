<?php
/*
	Clase para manejo de base de datos MySQL.
	Version: 2.0
	Created: long time ago.
	Author: DriverOp. http://driverop.com.ar/
	Licence: LGPL 3. http://www.gnu.org/licenses/lgpl.html
	Last Modif: Added option 'contar' on method Query().
	Last Modif: Agregue un par�metro a ShowLastError y la funci�n controla el valor de DEVELOPE.
	Last Modif: Agregue el m�todo GetArray.
	Last Modif: Arreglado bug en RealEscape cuando $str = NULL devolv�a string vac�o en vez de NULL
	Modified: 2019-03-09
	Desc: Arreglado bug en m�todo Last(). mysql_num_rows deb�a ser mysqli_numrows.
	Modified: 2019-09-18
	Desc: Agregada funci�n SetONLY_FULL_GROUP_BY_off() para desactivar la directiva ONLY_FULL_GROUP_BY en los servidores MySQL 5.7
	Modified: 2020-02-18
	Reescritura de la conexi�n a la base de datos para contemplar que se pueda conectar por SSL a MySQL.
	Modified: 2020-03-16
	Agregado m�todo RealEscapeArray para satinizar un array.
*/

if (!isset($db_link)) {
	$db_link = NULL;
}

class cDb
{
	var $link = NULL;
	var $port = NULL;
	var $errmsg = "";
	var $error = false;
	var $errno = 0;
	var $numrows = 0;
	var $affectedrows = 0;
	var $result = NULL;
	var $last_id = 0;
	var $lastsql = "";
	var $persistent = false;
	var $cantidad = 0;
	private $opened = false;
	private $ssl = false;
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpass;
	private $path_to_certs = null;
	private $key_pem = null;
	private $cert_pem = null;
	private $ca_pem = null;

	function __construct($dbhost = null, $dbname = null, $dbuser = null, $dbpass = null)
	{
		if (!empty($dbhost) and !empty($dbname) and !empty($dbuser) and !empty($dbpass)) {
			$this->Connect($dbhost, $dbname, $dbuser, $dbpass);
		}
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
	}

	/*
	Establecer las opciones de conexi�n sobre SSL
	$path: ruta hacia los archivos .pem
	$key, $cert, $ca: nombre de los archivos .pem
*/
	function SetSSL($path, $key, $cert, $ca)
	{
		$this->path_to_certs = $path;
		$this->key_pem = $key;
		$this->cert_pem = $cert;
		$this->ca_pem = $ca;
		if ((!empty($this->key_pem)) and !$this->ExisteArchivo($this->path_to_certs . $this->key_pem)) {
			throw new Exception($this->path_to_certs . $this->key_pem . ' no encontrado.');
		}
		if ((!empty($this->cert_pem)) and !$this->ExisteArchivo($this->path_to_certs . $this->cert_pem)) {
			throw new Exception($this->path_to_certs . $this->cert_pem . ' no encontrado.');
		}
		if ((!empty($this->ca_pem)) and !$this->ExisteArchivo($this->path_to_certs . $this->ca_pem)) {
			throw new Exception($this->path_to_certs . $this->ca_pem . ' no encontrado.');
		}
		$this->ssl = true;
	}

	function CheckError()
	{
		$this->errno = mysqli_errno($this->link);
		$this->error = $this->errno != 0;
		$this->errmsg = $this->errno . ": " . mysqli_error($this->link);
		return $this->error;
	}

	function Connect($dbhost, $dbname, $dbuser, $dbpass)
	{
		$this->error = false;
		if (($this->opened != true) or ($this->persistent != true)) {
			$this->GetConnect($dbhost, $dbuser, $dbpass, $dbname);
		}
		if (!$this->error) {
			$this->SetUTF8();
			$this->SetONLY_FULL_GROUP_BY_off();
		}
		return $this->error;
	}

	private function GetConnect($dbhost, $dbuser, $dbpass, $dbname)
	{
		global $db_link;
		if (($db_link == NULL) or ($this->dbhost != $dbhost) or ($this->dbname != $dbname)) {
			$this->port = (is_null($this->port)) ? 3306 : $this->port;
			$this->link = mysqli_init();
			if ($this->ssl) {
				mysqli_ssl_set(
					$this->link,
					$this->path_to_certs . $this->key_pem,
					$this->path_to_certs . $this->cert_pem,
					$this->path_to_certs . $this->ca_pem,
					NULL,
					NULL
				);
				$result = mysqli_real_connect($this->link, $dbhost, $dbuser, $dbpass, $dbname, $this->port, null, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
			} else {
				$result = mysqli_real_connect($this->link, $dbhost, $dbuser, $dbpass, $dbname, $this->port);
			}
			if (!$result or (mysqli_connect_errno() > 0)) {
				$this->error = true;
				$this->errno = mysqli_connect_errno();
				$this->errmsg = $this->errno . ": " . mysqli_connect_error();
			} else {
				$this->opened = true;
				$db_link = $this->link;
			}
		} else {
			$this->link = $db_link;
			$this->opened = true;
		}
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
	}

	function Disconnect()
	{
		global $db_link;
		if (!$this->persistent) {
			if ($this->link !== NULL) {
				mysqli_close($this->link);
				$this->link = NULL;
				$this->opened = false;
				$db_link = $this->link;
			}
		}
	}

	function IsConnected()
	{
		if (!is_bool($this->link) and !($this->link == NULL)) {
			return true;
		} else {
			return false;
		}
	}

	function GetLink()
	{
		return $this->link;
	}

	function Query($sql, $contar = false)
	{
		if (($contar) and (strtoupper(substr(trim($sql), 0, 6)) == 'SELECT')) {
			$sql = 'SELECT SQL_CALC_FOUND_ROWS' . substr($sql, 6);
			$sql_found = 'SELECT FOUND_ROWS() AS `cantidad`';
		}
		$this->numrows = 0;
		$this->lastsql = $sql;

		$this->result = mysqli_query($this->link, $sql);

		if (!$this->CheckError()) {
			if (!is_bool($this->result)) {
				$this->numrows = mysqli_num_rows($this->result);
			} else {
				$this->affectedrows = mysqli_affected_rows($this->link);
			}

			// Si quiero contar la cantidad de registros sin el limit
			if (isset($sql_found)) {
				$aux = mysqli_query($this->link, $sql_found);
				$aux2 = mysqli_fetch_assoc($aux);
				$this->cantidad = $aux2['cantidad'];
			}
		} // si no hubo error
		return $this->result;
	}

	function RawQuery($sql)
	{
		$this->result = mysqli_query($this->link, $sql);
		return $this->result;
	}

	function Update($tabla, $lista, $where = "")
	{
		$this->affectedrows = -1;
		if (!is_array($lista)) {
			$this->error = true;
			$this->errno = -1;
			$this->errmsg = "Segundo par�metro no es array ('campo'=>'valor')";
			return false;
		} else {
			$sql = "UPDATE `" . $tabla . "` SET";
			foreach ($lista as $key => $value) {
				if ($value === null) {
					$sql .= " `" . $key . "` = NULL,";
				} else {
					$sql .= " `" . $key . "` = '" . $value . "',";
				}
			}
			$sql = substr($sql, 0, -1); // Quita la �ltima coma.
			$where = trim($where);
			if (!empty($where)) {
				$sql .= " WHERE " . $where;
			}
			$sql .= ";";
			$this->lastsql = $sql;
			$this->result = mysqli_query($this->link, $sql);
			if (!$this->CheckError()) {
				$this->affectedrows = mysqli_affected_rows($this->link);
				return true;
			} else {
				return false;
			}
		}
	}

	function Insert($tabla, $lista)
	{
		$this->affectedrows = -1;
		if (!is_array($lista)) {
			$this->error = true;
			$this->errno = -1;
			$this->errmsg = "Segundo par�metro no es array ('campo'=>'valor')";
			return false;
		} else {
			$sql = "INSERT INTO `" . $tabla . "` (`" . implode("`, `", array_keys($lista)) . "`) VALUES (";
			reset($lista);
			foreach ($lista as $key => $value) {
				if ($value === null) {
					$sql .= "NULL,";
				} else {
					$sql .= "'" . $value . "',";
				}
			}
			$sql = substr($sql, 0, -1); // Quita la �ltima coma.
			$sql .= ");";

			$this->lastsql = $sql;
			$this->result = mysqli_query($this->link, $sql);
			if (!$this->CheckError()) {
				$this->last_id = mysqli_insert_id($this->link);
				$this->affectedrows = mysqli_affected_rows($this->link);
				return true;
			} else {
				return false;
			}
		}
	}

	function MultiInsert($tabla, $campos, $valores)
	{
		$this->affectedrows = -1;
		if ((!is_array($campos)) or (!is_array($valores))) {
			$this->error = true;
			$this->errno = -1;
			$this->errmsg = "Par�metros 'campos' y 'valores' deben ser array.";
			return false;
		} else {
			if (count($valores) > 0) {
				$sql = "INSERT INTO `" . $tabla . "` (`" . implode("`, `", $campos) . "`) VALUES ";

				foreach ($valores as $key => $value) {
					$sql .= "('" . implode("', '", $value) . "'),";
				}
				$sql = substr($sql, 0, -1); // Quita la �ltima coma.

				$this->lastsql = $sql;
				$this->result = mysqli_query($this->link, $sql);
				if (!$this->CheckError()) {
					$this->last_id = mysqli_insert_id($this->link);
					$this->affectedrows = mysqli_affected_rows($this->link);
					return true;
				} else {
					return false;
				}
			} else {
				$this->error = true;
				$this->errno = -2;
				$this->errmsg = "Valores est� vac�o";
				return false;
			}
		}
	} // MultiInsert

	function Delete($tabla, $where)
	{
		$sql = "DELETE FROM `" . $tabla . "` WHERE " . $where . ";";
		$this->lastsql = $sql;
		$this->result = mysqli_query($this->link, $sql);
		if (!$this->CheckError()) {
			$this->affectedrows = mysqli_affected_rows($this->link);
			return true;
		} else {
			return false;
		}
	}

	function First($res = NULL)
	{
		if ($res == NULL) {
			$res = $this->result;
		}
		if (mysqli_num_rows($res) > 0) {
			mysqli_data_seek($res, 0);
			return mysqli_fetch_assoc($res);
		} else {
			return false;
		}
	}

	function Next($res = NULL)
	{
		if ($res == NULL) {
			$res = $this->result;
		}
		if (mysqli_num_rows($res) > 0) {
			return mysqli_fetch_assoc($res);
		} else {
			return false;
		}
	}

	function Last($res = NULL)
	{
		if ($res == NULL) {
			$res = $this->result;
		}
		if (mysqli_num_rows($res) > 0) {
			mysqli_data_seek($res, mysqli_num_rows($res) - 1);
			return mysqli_fetch_assoc($res);
		} else {
			return false;
		}
	}

	function Seek($num, $res = NULL)
	{
		$result = false;
		if ($res == NULL) {
			$res = $this->result;
		}
		if (is_int($num)) {
			$num = (int) $num;
			if ((mysqli_num_rows($res) > 0) and ($num < mysqli_num_rows($res))) {
				mysqli_data_seek($res, $num);
				$result = mysqli_fetch_assoc($res);
			}
		}
		return $result;
	}

	function SeekBy($tabla, $campo, $valor, $altorden = null)
	{
		$result = false;
		if (!empty($tabla) and !empty($campo)) {
			$sql = "DESCRIBE `" . $tabla . "` `" . $campo . "`";
			$this->lastsql = $sql;
			$this->result = mysqli_query($this->link, $sql);
			if (!$this->CheckError()) {
				$result = mysqli_fetch_assoc($this->result);
				if ($result === FALSE) {
					$this->error = true;
					$this->errno = 1054;
					$this->errmsg = "Unknown column '" . $campo . "' in table '" . $tabla . "'";
				} else {
					$sql = "SELECT * FROM `" . $tabla . "` WHERE ";
					if ((stripos($result['Type'], "varchar(") == 0) or (stripos($result['Type'], "text") == 0)) {
						$sql .= "LOWER(`" . $campo . "`) LIKE LOWER('" . $valor . "')";
					} else {
						$sql .= "`" . $campo . "` = '" . $valor . "' ";
					}
					if (!empty($altorden)) {
						$sql .= " ORDER BY `" . $altorden . "`";
					}
					$this->Query($sql);
					$this->lastsql = $sql;
					if (!$this->error and $this->numrows > 0) {
						$result = $this->First();
					} else {
						$result = false;
					}
				}
			}
		}
		return $result;
	}

	function GetNumRows($res)
	{
		return mysqli_num_rows($res);
	}

	function ShowLastError($forzar = false)
	{
		if (($forzar == true) or (DEVELOPE)) {
			if ($this->error) {
				echo $this->errno . ": " . $this->errmsg . "<br />";
				echo $this->lastsql . "<br>";
			}
		}
	} // function ShowLastError

	function SetUTF8($value = true)
	{
		$sql = ($value) ? "SET NAMES 'utf8'" : "SET NAMES 'latin1'";
		$this->result = mysqli_query($this->link, $sql);
		return $this->CheckError();
	}

	function SetONLY_FULL_GROUP_BY_off()
	{
		$sql = "SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));";
		$this->result = mysqli_query($this->link, $sql);
		return $this->CheckError();
	}

	function RealEscape($str)
	{
		if ($str !== NULL) {
			return mysqli_real_escape_string($this->link, $str);
		} else {
			return NULL;
		}
	}

	function InitDB()
	{
		if ($this->IsConnected() == false) {
			$this->Connect(DBHOST, DBNAME, DBUSER, DBPASS);
		}
	}

	function FinalizeDB()
	{
		$this->Disconnect();
	}

	/*
	Esta funci�n devuelve un array en base a la sql pasada como par�metro.
	Como tercer par�metro se puede pasar un string indicando el �nico campo que se desea que sea devuelto, o un array para m�s de un campo.
	El tercer campo se recomienda para obtener array('campo'=>'valor'), en vez de array('resultado 1'=>array('campo'=>'valor')), en una consulta que devuleve una sola columna.
*/
	public function GetArray($sql, $contar = false, $fields = null)
	{
		$result = array();
		$this->Query($sql, $contar);
		if (!$this->error) {
			if ($this->numrows > 0) {
				$aux = array();
				$i = 0;
				while ($fila = $this->Next()) {
					if ($fields != null) {
						if (is_array($fields)) {
							foreach ($fields as $llave => $campo) {
								$aux[$i][$campo] = $fila[$campo];
							}
						} else {
							$aux[$i] = $fila[$fields];
						}
					} else {
						$aux[$i] = $fila;
					}
					$i++;
				}
				$result = $aux;
			}
		}
		return $result;
	} // function GetArray
	/*

*/
	public function RealEscapeArray($arr)
	{
		foreach ($arr as $key => $value) {
			$arr[$key] = $this->RealEscape($value);
		}
		return $arr;
	}

	private function ExisteArchivo($file)
	{
		return (file_exists($file) and is_file($file) and is_readable($file));
	}
}
