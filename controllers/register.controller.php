<?php
require __DIR__ . "/../vendor/autoload.php";
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

session_set_cookie_params([
    "lifetime" => 21600,
    "domain" => "localhost",
    "path" => "/",
    "secure" => true,
    "httponly" => true
]);

session_start();

if (!isset($_SESSION["last_regen"])){
    session_regenerate_id();
    $_SESSION["last_regen"] = time();
} else {
    $interval = 60 * 30;
    if (time() - $_SESSION["last_regen"] >= $interval){
        session_regenerate_id();
        $_SESSION["last_regen"] = time();
    }
}

if (isset($_SESSION["username"], $_SESSION["email"], $_SESSION["userid"], $_SESSION["creation_date"], $_SESSION["role"], $_SESSION["whitelisted"], $_SESSION["discordusername"], $_SESSION["profileimg"])){
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $socket = mysqli_connect($_ENV["MYSQL_HOSTNAME"], $_ENV["MYSQL_USERNAME"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);

    $sql = "INSERT INTO `users` (`username`, `email`, `password`, `userid`, `role`, `whitelisted`, `discordusername`, `profileimg`, `creationdate`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())";
    $stmt = $socket->prepare($sql);
    
    $hash = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $id = date("YmdHis");
    $role = "student";
    $whitelisted = 0; // false

    try {
        if (!is_dir(__DIR__ . "/../users/" . $_POST["username"])){
            mkdir(__DIR__ . "/../users/" . $_POST["username"]);
        }
    
        $uploadDir = __DIR__ . "/../users/" . $_POST["username"] . "/";
        $uploadPath = $uploadDir . basename($_FILES["file"]["name"]);
    
        move_uploaded_file($_FILES["file"]["tmp_name"], $uploadPath);
    
        $stmt->bind_param("sssisiss", $_POST["username"], $_POST["email"], $hash, $id, $role, $whitelisted, $_POST["discordusername"], $_FILES["file"]["name"]);
        $stmt->execute();

        $stmt->close();
        $socket->close();

        header("Location: ../login.php?creationsuccess=" . urlencode(true));
    } catch (Throwable $e){
        $stmt->close();
        $socket->close();

        header("Location: ../register.php?creationfailed=" . urlencode(true));
    }
}