<?php
$base_url = '/3/';
?>

<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>FORESTER</h3>
            <p>El mejor portal gaming para jugadores de todo el mundo.</p>
            <div class="social-links">
                <a href="#" class="social-icon">f</a>
                <a href="#" class="social-icon">𝕏</a>
                <a href="#" class="social-icon">▶</a>
                <a href="#" class="social-icon">📷</a>
            </div>
        </div>
        
        <div class="footer-section">
            <h4>Enlaces Rápidos</h4>
            <ul>
                <li><a href="<?php echo $base_url; ?>index.php">Inicio</a></li>
                <li><a href="<?php echo $base_url; ?>games.php">Juegos</a></li>
                <li><a href="<?php echo $base_url; ?>ranking.php">Ranking</a></li>
                <li><a href="<?php echo $base_url; ?>tournaments.php">Torneos</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Soporte</h4>
            <ul>
                <li><a href="#">Contacto</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Términos de Servicio</a></li>
                <li><a href="#">Política de Privacidad</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Newsletter</h4>
            <form class="newsletter-form">
                <input type="email" placeholder="Tu email" required>
                <button type="submit" class="btn btn-primary">Suscribirse</button>
            </form>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2024 FORESTER Gaming Portal. Todos los derechos reservados.</p>
    </div>
</footer>

<script src="<?php echo $base_url; ?>js/main.js"></script>
