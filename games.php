<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'featured';
$page = $_GET['page'] ?? 1;
$per_page = 12;

// Build query
$where = "1=1";
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $where .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}
if (!empty($category)) {
    $category = mysqli_real_escape_string($conn, $category);
    $where .= " AND category_id = '$category'";
}

// Sort
$order = "featured DESC, rating DESC";
if ($sort == 'rating') {
    $order = "rating DESC";
} elseif ($sort == 'newest') {
    $order = "release_date DESC";
} elseif ($sort == 'popular') {
    $order = "players DESC";
}

// Count total
$count_query = "SELECT COUNT(*) as total FROM games WHERE $where";
$count_result = mysqli_query($conn, $count_query);
$count = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($count / $per_page);
$offset = ($page - 1) * $per_page;

// Get games
$query = "SELECT * FROM games WHERE $where ORDER BY $order LIMIT $offset, $per_page";
$result = mysqli_query($conn, $query);

// Get categories for filter
$cat_query = "SELECT * FROM categories";
$categories = mysqli_query($conn, $cat_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos - FORESTER Gaming Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .games-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            margin: 2rem 0;
        }

        .filters-sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a3a 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            height: fit-content;
            position: sticky;
            top: 80px;
        }

        .filter-group {
            margin-bottom: 2rem;
        }

        .filter-group label {
            display: block;
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: var(--secondary-color);
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn-filter {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            color: var(--secondary-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: #FFC700;
            transform: scale(1.02);
        }

        .games-content {
            display: flex;
            flex-direction: column;
        }

        .games-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .games-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
        }

        .sort-options {
            display: flex;
            gap: 1rem;
        }

        .sort-options a {
            padding: 0.5rem 1rem;
            background: var(--secondary-color);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .sort-options a.active,
        .sort-options a:hover {
            background: var(--primary-color);
            color: var(--secondary-color);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #AAA;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
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

        @media (max-width: 768px) {
            .games-container {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                position: relative;
                top: 0;
            }

            .sort-options {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="games-header">
            <h2>Catalogo de Juegos</h2>
            <div class="sort-options">
                <a href="?sort=featured" class="<?php echo $sort == 'featured' ? 'active' : ''; ?>">Destacados</a>
                <a href="?sort=rating" class="<?php echo $sort == 'rating' ? 'active' : ''; ?>">Mejor Calificados</a>
                <a href="?sort=newest" class="<?php echo $sort == 'newest' ? 'active' : ''; ?>">Mas Nuevo</a>
                <a href="?sort=popular" class="<?php echo $sort == 'popular' ? 'active' : ''; ?>">Mas Popular</a>
            </div>
        </div>

        <div class="games-container">
            <!-- FILTERS SIDEBAR -->
            <aside class="filters-sidebar">
                <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">Filtros</h3>
                <form method="GET" action="games.php">
                    <div class="filter-group">
                        <label>Buscar Juego</label>
                        <input type="text" name="search" placeholder="Nombre del juego..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>

                    <div class="filter-group">
                        <label>Categoria</label>
                        <select name="category">
                            <option value="">Todas las categorias</option>
                            <?php
                            while ($cat = mysqli_fetch_assoc($categories)) {
                                $selected = $category == $cat['id'] ? 'selected' : '';
                                echo "<option value='" . $cat['id'] . "' " . $selected . ">" . $cat['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-filter">Aplicar Filtros</button>
                </form>
            </aside>

            <!-- GAMES GRID -->
            <div class="games-content">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="games-grid">
                        <?php
                        while ($game = mysqli_fetch_assoc($result)) {
                            echo "<div class='game-card'>
                                    <img src='" . htmlspecialchars($game['image']) . "' alt='" . htmlspecialchars($game['title']) . "'>
                                    <h3>" . htmlspecialchars($game['title']) . "</h3>
                                    <p style='font-size: 0.85rem; color: #AAA; margin: 0.5rem 0;'>" . htmlspecialchars(substr($game['description'], 0, 80)) . "...</p>
                                    <div class='game-meta'>
                                        <span class='rating'> " . $game['rating'] . "/10</span>
                                        <span class='players'> " . number_format($game['players']) . "</span>
                                    </div>
                                    <a href='game-detail.php?id=" . $game['id'] . "' class='btn btn-small' style='width: 100%; text-align: center; margin-top: 1rem;'>Ver Detalles</a>
                                </div>";
                        }
                        ?>
                    </div>

                    <!-- PAGINATION -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php
                            $query_params = !empty($search) ? "&search=" . urlencode($search) : "";
                            $query_params .= !empty($category) ? "&category=" . $category : "";
                            $query_params .= "&sort=" . $sort;

                            if ($page > 1) {
                                echo "<a href='?page=1" . $query_params . "'>Primera</a>";
                                echo "<a href='?page=" . ($page - 1) . $query_params . "'>Anterior</a>";
                            }

                            for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
                                $active = $i == $page ? 'active' : '';
                                echo "<a href='?page=" . $i . $query_params . "' class='" . $active . "'>" . $i . "</a>";
                            }

                            if ($page < $total_pages) {
                                echo "<a href='?page=" . ($page + 1) . $query_params . "'>Siguiente</a>";
                                echo "<a href='?page=" . $total_pages . $query_params . "'>Ultima</a>";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="no-results">
                        <h3>No se encontraron juegos</h3>
                        <p>Intenta con otros filtros o busqueda</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
