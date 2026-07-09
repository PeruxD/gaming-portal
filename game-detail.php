<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

$game_id = intval($_GET['id'] ?? 0);

$query = "SELECT g.*, c.name as category_name FROM games g 
          LEFT JOIN categories c ON g.category_id = c.id 
          WHERE g.id = $game_id";
$result = mysqli_query($conn, $query);
$game = mysqli_fetch_assoc($result);

if (!$game) {
    echo "<div class='container'><h2>Juego no encontrado</h2><a href='games.php' class='btn btn-primary'>Volver</a></div>";
    include 'includes/footer.php';
    exit();
}

// Get reviews
$reviews_query = "SELECT r.*, u.username FROM reviews r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.game_id = $game_id 
                 ORDER BY r.created_at DESC LIMIT 5";
$reviews = mysqli_query($conn, $reviews_query);

// Handle review submission
$review_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    $insert = "INSERT INTO reviews (game_id, user_id, rating, comment) 
              VALUES ($game_id, $user_id, $rating, '$comment')";
    
    if (mysqli_query($conn, $insert)) {
        $review_message = '✅ Reseña publicada exitosamente';
        // Recalculate game rating
        $avg_query = "SELECT AVG(rating) as avg FROM reviews WHERE game_id = $game_id";
        $avg_result = mysqli_query($conn, $avg_query);
        $avg = mysqli_fetch_assoc($avg_result)['avg'];
        $update = "UPDATE games SET rating = $avg WHERE id = $game_id";
        mysqli_query($conn, $update);
    } else {
        $review_message = '❌ Error al publicar reseña';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['title']); ?> - FORESTER</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .game-detail-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
            margin: 2rem 0;
        }

        .game-cover {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 1rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .game-cover img {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .game-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .game-info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .game-info-label {
            color: var(--primary-color);
            font-weight: bold;
        }

        .btn-play {
            width: 100%;
            margin-top: 1rem;
        }

        .game-details {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .detail-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .detail-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .review-item {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 3px solid var(--primary-color);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .review-author {
            color: var(--primary-color);
            font-weight: bold;
        }

        .review-rating {
            color: #FFD700;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
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

        @media (max-width: 768px) {
            .game-detail-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="games.php" class="btn btn-secondary" style="margin-bottom: 1rem; display: inline-block;">← Volver</a>

        <?php if (!empty($review_message)): ?>
            <div style="background: <?php echo strpos($review_message, '✅') !== false ? '#006B3F' : '#8B0000'; ?>; color: <?php echo strpos($review_message, '✅') !== false ? '#90EE90' : '#FFB6C1'; ?>; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $review_message; ?>
            </div>
        <?php endif; ?>

        <div class="game-detail-container">
            <!-- GAME COVER & INFO -->
            <aside class="game-cover">
                <img src="<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>">
                
                <div class="game-info">
                    <div class="game-info-item">
                        <span class="game-info-label">Calificación:</span>
                        <span>⭐ <?php echo $game['rating']; ?>/10</span>
                    </div>
                    <div class="game-info-item">
                        <span class="game-info-label">Jugadores:</span>
                        <span>👥 <?php echo number_format($game['players']); ?></span>
                    </div>
                    <div class="game-info-item">
                        <span class="game-info-label">Categoría:</span>
                        <span><?php echo htmlspecialchars($game['category_name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="game-info-item">
                        <span class="game-info-label">Desarrollador:</span>
                        <span><?php echo htmlspecialchars($game['developer'] ?? 'Desconocido'); ?></span>
                    </div>
                    <div class="game-info-item">
                        <span class="game-info-label">Lanzamiento:</span>
                        <span><?php echo $game['release_date'] ? date('d/m/Y', strtotime($game['release_date'])) : 'N/A'; ?></span>
                    </div>
                </div>

                <button class="btn btn-primary btn-play">��� JUGAR AHORA</button>
            </aside>

            <!-- GAME DETAILS -->
            <section class="game-details">
                <div class="detail-section">
                    <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                    <p style="line-height: 1.8; color: #DDD;"><?php echo htmlspecialchars($game['description']); ?></p>
                </div>

                <!-- REVIEWS SECTION -->
                <div class="detail-section">
                    <h3>📝 Reseñas</h3>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="review-form" style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 2px solid var(--border-color);">
                            <h4 style="color: var(--primary-color);">Comparte tu opinión</h4>
                            <form method="POST">
                                <div class="form-group">
                                    <label for="rating">Calificación (1-10)</label>
                                    <select id="rating" name="rating" required>
                                        <option value="">Selecciona una calificación</option>
                                        <option value="1">⭐ 1 - Muy Malo</option>
                                        <option value="2">⭐⭐ 2 - Malo</option>
                                        <option value="3">⭐⭐⭐ 3 - Regular</option>
                                        <option value="4">⭐⭐⭐⭐ 4 - Bueno</option>
                                        <option value="5">⭐⭐⭐⭐⭐ 5 - Muy Bueno</option>
                                        <option value="6">6 - Excelente</option>
                                        <option value="7">7 - Excepcional</option>
                                        <option value="8">8 - Increíble</option>
                                        <option value="9">9 - Casi Perfecto</option>
                                        <option value="10">10 - Perfecto</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="comment">Tu Reseña</label>
                                    <textarea id="comment" name="comment" placeholder="Cuéntanos qué piensas de este juego..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Publicar Reseña</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; color: #AAA;">
                            <a href="auth/login.php" class="btn btn-primary">Inicia sesión para dejar una reseña</a>
                        </p>
                    <?php endif; ?>

                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Últimas Reseñas</h4>
                        <?php
                        if (mysqli_num_rows($reviews) > 0) {
                            while ($review = mysqli_fetch_assoc($reviews)) {
                                echo "<div class='review-item'>
                                        <div class='review-header'>
                                            <span class='review-author'>" . htmlspecialchars($review['username']) . "</span>
                                            <span class='review-rating">" . str_repeat('⭐', $review['rating']) . " {$review['rating']}/10</span>
                                        </div>
                                        <p style='color: #DDD; margin-bottom: 0.5rem;'>" . htmlspecialchars($review['comment']) . "</p>
                                        <small style='color: #666;'>" . date('d/m/Y H:i', strtotime($review['created_at'])) . "</small>
                                    </div>";
                            }
                        } else {
                            echo "<p style='color: #AAA; text-align: center;'>No hay reseñas aún. ¡Sé el primero!</p>";
                        }
                        ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>