<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

$status_filter = $_GET['status'] ?? 'all';
$where = "1=1";
if ($status_filter != 'all') {
    $where = "status = '$status_filter'";
}

// Get tournaments
$query = "SELECT t.*, COUNT(tp.id) as participants FROM tournaments t 
         LEFT JOIN tournament_participants tp ON t.id = tp.tournament_id 
         WHERE $where 
         GROUP BY t.id 
         ORDER BY t.start_date ASC";
$result = mysqli_query($conn, $query);

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $tournament_id = intval($_POST['tournament_id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if already registered
    $check = "SELECT * FROM tournament_participants WHERE tournament_id = $tournament_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($check_result) == 0) {
        $insert = "INSERT INTO tournament_participants (tournament_id, user_id) VALUES ($tournament_id, $user_id)";
        if (mysqli_query($conn, $insert)) {
            echo "<div style='background: #006B3F; color: #90EE90; padding: 1rem; border-radius: 5px; margin: 1rem 0;'>✅ Registrado en el torneo</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneos - FORESTER Gaming Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .tournaments-header {
            text-align: center;
            margin: 2rem 0;
        }

        .tournaments-header h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .tournament-filters {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tournament-filters a {
            padding: 0.75rem 1.5rem;
            background: var(--secondary-color);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 25px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .tournament-filters a.active,
        .tournament-filters a:hover {
            background: var(--primary-color);
            color: var(--secondary-color);
        }

        .tournaments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .tournament-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .tournament-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
        }

        .tournament-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-color);
        }

        .tournament-content {
            padding: 1.5rem;
        }

        .tournament-title {
            color: var(--primary-color);
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .tournament-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .status-upcoming {
            background: #4A90E2;
            color: #FFF;
        }

        .status-ongoing {
            background: #7ED321;
            color: #000;
        }

        .status-completed {
            background: #B8E986;
            color: #000;
        }

        .tournament-info {
            display: grid;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #AAA;
            margin-bottom: 1rem;
        }

        .tournament-info-item {
            display: flex;
            justify-content: space-between;
        }

        .tournament-prize {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .tournament-actions {
            display: flex;
            gap: 0.5rem;
        }

        .tournament-actions a,
        .tournament-actions button {
            flex: 1;
            padding: 0.75rem;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-register {
            background: var(--primary-color);
            color: var(--secondary-color);
        }

        .btn-register:hover {
            background: #FFC700;
        }

        .btn-view {
            background: var(--secondary-color);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .btn-view:hover {
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tournaments-header">
            <h2>🏅 Torneos Disponibles</h2>
            <p>Participa en emocionantes competiciones y gana premios</p>
        </div>

        <!-- FILTERS -->
        <div class="tournament-filters">
            <a href="?status=all" class="<?php echo $status_filter == 'all' ? 'active' : ''; ?>">Todos</a>
            <a href="?status=upcoming" class="<?php echo $status_filter == 'upcoming' ? 'active' : ''; ?>">Próximamente</a>
            <a href="?status=ongoing" class="<?php echo $status_filter == 'ongoing' ? 'active' : ''; ?>">En Vivo</a>
            <a href="?status=completed" class="<?php echo $status_filter == 'completed' ? 'active' : ''; ?>">Completados</a>
        </div>

        <!-- TOURNAMENTS GRID -->
        <div class="tournaments-grid">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($tournament = mysqli_fetch_assoc($result)) {
                    $status_class = 'status-' . $tournament['status'];
                    $status_text = ucfirst($tournament['status']);
                    $start_date = date('d/m/Y H:i', strtotime($tournament['start_date']));
                    
                    echo "<div class='tournament-card'>
                            <div class='tournament-image'>🎮</div>
                            <div class='tournament-content'>
                                <h3 class='tournament-title'>" . htmlspecialchars($tournament['title']) . "</h3>
                                <span class='tournament-status $status_class">$status_text</span>
                                
                                <div class='tournament-info'>
                                    <div class='tournament-info-item'>
                                        <span>📅 Inicio:</span>
                                        <strong>$start_date</strong>
                                    </div>
                                    <div class='tournament-info-item'>
                                        <span>👥 Participantes:</span>
                                        <strong>" . $tournament['participants'] . "/" . $tournament['max_participants'] . "</strong>
                                    </div>
                                    <div class='tournament-info-item'>
                                        <span>🎮 Juego:</span>
                                        <strong>Juego #" . $tournament['game_id'] . "</strong>
                                    </div>
                                </div>

                                <div class='tournament-prize'>💰 \$" . number_format($tournament['prize_pool'], 2) . "</div>

                                <div class='tournament-actions'>";
                    
                    if (isset($_SESSION['user_id'])) {
                        echo "<form method='POST' style='flex: 1;'>
                                <input type='hidden' name='tournament_id' value='" . $tournament['id'] . "'>
                                <button type='submit' class='btn-register' style='width: 100%;'>Registrarse</button>
                            </form>";
                    } else {
                        echo "<a href='auth/login.php' class='btn-register'>Registrarse</a>";
                    }
                    
                    echo "<a href='tournament-detail.php?id=" . $tournament['id'] . "' class='btn-view'>Ver Detalles</a>
                                </div>
                            </div>
                        </div>";
                }
            } else {
                echo "<div style='grid-column: 1/-1; text-align: center; padding: 3rem; color: #AAA;'>
                        <h3>😕 No hay torneos disponibles</h3>
                        <p>Vuelve más tarde para ver nuevos torneos</p>
                    </div>";
            }
            ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>