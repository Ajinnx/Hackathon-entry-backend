<?php
session_start();

require "../private/autoload.php";
$Error = "";

// Generate a CSRF token and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validate the CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email and password
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
        $Error = "Por favor ingresar un correo válido";
    } else {
        // Prepare and execute the query using a prepared statement
        $query = "SELECT * FROM users WHERE email = ? AND password = ?";
        $statement = $connection->prepare($query);
        $statement->bind_param("ss", $email, $password);
        $statement->execute();
        $result = $statement->get_result();

        if ($result->num_rows == 1) {
            // IF login successful
            $row = $result->fetch_assoc();

            // Store the values in session variables
            $_SESSION['url_address'] = $row['url_address'];
            $_SESSION['username'] = $row['username'];

            header("Location: index.php");
            exit();
        } 
        else 
        {
            
            $Error = "Correo o contraseña incorrectos.";
        }

        $statement->close();
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body>
	<div class="container">
		<form method="post">
			<div><?php
				if (isset($Error) && $Error != "") 
				{
					echo '<div class="alert alert-danger">' . $Error . '</div>';
				}
			?></div>
			<h1 class="text-center mt-5">Login</h1>
			<div class="mb-3">
				<label for="email" class="form-label">Correo:</label>
				<input type="email" id="email" name="email" class="form-control" required>
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Contraseña:</label>
				<input type="password" id="password" name="password" class="form-control" required>
			</div>
			<!-- Add the CSRF token to the form -->
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
			<button type="submit" class="btn btn-primary">Iniciar Sesion</button>
			<div class="text-center mt-3">
                <p>No tienes cuenta? <a href="signup.php">Registrarse</a></p>
            </div>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>
</html>
