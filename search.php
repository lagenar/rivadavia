<?php
require 'db.php';

class SearchEngine {

  function __construct() {
    DBConnectionManager::connect();
  }

  public function search($query, $search_type) {
    $query = mysql_real_escape_string($query);
    if ($search_type == 'autor') {
      $r = mysql_query("SELECT Libro.nombre, Autor.nombre FROM
                        (Autor JOIN Libro_Autor on Autor.id = Libro_Autor.id_autor)
                        JOIN Libro on Libro.id = Libro_Autor.id_libro WHERE
                        Autor.nombre LIKE '%$query%'");

    } else if ($search_type == 'libro') {
      $r = mysql_query("SELECT Libro.nombre, Autor.nombre FROM
                        (Libro JOIN Libro_Autor on Libro.id = Libro_Autor.id_libro)
                        JOIN Autor on Autor.id = Libro_Autor.id_autor WHERE
                        Libro.nombre LIKE '%$query%'");      
    } else {
      $r = mysql_query("SELECT Libro.nombre, Autor.nombre FROM
                        (Libro JOIN Libro_Autor on Libro.id = Libro_Autor.id_libro)
                        JOIN Autor on Autor.id = Libro_Autor.id_autor WHERE
                        Libro.nombre LIKE '%$query%' OR Autor.nombre LIKE '%$query%'");
    }

    $libros = Array();
    while (($row = mysql_fetch_row($r)) !== FALSE) {
      $libros[] = $row;
    }

    return $libros;
  }
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Busqueda de libros</title>
  </head>
  <body>
    <?php
      if (isset($_GET['query'])) {
	$search_eng = new SearchEngine();
	$libros = $search_eng->search($_GET['query'], $_GET['search_type']);
	foreach ($libros as $libro) {
	  echo "<p>{$libro[0]}, {$libro[1]}</p>";
	}
      } else {
    ?>
    <form action="search.php">
      <p>
      <input type="text" name="query" />
      <select name="search_type">
	<option value="">Buscar en todos los campos</option>
	<option value="libro">Buscar por titulo</option>
	<option value="autor">Buscar por autor</option>
      </select>
      <input type="submit" value="Buscar" />
      </p>
    </form>
    <?php
      }
    ?>

  </body>
</html>