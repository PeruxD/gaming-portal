<?php
include 'config/constants.php';
?>

<header class="main-header">
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <a href="index.php">
                    <h1>🎮 FORESTER</h1>
                </a>
            </div>
            
            <ul class="nav-menu">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="games.php">Juegos</a></li>
                <li><a href="ranking.php">Ranking</a></li>
                <li><a href="tournaments.php">Torneos</a></li>
                <li><a href="community.php">Comunidad</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Mi Perfil</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Iniciar Sesión</a></li>
                    <li><a href="register.php" class="btn-register">Registrarse</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
</header>