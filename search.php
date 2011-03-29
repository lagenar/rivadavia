<?php
require 'db.php';

class SearchEngine {

  function __construct() {
    DBConnectionManager::connect();
  }

  public function search($query, $search_type) {
    $query = mysql_real_escape_string($query);   
    if ($search_type == 'autor') {
      $where_clause = "Autor.nombre LIKE '%$query%'";
    } else if ($search_type == 'libro') {
      $where_clause = "Libro.nombre LIKE '%$query%'";
    } else {
      $where_clause = "Libro.nombre LIKE '%$query%' OR Autor.nombre LIKE '%$query%'";
    }

    $r = mysql_query("SELECT Libro.nombre, Autor.nombre FROM
                      (Libro JOIN Libro_Autor on Libro.id = Libro_Autor.id_libro)
                      JOIN Autor on Autor.id = Libro_Autor.id_autor WHERE
                      " . $where_clause);

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
    <link rel="stylesheet" type="text/css" href="styles.css" />
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
    <div id="logo">
      <img src="/logo.png" alt="Rivadavia"/>
      <form action="search.php" style="position : relative; left : -15px;" id="searchform">
	<p>
	  <input type="text" size="40" name="query" /><br />
	  <select name="search_type">
	    <option value="">Buscar en todos los campos</option>
	    <option value="libro">Buscar por titulo</option>
	    <option value="autor">Buscar por autor</option>
	  </select>
	  <input type="submit" value="Buscar" />
	</p>
      </form>
    </div>
    <?php
      }
    ?>

  </body>
</html>