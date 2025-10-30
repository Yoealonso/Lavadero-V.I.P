<?php 
include_once("C:/xampp/htdocs/Lavadero/Conexion/conexion.php");


if (isset($_POST["enviar"])) {
	$nombre = $_POST["name"];
    $apellido = $_POST["lastName"];
    $telefono = $_POST["phone"];
    $email = $_POST["email"];
    $direccion = $_POST["location"];
	
    
	$cadena = "INSERT INTO cliente(nombre, apellido, telefono, email, direccion) VALUES('$nombre', '$apellido', '$telefono', '$email', '$direccion')";
  
	$consulta = mysqli_query($link,$cadena);
	

    if ($consulta)
	{	

		echo '<script type="text/javascript">
		 alert ("Bienvenido!!!");
		 window.location.href="../login.php";
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
    <label>Nombre:</label>		 	
    <input type="text" name="name" required><br><br>
    <label>Apellido:</label>		 	
    <input type="text" name="lastName" required><br><br>
    <label>Telefono de contacto:</label>		 	
    <input type="phone" name="phone" required><br><br>
    <label>Email:</label>		 	
    <input type="text" name="email" required><br><br>
    <label>Direccion:</label>		 	
    <input type="text" name="location" required><br><br>
    <br>
    <!-- <h3>Seleccione el servicio:</h3> 
    <select name="server">
        <option></option>
        <option>Servicio_uno</option>
        <option>Servicio_dos</option>
    </select><br><br>-->
    <input type="submit" name="enviar" value="Enviar" class="enviar">
    

    <footer>
        
    </footer>
</body>

