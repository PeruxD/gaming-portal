<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user = mysqli_fetch_assoc(mysqli_query($conn, $user_query));

// Get player info
$player_query = "SELECT p.*, (SELECT COUNT(*) FROM players WHERE points > p.points) + 1 as rank FROM players p WHERE p.user_id = $user_id";
$player = mysqli_fetch_assoc(mysqli_query($conn, $player_query));

// Get achievements
$achievements_query = "SELECT * FROM achievements WHERE user_id = $user_id";
$achievements = mysqli_query($conn, $achievements_query);

// Handle profile update
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    
    $update = "UPDATE users SET bio = '$bio', country = '$country' WHERE id = $user_id";
    if (mysqli_query($conn, $update)) {
        $message = '✅ Perfil actualizado exitosamente';
        $user['bio'] = $bio;
        $user['country'] = $country;
    } else {
        $message = '❌ Error al actualizar';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - FORESTER</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            margin: 2rem 0;
        }

        .profile-sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            height: fit-content;
            position: sticky;
            top: 80px;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, #FFC700 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            margin: 0 auto 1rem;
            border: 3px solid var(--primary-color);
        }

        .profile-username {
            text-align: center;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .stat-value {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stat-label {
            color: #AAA;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .rank-badge {
            display: block;
            text-align: center;
            padding: 0.75rem;
            margin-top: 1rem;
            border-radius: 5px;
            font-weight: bold;
        }

        .profile-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .profile-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .profile-section h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: var(--secondary-color);
            color: var(--text-color);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
        }

        .achievement-item {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .achievement-item:hover {
            transform: scale(1.05);
            border-color: var(--primary-color);
        }

        .achievement-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .achievement-name {
            font-size: 0.85rem;
            color: var(--primary-color);
        }

        .btn-save {
            background: var(--primary-color);
            color: var(--secondary-color);
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background: #FFC700;
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }

            .profile-sidebar {
                position: relative;
                top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-secondary" style="margin-bottom: 1rem; display: inline-block;">← Volver</a>

        <?php if (!empty($message)): ?>
            <div style="background: <?php echo strpos($message, '✅') !== false ? '#006B3F' : '#8B0000'; ?>; color: <?php echo strpos($message, '✅') !== false ? '#90EE90' : '#FFB6C1'; ?>; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <!-- PROFILE SIDEBAR -->
            <aside class="profile-sidebar">
                <div class="profile-avatar">🎮</div>
                <h2 class="profile-username"><?php echo htmlspecialchars($user['username']); ?></h2>
                
                <?php if ($player): ?>
                    <div class="profile-stats">
                        <div class="stat">
                            <div class="stat-value">#<?php echo $player['rank']; ?></div>
                            <div class="stat-label">Posición</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value"><?php echo $player['level']; ?></div>
                            <div class="stat-label">Nivel</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value"><?php echo number_format($player['points']); ?></div>
                            <div class="stat-label">Puntos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value"><?php echo $player['games_played']; ?></div>
                            <div class="stat-label">Partidas</div>
                        </div>
                    </div>
                    <span class="rank-badge" style="background: #FFD700; color: #000;"><?php echo $player['rank']; ?></span>
                <?php endif; ?>
            </aside>

            <!-- PROFILE CONTENT -->
            <div class="profile-content">
                <!-- EDIT PROFILE -->
                <section class="profile-section">
                    <h3>✏️ Editar Perfil</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Usuario (No editable)</label>
                            <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="country">País</label>
                            <input type="text" id="country" name="country" placeholder="Tu país" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="bio">Biografía</label>
                            <textarea id="bio" name="bio" placeholder="Cuéntanos sobre ti..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn-save">💾 Guardar Cambios</button>
                    </form>
                </section>

                <!-- ACHIEVEMENTS -->
                <section class="profile-section">
                    <h3>🏅 Logros Desbloqueados</h3>
                    <?php if (mysqli_num_rows($achievements) > 0): ?>
                        <div class="achievements-grid">
                            <?php
                            while ($achievement = mysqli_fetch_assoc($achievements)) {
                                echo "<div class='achievement-item' title='" . htmlspecialchars($achievement['description']) . "'>
                                        <div class='achievement-icon">🏆</div>
                                        <div class='achievement-name'>" . htmlspecialchars($achievement['title']) . "</div>
                                    </div>";
                            }
                            ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #AAA; text-align: center;">No tienes logros desbloqueados aún. ¡Sigue jugando!</p>
                    <?php endif; ?>
                </section>

                <!-- SECURITY -->
                <section class="profile-section">
                    <h3>🔒 Seguridad</h3>
                    <div class="form-group">
                        <a href="change-password.php" class="btn btn-primary" style="text-align: center; text-decoration: none;">Cambiar Contraseña</a>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>