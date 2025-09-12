<?php 
session_start();

// Подключение к базе данных выносим отдельно
$servername = getenv('MYSQL_SERVER');
$db_user = getenv('MYSQL_USER');
$db_pass = getenv('MYSQL_PASSWORD');
$db_name = getenv('MYSQL_DATABASE');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = mysqli_connect($servername, $db_user, $db_pass, $db_name);
    if ($db === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    // Инициализация базы данных
    $sql = "CREATE TABLE IF NOT EXISTS info (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) DEFAULT NULL,
        address varchar(100) DEFAULT NULL,
        PRIMARY KEY (id)
    )";
    
    mysqli_query($db, $sql);
    
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Инициализация CSRF токена
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = $name = $address = '';
$update = false;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $update = true;
    
    $stmt = $db->prepare("SELECT * FROM info WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $record = $stmt->get_result();

    if ($record && mysqli_num_rows($record) == 1) {
        $n = mysqli_fetch_array($record);
        $name = htmlspecialchars($n['name']);
        $address = htmlspecialchars($n['address']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD: Create, Update, Delete PHP MySQL</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <?php 
    $msg = 'Update v2 hostname is: ' . getenv('HOSTNAME');
    if (isset($msg) && !empty($msg)): ?>
        <div class="title">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif ?>

    <form method="post" action="process.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="input-group">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $name; ?>" required>
        </div>
        <div class="input-group">
            <label>Address</label>
            <input type="text" name="address" value="<?php echo $address; ?>" required>
        </div>
        <div class="input-group">
            <?php if ($update == true): ?>
                <button class="btn" type="submit" name="update" style="background: #556B2F;">Update</button>
            <?php else: ?>
                <button class="btn" type="submit" name="save">Save</button>
            <?php endif ?>
        </div>
    </form>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="msg">
            <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif ?>

    <?php 
    $results = mysqli_query($db, "SELECT * FROM info");
    if ($results && mysqli_num_rows($results) > 0): 
    ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($results)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td>
                            <a href="index.php?edit=<?php echo (int)$row['id']; ?>" class="edit_btn">Edit</a>
                        </td>
                        <td>
                            <a href="process.php?del=<?php echo (int)$row['id']; ?>" class="del_btn" 
                               onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="msg">No records found</div>
    <?php endif; ?>
</body>
</html>