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

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $socket->prepare($sql);

    $stmt->bind_param("s", $_POST["email"]);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0){
        $row = $result->fetch_assoc();

        if (password_verify($_POST["password"], $row["password"])){
            $_SESSION["username"] = $row["username"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["userid"] = $row["userid"];
            $_SESSION["whitelisted"] = $row["whitelisted"];
            $_SESSION["discordusername"] = $row["discordusername"];
            $_SESSION["creation_date"] = $row["creationdate"];
            $_SESSION["role"] = $row["role"];
            $_SESSION["profileimg"] = $row["profileimg"];

            header("Location: ../index.php");
        } else {
            header("Location: ../login.php?loginfailed=" . urlencode(true));
        };
    } else {
        header("Location: ../login.php?loginfailed=" . urlencode(true));
    }
}