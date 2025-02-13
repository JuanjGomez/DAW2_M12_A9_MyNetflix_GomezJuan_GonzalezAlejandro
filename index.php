<?php
    session_start();
    if(isset($_SESSION['successLogin']) && $_SESSION['successLogin']){
        echo '<script>let successLogin = true</script>';
        unset($_SESSION['successLogin']);
    }
    if(isset($_SESSION['successCrear']) && $_SESSION['successCrear']){
        echo '<script>let successCrear = true</script>';
        unset($_SESSION['successCrear']);
    }

require_once 'database/conexion.php';

// Obtener todas las categorías para el filtro
$stmt = $conn->prepare("SELECT * FROM tbl_categorias");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Preparar la consulta base
$sql = "SELECT DISTINCT p.*, c.nombre_cat 
        FROM tbl_peliculas p 
        INNER JOIN tbl_pelicula_categoria pc ON p.id_peli = pc.id_peli 
        INNER JOIN tbl_categorias c ON pc.id_cat = c.id_cat 
        WHERE 1=1";
$params = array();

// Aplicar filtros si existen
if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $sql .= " AND c.id_cat = :categoria";
    $params[':categoria'] = $_GET['categoria'];
}

if (isset($_GET['director']) && !empty($_GET['director'])) {
    $sql .= " AND p.director_peli LIKE :director";
    $params[':director'] = '%' . $_GET['director'] . '%';
}

if (isset($_GET['titulo']) && !empty($_GET['titulo'])) {
    $sql .= " AND p.titulo_peli LIKE :titulo";
    $params[':titulo'] = '%' . $_GET['titulo'] . '%';
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar películas por categoría
$peliculasPorCategoria = array();
foreach ($peliculas as $pelicula) {
    $peliculasPorCategoria[$pelicula['nombre_cat']][] = $pelicula;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css" integrity="sha256-ugbaEitpVSMgCpnPe7m69OyL6M47KkfE36OdRjQXD28=" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Document</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="img/logoN.png">
        </div>
        <div class="user-dropdown">
            <button class="dropbtn">
                <i class="fas fa-user"></i>
            </button>
            <div class="dropdown-content">
                <a href="view/formSesion.php">Inicio Sesión</a>
                <a href="view/formRegistro.php">Registro</a>
            </div>
        </div>

    </header>

    <div class="filtros-container">
        <form action="" method="GET" class="form-filtros">
            <select name="categoria">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id_cat']; ?>" 
                            <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $categoria['id_cat']) ? 'selected' : ''; ?>>
                        <?php echo $categoria['nombre_cat']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="director" placeholder="Buscar por director" 
                   value="<?php echo isset($_GET['director']) ? htmlspecialchars($_GET['director']) : ''; ?>">
            
            <input type="text" name="titulo" placeholder="Buscar por título" 
                   value="<?php echo isset($_GET['titulo']) ? htmlspecialchars($_GET['titulo']) : ''; ?>">
            
            <button type="submit">Filtrar</button>
            <a href="index.php" class="btn-reset">Resetear filtros</a>
        </form>
    </div>

    <main>
        <?php foreach ($peliculasPorCategoria as $categoria => $peliculas): ?>
        <div class="categoria-container">
            <h2 class="categoria-titulo"><?php echo htmlspecialchars($categoria); ?></h2>
            <div class="peliculas-grid">
                <?php foreach ($peliculas as $pelicula): ?>
                <div class="pelicula">
                    <img src="<?php echo htmlspecialchars($pelicula['poster_peli']); ?>" 
                         alt="<?php echo htmlspecialchars($pelicula['titulo_peli']); ?>">
                    <h3><?php echo htmlspecialchars($pelicula['titulo_peli']); ?></h3>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/toolsInicio.js"></script>
</body>
</html>