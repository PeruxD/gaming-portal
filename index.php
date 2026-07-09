<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORESTER - Gaming Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <div class="container">
        <!-- HERO SECTION -->
        <section class="hero">
            <div class="hero-content">
                <h1 class="title">FORESTER</h1>
                <p class="subtitle">Explora el mundo del gaming</p>
                <img src="images/hero-banner.jpg" alt="Hero Banner" class="hero-image">
                <div class="hero-buttons">
                    <button class="btn btn-primary">JUGAR AHORA</button>
                    <button class="btn btn-secondary">VER TRAILER</button>
                </div>
            </div>
        </section>

        <!-- FEATURED GAMES -->
        <section class="featured-games">
            <h2>Juegos Destacados</h2>
            <div class="games-grid">
                <?php
                $query = "SELECT * FROM games WHERE featured = 1 LIMIT 6";
                $result = mysqli_query($conn, $query);
                while($game = mysqli_fetch_assoc($result)) {
                    echo "<div class='game-card'>
                            <img src='" . htmlspecialchars($game['image']) . "' alt='" . htmlspecialchars($game['title']) . "'>
                            <h3>" . htmlspecialchars($game['title']) . "</h3>
                            <p>" . htmlspecialchars($game['description']) . "</p>
                            <div class='game-meta'>
                                <span class='rating'>⭐ " . $game['rating'] . "/10</span>
                                <span class='players">👥 " . $game['players'] . " jugadores</span>
                            </div>
                            <a href='game.php?id=" . $game['id'] . "' class='btn btn-small'>Ver Juego</a>
                        </div>";
                }
                ?>
            </div>
        </section>

        <!-- CATEGORIES -->
        <section class="categories">
            <h2>Categorías</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <img src="images/action.jpg" alt="Acción">
                    <h3>Acción</h3>
                    <p>Juegos de combate y aventura</p>
                </div>
                <div class="category-card">
                    <img src="images/rpg.jpg" alt="RPG">
                    <h3>RPG</h3>
                    <p>Juegos de rol y fantasía</p>
                </div>
                <div class="category-card">
                    <img src="images/strategy.jpg" alt="Estrategia">
                    <h3>Estrategia</h3>
                    <p>Juegos de estrategia</p>
                </div>
            </div>
        </section>

        <!-- PLAYERS RANKING -->
        <section class="ranking">
            <h2>Top Jugadores</h2>
            <div class="ranking-table">
                <table>
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th>Jugador</th>
                            <th>Puntos</th>
                            <th>Juegos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM players ORDER BY points DESC LIMIT 10";
                        $result = mysqli_query($conn, $query);
                        $position = 1;
                        while($player = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>" . $position . "</td>
                                    <td>" . htmlspecialchars($player['username']) . "</td>
                                    <td>" . $player['points'] . "</td>
                                    <td>" . $player['games_played'] . "</td>
                                </tr>";
                            $position++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>