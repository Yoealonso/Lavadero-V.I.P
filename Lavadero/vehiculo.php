<?php 
include_once("C:/xampp/htdocs/Lavadero/Conexion/conexion.php");


if (isset($_POST["enviar"])) {
	$marca = $_POST["brand"];
    $tipo = $_POST["type"];
    $modelo = $_POST["model"];
    $anio = $_POST["releaseDate"];
    $patente = $_POST["patent"];
    $detalles = $_POST["details"];
	
    
	$cadena = "INSERT INTO vehiculo(tipo, marca, modelo, anio, patente, detalles) VALUES('$marca', '$tipo', '$modelo', '$anio', '$patente', '$detalles')";
  
	$consulta = mysqli_query($link,$cadena);
	

    if ($consulta)
	{	

		echo '<script type="text/javascript">
		 alert ("Bienvenido!!!");
		</script>';
	
        }else{

		echo "<script> alert('Ocurrió un error, intente nuevamente')></script>";
	}
}
 ?>
      <h3>Datos del vehiculo</h3>
    <label>Marca:</label>		 	
    <select name="brand">
    <option></option>
    <option>Ford</option>
    <option>Chevrolet</option>
    <option>Toyota</option>
    <option>Fiat</option>
    <option>Peugeot</option>
    <option>Susuki</option>
    <option>Volkswagen</option>
    <option>Dodge</option>
    <option>Citroën</option>
    <option>Izuzu</option>
    <option>Mitsubishi</option>
    <option>Renault</option>
    <option>Jeep</option>
    <option>Nissan</option>
    <option>Honda</option>
    <option>Chrysler</option>     
    <option>Daihatsu</option>
    <option>Madza</option>
    <option>Mercedes Benz</option>
    <option>BMW</option>
    <option>Audi</option>
    <option>Subaru</option>
</select><br><br>
    <label>Tipo de vehiculo:</label>		 	
    <select name="type">
    <option></option>
    <option>Auto</option>
    <option>Camioneta</option>
    <option>S.U.V</option>
</select><br><br>
    <label>Modelo:</label>		 	
    <input type="text" name="model" required><br><br>
    <label>Año:</label>		 	
    <input type="number" name="releaseDate" value="2010" min="1950" max="2030" step="1" required><br><br>
    <label>Patente:</label>		 	
    <input type="text" name="patent" required><br><br>    
      <label>Detalles del vehiculo:</label>		 	
    <input type="text" name="details" required><br><br>

    <!-- <h3>Fecha del turno</h3>

    <label>Ingrese la fecha y hora que desea</label><br>
    <input type="date" name="date">
    <input type="time" name="date">
    <br><br> -->
    <input type="submit" name="enviar" value="Enviar" class="enviar">