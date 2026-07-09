<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

$page = $_GET['page'] ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidad - FORESTER Gaming Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .community-header {
            text-align: center;
            margin: 2rem 0;
        }

        .community-header h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .community-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            margin: 2rem 0;
        }

        .community-sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            height: fit-content;
            position: sticky;
            top: 80px;
        }

        .community-sidebar h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .community-stats {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .stat-number {
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: bold;
        }

        .stat-label {
            color: #AAA;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .community-featured {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            padding: 1.5rem;
            border-radius: 5px;
            border: 1px solid var(--primary-color);
        }

        .featured-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .featured-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .featured-item h4 {
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .featured-item p {
            color: #AAA;
            font-size: 0.9rem;
        }

        .posts-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .post-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .post-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.1);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .post-author {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .post-date {
            color: #AAA;
            font-size: 0.85rem;
        }

        .post-title {
            color: var(--text-color);
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .post-content {
            color: #DDD;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .post-stats {
            display: flex;
            gap: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.9rem;
            color: #AAA;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #AAA;
        }

        @media (max-width: 768px) {
            .community-container {
                grid-template-columns: 1fr;
            }

            .community-sidebar {
                position: relative;
                top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="community-header">
            <h2>👥 Comunidad FORESTER</h2>
            <p>Únete a miles de jugadores en la comunidad más épica</p>
        </div>

        <div class="community-container">
            <!-- COMMUNITY SIDEBAR -->
            <aside class="community-sidebar">
                <h3>📊 Estadísticas</h3>
                <div class="community-stats">
                    <div class="stat-card">
                        <div class="stat-number">10.5K</div>
                        <div class="stat-label">Miembros Activos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">256</div>
                        <div class="stat-label">Juegos Disponibles</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">45</div>
                        <div class="stat-label">Torneos Anuales</div>
                    </div>
                </div>

                <h3 style="margin-top: 2rem;">⭐ Destacados</h3>
                <div class="community-featured">
                    <div class="featured-item">
                        <h4>Torneo Global</h4>
                        <p>Compite contra los mejores jugadores del mundo</p>
                    </div>
                    <div class="featured-item">
                        <h4>Eventos Semanales</h4>
                        <p>Nuevas competiciones cada semana</p>
                    </div>
                    <div class="featured-item">
                        <h4>Discord Oficial</h4>
                        <p>Únete a nuestro servidor Discord</p>
                    </div>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-primary" style="width: 100%; margin-top: 2rem;">✍️ Crear Publicación</button>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-primary" style="width: 100%; margin-top: 2rem; text-align: center; text-decoration: none; display: block;">Inicia sesión para participar</a>
                <?php endif; ?>
            </aside>

            <!-- COMMUNITY POSTS -->
            <div class="posts-container">
                <!-- Sample Posts -->
                <article class="post-card">
                    <div class="post-header">
                        <div>
                            <div class="post-author">👤 GameMaster99</div>
                            <div class="post-date">Hace 2 horas</div>
                        </div>
                    </div>
                    <h3 class="post-title">¿Cuál es tu juego favorito en la plataforma?</h3>
                    <div class="post-content">
                        Hola comunidad, me gustaría conocer cuál es el juego favorito de todos ustedes. Recientemente descubrí algunos títulos increíbles y me encantaría recomendaciones.
                    </div>
                    <div class="post-stats">
                        <span>💬 45 Comentarios</span>
                        <span>👍 156 Likes</span>
                        <span>👁️ 1.2K Vistas</span>
                    </div>
                </article>

                <article class="post-card">
                    <div class="post-header">
                        <div>
                            <div class="post-author">👤 ProPlayer2024</div>
                            <div class="post-date">Hace 5 horas</div>
                        </div>
                    </div>
                    <h3 class="post-title">Tips y trucos para mejorar tu ranking</h3>
                    <div class="post-content">
                        Aquí comparto algunos consejos que me han ayudado a llegar al rango Diamond. La práctica consistente es clave, pero también necesitas entender la mecánica del juego.
                    </div>
                    <div class="post-stats">
                        <span>💬 78 Comentarios</span>
                        <span>👍 298 Likes</span>
                        <span>👁️ 3.5K Vistas</span>
                    </div>
                </article>

                <article class="post-card">
                    <div class="post-header">
                        <div>
                            <div class="post-author">👤 CommunityMod</div>
                            <div class="post-date">Hace 1 día</div>
                        </div>
                    </div>
                    <h3 class="post-title">📢 Anuncio: Nuevo torneo global comienza el mes que viene</h3>
                    <div class="post-content">
                        ¡Tenemos noticias emocionantes! El próximo mes lanzaremos un torneo global con un premio de $100,000 USD. Los detalles se anunciarán pronto. Mantente atento.
                    </div>
                    <div class="post-stats">
                        <span>💬 234 Comentarios</span>
                        <span>👍 1.2K Likes</span>
                        <span>👁️ 8.9K Vistas</span>
                    </div>
                </article>

                <article class="post-card">
                    <div class="post-header">
                        <div>
                            <div class="post-author">👤 NewPlayer123</div>
                            <div class="post-date">Hace 1 día</div>
                        </div>
                    </div>
                    <h3 class="post-title">Soy nuevo en la plataforma, ¿por dónde empiezo?</h3>
                    <div class="post-content">
                        Hola a todos, acabo de unirme a FORESTER y estoy un poco abrumado por la cantidad de opciones. ¿Pueden recomendarme algunos juegos para principiantes?
                    </div>
                    <div class="post-stats">
                        <span>💬 23 Comentarios</span>
                        <span>👍 89 Likes</span>
                        <span>👁️ 542 Vistas</span>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>