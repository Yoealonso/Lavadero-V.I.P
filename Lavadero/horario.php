<?php 
include_once("C:/xampp/htdocs/Lavadero/Conexion/conexion.php");


if (isset($_POST["enviar"])) {
	$fecha = $_POST["date"];
    $hora = $_POST["datetime"];
  
	
	$cadena = "INSERT INTO turno(fechaReserva, horaReserva) VALUES('$fecha', '$hora')";
  
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


<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Lavadero</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='style.css'>
    <script src='main.js'></script>
</head>
<body>

<header>
    <h2>VIP CAR WASH</h2>
  </header>

  <nav>
  
  </nav>

   <form method="POST">
    <h3>Complete sus datos para su proximo turno!!!</h3>
    <label>Dia:</label>		 	
    <input type="date" name="date" required><br><br>
    <label>Hora:</label>		 	
    <input type="datetime" name="datetime" required><br><br>
    
    <input type="submit" name="enviar" value="Enviar" class="enviar">
    

    <footer>
        
    </footer>
</body>
