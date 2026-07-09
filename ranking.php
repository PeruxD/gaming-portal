<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

$page = $_GET['page'] ?? 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get total players
$count_query = "SELECT COUNT(*) as total FROM players";
$count_result = mysqli_query($conn, $count_query);
$total = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total / $per_page);

// Get players ranking
$query = "SELECT p.*, u.username, u.avatar FROM players p 
         JOIN users u ON p.user_id = u.id 
         ORDER BY p.points DESC 
         LIMIT $offset, $per_page";
$result = mysqli_query($conn, $query);

// Get current user rank if logged in
$user_rank = null;
if (isset($_SESSION['user_id'])) {
    $user_rank_query = "SELECT p.*, 
                       (SELECT COUNT(*) FROM players WHERE points > p.points) + 1 as rank 
                       FROM players p WHERE p.user_id = {$_SESSION['user_id']}";
    $user_rank_result = mysqli_query($conn, $user_rank_query);
    $user_rank = mysqli_fetch_assoc($user_rank_result);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - FORESTER Gaming Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .ranking-header {
            text-align: center;
            margin: 2rem 0;
        }

        .ranking-header h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .top-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .top-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            border: 2px solid var(--border-color);
            position: relative;
        }

        .top-card.first {
            border-color: #FFD700;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
            transform: scale(1.05);
        }

        .top-card.second {
            border-color: #C0C0C0;
        }

        .top-card.third {
            border-color: #CD7F32;
        }

        .medal {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .top-card h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .top-card .points {
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: bold;
        }

        .ranking-table-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            border-radius: 10px;
            overflow: hidden;
            margin: 2rem 0;
            border: 1px solid var(--border-color);
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ranking-table thead {
            background: linear-gradient(90deg, #FFD700 0%, #FFC700 100%);
            color: var(--secondary-color);
        }

        .ranking-table th {
            padding: 1rem;
            text-align: left;
            font-weight: bold;
        }

        .ranking-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .ranking-table tbody tr:hover {
            background: rgba(255, 215, 0, 0.05);
        }

        .ranking-table td {
            padding: 1rem;
        }

        .rank-number {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.2rem;
            min-width: 30px;
        }

        .player-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .player-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
            font-weight: bold;
        }

        .rank-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .rank-bronze { background: #CD7F32; color: #FFF; }
        .rank-silver { background: #C0C0C0; color: #000; }
        .rank-gold { background: #FFD700; color: #000; }
        .rank-platinum { background: #E5E4E2; color: #000; }
        .rank-diamond { background: #00D9FF; color: #000; }

        .user-rank-card {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 2px solid var(--primary-color);
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }

        .user-rank-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: var(--primary-color);
            color: var(--secondary-color);
        }

        .pagination .active {
            background: var(--primary-color);
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ranking-header">
            <h2>🏆 Ranking de Jugadores</h2>
            <p>Los mejores jugadores de FORESTER</p>
        </div>

        <!-- USER CURRENT RANK -->
        <?php if ($user_rank): ?>
            <div class="user-rank-card">
                <h3>Tu Rango Actual</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                    <div>
                        <p style="color: #AAA; margin-bottom: 0.25rem;">Posición</p>
                        <p style="font-size: 2rem; color: var(--primary-color); font-weight: bold;">#<?php echo $user_rank['rank']; ?></p>
                    </div>
                    <div>
                        <p style="color: #AAA; margin-bottom: 0.25rem;">Puntos</p>
                        <p style="font-size: 2rem; color: var(--primary-color); font-weight: bold;"><?php echo number_format($user_rank['points']); ?></p>
                    </div>
                    <div>
                        <p style="color: #AAA; margin-bottom: 0.25rem;">Nivel</p>
                        <p style="font-size: 2rem; color: var(--primary-color); font-weight: bold;"><?php echo $user_rank['level']; ?></p>
                    </div>
                    <div>
                        <p style="color: #AAA; margin-bottom: 0.25rem;">Rango</p>
                        <p style="font-size: 1.5rem;"><span class="rank-badge rank-<?php echo strtolower($user_rank['rank']); ?>"><?php echo $user_rank['rank']; ?></span></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- TOP 3 PLAYERS (if on first page) -->
        <?php if ($page == 1 && mysqli_num_rows($result) >= 3): ?>
            <?php mysqli_data_seek($result, 0); ?>
            <div class="top-3">
                <?php
                $medals = ['🥇', '🥈', '🥉'];
                $positions = ['first', 'second', 'third'];
                $rank_num = 1;
                while ($player = mysqli_fetch_assoc($result) and $rank_num <= 3) {
                    echo "<div class='top-card {$positions[$rank_num-1]}'>
                            <div class='medal'>{$medals[$rank_num-1]}</div>
                            <h3>#{$rank_num} - " . htmlspecialchars($player['username']) . "</h3>
                            <p style='color: #AAA; margin-bottom: 1rem;'>
                                Nivel " . $player['level'] . " - 
                                <span class='rank-badge rank-" . strtolower($player['rank']) . "'>" . $player['rank'] . "</span>
                            </p>
                            <div class='points'>" . number_format($player['points']) . " pts</div>
                            <p style='color: #AAA; margin-top: 1rem; font-size: 0.9rem;'>
                                Partidas: " . $player['games_played'] . " | Victorias: " . $player['wins'] . "
                            </p>
                        </div>";
                    $rank_num++;
                }
                ?>
            </div>
            <?php mysqli_data_seek($result, 3); ?>
        <?php endif; ?>

        <!-- FULL RANKING TABLE -->
        <div class="ranking-table-container">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Posición</th>
                        <th>Jugador</th>
                        <th style="width: 120px;">Puntos</th>
                        <th style="width: 100px;">Nivel</th>
                        <th style="width: 120px;">Partidas</th>
                        <th style="width: 100px;">Victorias</th>
                        <th style="width: 120px;">Rango</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($result, 0);
                    $position = $offset + 1;
                    while ($player = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td><span class='rank-number'>#{$position}</span></td>
                                <td>
                                    <div class='player-info'>
                                        <div class='player-avatar'>" . strtoupper(substr($player['username'], 0, 1)) . "</div>
                                        <span>" . htmlspecialchars($player['username']) . "</span>
                                    </div>
                                </td>
                                <td style='color: var(--primary-color); font-weight: bold;'>" . number_format($player['points']) . "</td>
                                <td style='text-align: center;'>⭐ " . $player['level'] . "</td>
                                <td style='text-align: center;'>" . $player['games_played'] . "</td>
                                <td style='text-align: center; color: #90EE90;'>" . $player['wins'] . "</td>
                                <td style='text-align: center;'>
                                    <span class='rank-badge rank-" . strtolower($player['rank']) . "'>" . $player['rank'] . "</span>
                                </td>
                            </tr>";
                        $position++;
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php
                if ($page > 1) {
                    echo "<a href='?page=1'>« Primera</a>";
                    echo "<a href='?page=" . ($page - 1) . "'>‹ Anterior</a>";
                }

                for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
                    $active = $i == $page ? 'active' : '';
                    echo "<a href='?page=$i' class='$active'>$i</a>";
                }

                if ($page < $total_pages) {
                    echo "<a href='?page=" . ($page + 1) . "'>Siguiente ›</a>";
                    echo "<a href='?page=$total_pages'>Última »</a>";
                }
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>