<?php
require 'db.php';

class DBPopulator {
  
  function __construct() {
    DBConnectionManager::connect();
  }
  
  private function add_autor_libro($autor, $id_libro) {
    $r = mysql_query(sprintf('SELECT id FROM Autor WHERE
                              nombre="%s"', mysql_real_escape_string($autor)));
    $id_autor = 0;
    if (!($aut = mysql_fetch_row($r))) {
      mysql_query(sprintf('INSERT INTO Autor (nombre)
                           VALUES ("%s")', mysql_real_escape_string($autor)));
      $r = mysql_query('SELECT LAST_INSERT_ID() FROM Autor');
      $id_autor = mysql_fetch_row($r);
      $id_autor = $id_autor[0];
    } else {
      $id_autor = $aut[0];
    }
    
    mysql_query("INSERT INTO Libro_Autor (id_libro, id_autor)
                 VALUES ($id_libro, $id_autor)");
  }

  private function add_libro($nombre, $autores) {
    $result = mysql_query(sprintf('SELECT id FROM Libro WHERE nombre="%s"',
				  mysql_real_escape_string($nombre)));
    while (($row = mysql_fetch_row($result)) !== FALSE) {
      $id_libro = $row[0];
      $r = mysql_query(sprintf('SELECT Autor.nombre FROM Autor INNER JOIN Libro_Autor
                                ON Autor.id = Libro_Autor.id_autor WHERE
                                Libro_Autor.id_libro=%s', $id_libro));
      if (mysql_num_rows($r) == count($autores)) {
	$found = TRUE;
	while ($found && ($aut = mysql_fetch_row($r)) !== FALSE) {
	  if (!in_array($aut[0], $autores))
	    $found = FALSE;
	}
	if ($found)
	  return $id_libro;
      }
    }
    
    // el libro no se encontro en la db
    mysql_query(sprintf('INSERT INTO Libro (nombre) 
                         VALUES ("%s")', mysql_real_escape_string($nombre)));
    $r = mysql_query('SELECT LAST_INSERT_ID() FROM Libro');
    $id_libro = mysql_fetch_row($r);
    $id_libro = $id_libro[0];
    foreach ($autores as $autor) {
      $this->add_autor_libro($autor, $id_libro);
    }

    return $id_libro;
  }
  
  public function add_tomo($codigo, $nombre, $autores, $editorial, $anio_edic) {
    $id_libro = $this->add_libro($nombre, $autores);
    mysql_query(sprintf('INSERT INTO Tomo (codigo, editorial, anio_edicion, id_libro)
                         VALUES ("%s", "%s", %s, %s)', 
			mysql_real_escape_string($codigo), 
			mysql_real_escape_string($editorial), 
			mysql_real_escape_string($anio_edic), $id_libro));
  }
}

function parse_autores($autores) {
  $auts = explode(" - ", $autores);
  foreach ($auts as $key => $value) {
    $auts[$key] = trim($value);
  }
  return $auts;
}

$db_populator = new DBPopulator();
if (($handle = fopen("libros.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    $codigo = ltrim($data[0], "0");
    $nombre = trim($data[1]);
    $autores = parse_autores($data[2]);
    $editorial = trim($data[3]);
    $anio_edic = (int)$data[4];
    echo "$codigo, $nombre, $editorial, $anio_edic \n";
    $db_populator->add_tomo($codigo, $nombre, $autores, $editorial, $anio_edic);
  }
  fclose($handle);
}

?>
