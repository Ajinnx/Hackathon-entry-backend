<?php
require "../private/autoload.php";
$Error = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];

    // Validate the email
    if (!preg_match("/^[\w\-]+@[\w\-]+\.[\w\-]+$/", $email)) {
        $Error = "Por favor ingresar un correo válido";
    } else {
        $url_address = get_random_string(60);
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $date = date("Y-m-d H:i:s");

        // Validate the username
        if (!preg_match("/^[\w\-@.!#%$]+$/", $username)) {
            $Error = "Por favor ingresar un nombre de usuario válido";
        } else {
            // Prepare and execute the query using a prepared statement
            $query = "INSERT INTO users (url_address, username, password, email, date) VALUES (?, ?, ?, ?, ?)";
            $statement = $connection->prepare($query);
            $statement->bind_param("sssss", $url_address, $username, $hashedPassword, $email, $date); // Store the hashed password
            if ($statement->execute()) {
                $successMessage = "Usuario registrado con éxito.";
            } else {
                echo "Error: " . $statement->error;
            }

            $statement->close();
        }
    }
}

$connection->close();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <form method="post">
            <div><?php
                if (isset($Error) && $Error != "") 
                {
                    echo $Error;
                }
            ?></div>
            <h1 class="text-center mt-5">Signup</h1>
            <div class="mb-3">
                <label for="username" class="form-label">Usuario:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrarse</button>
            <div class="text-center mt-3">
                <p>Ya tienes cuenta? <a href="login.php">Inicia Sesión</a></p>
            </div>
        </form>

		<!-- POP UP for successful registration -->
		<?php if ($successMessage != ""): ?>
		    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
		        <div class="modal-dialog modal-dialog-centered">
		            <div class="modal-content">
		                <div class="modal-header">
		                    <h5 class="modal-title" id="successModalLabel">AVISO!</h5>
		                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		                </div>
		                <div class="modal-body">
		                    <?php echo $successMessage; ?>
		                </div>
		            </div>
		        </div>
		    </div>
		<?php endif; ?>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

		<?php if ($successMessage != ""): ?>
		    <script>
		        var successModal = new bootstrap.Modal(document.getElementById('successModal'), {
		            backdrop: 'static',
		            keyboard: false
		        });
		        successModal.show();
		    </script>
		<?php endif; ?>

		<!-- ------------------------------------ -->
</body>
</html>