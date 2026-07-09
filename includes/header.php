<?php
$base_url = '/3/';
?>

<header class="main-header">
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <a href="<?php echo $base_url; ?>index.php">
                    <h1>🎮 FORESTER</h1>
                </a>
            </div>
            
            <ul class="nav-menu">
                <li><a href="<?php echo $base_url; ?>index.php">Inicio</a></li>
                <li><a href="<?php echo $base_url; ?>games.php">Juegos</a></li>
                <li><a href="<?php echo $base_url; ?>ranking.php">Ranking</a></li>
                <li><a href="<?php echo $base_url; ?>tournaments.php">Torneos</a></li>
                <li><a href="<?php echo $base_url; ?>community.php">Comunidad</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo $base_url; ?>profile.php">Mi Perfil</a></li>
                    <li><a href="<?php echo $base_url; ?>logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_url; ?>auth/login.php" class="btn-login">Iniciar Sesión</a></li>
                    <li><a href="<?php echo $base_url; ?>auth/register.php" class="btn-register">Registrarse</a></li>
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
