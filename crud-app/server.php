<?php 
        session_start();

        $servername = getenv('MYSQL_SERVER');
        $db_user = getenv('MYSQL_USER');
        $db_pass = getenv('MYSQL_PASSWORD');
        $db_name = getenv('MYSQL_DATABASE');
        
        mysqli_report(MYSQLI_REPORT_ERROR);

        $db = mysqli_connect( $servername , $db_user, $db_pass , $db_name );
        
        if($db === false){
                die("ERROR: Could not connect. " . mysqli_connect_error());
            }

        
        // initialize database
        $sql = "CREATE TABLE IF NOT EXISTS info ( id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(100) DEFAULT NULL,
                address varchar(100) DEFAULT NULL,
                PRIMARY KEY (id))";
        
        mysqli_query($db, $sql);

        // initialize variables

        $name = "";
        $address = "";
        $id = 0;
        $update = false;

        if (isset($_POST['save'])) {
                $name = $_POST['name'];
                $address = $_POST['address'];

                mysqli_query($db, "INSERT INTO info (name, address) VALUES ('$name', '$address')"); 
                $_SESSION['message'] = "Address saved"; 
                header('location: index.php');
        }
        if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $name = $_POST['name'];
                $address = $_POST['address'];
        
                mysqli_query($db, "UPDATE info SET name='$name', address='$address' WHERE id=$id");
                $_SESSION['message'] = "Address updated!"; 
                header('location: index.php');
        }
        if (isset($_GET['del'])) {
                $id = $_GET['del'];
                mysqli_query($db, "DELETE FROM info WHERE id=$id");
                $_SESSION['message'] = "Address deleted!"; 
                header('location: index.php');
        }


// ...
?>
