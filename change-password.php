<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Get current password
    $user_query = "SELECT password FROM users WHERE id = $user_id";
    $user = mysqli_fetch_assoc(mysqli_query($conn, $user_query));
    
    if (password_verify($current_password, $user['password'])) {
        if (strlen($new_password) < 6) {
            $message = 'La contraseña debe tener al menos 6 caracteres';
            $message_type = 'error';
        } elseif ($new_password !== $confirm_password) {
            $message = 'Las contraseñas no coinciden';
            $message_type = 'error';
        } else {
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $update = "UPDATE users SET password = '$hashed' WHERE id = $user_id";
            if (mysqli_query($conn, $update)) {
                $message = '✅ Contraseña cambiada exitosamente';
                $message_type = 'success';
            }
        }
    } else {
        $message = 'Contraseña actual incorrecta';
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - FORESTER</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .change-password-container {
            max-width: 500px;
            margin: 3rem auto;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }

        .change-password-container h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: var(--secondary-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            color: var(--secondary-color);
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #FFC700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
            transform: scale(1.02);
        }

        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }

        .message.success {
            background: #006B3F;
            color: #90EE90;
            border-color: #00FF41;
        }

        .message.error {
            background: #8B0000;
            color: #FFB6C1;
            border-color: #FF6B6B;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="profile.php" class="btn btn-secondary" style="margin-bottom: 1rem; display: inline-block;">← Volver</a>

        <div class="change-password-container">
            <h2>🔒 Cambiar Contraseña</h2>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Contraseña Actual</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">Nueva Contraseña</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Nueva Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn-submit">Cambiar Contraseña</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>