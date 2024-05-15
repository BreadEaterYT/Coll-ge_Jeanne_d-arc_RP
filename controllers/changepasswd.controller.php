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

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $actualpassword = $_POST["actualpassword"];
    $newpassword = $_POST["newpassword"];

    $socket = mysqli_connect($_ENV["MYSQL_HOSTNAME"], $_ENV["MYSQL_USERNAME"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);

    $sql = "SELECT password FROM users WHERE userid = ?";
    $stmt = $socket->prepare($sql);

    $stmt->bind_param("i", $_SESSION["userid"]);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (password_verify($actualpassword, $row["password"])){
        $stmt->close();

        $updatesql = "UPDATE users SET password = ? WHERE userid = ?";
        $updatequery = $socket->prepare($updatesql);

        $hash = password_hash($newpassword, PASSWORD_BCRYPT);

        $updatequery->bind_param("si", $hash, $_SESSION["userid"]);
        $updatequery->execute();

        $updatequery->close();
        $socket->close();

        session_destroy();

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="shortcut icon" href="../assets/img/icon.jpg" type="image/x-icon">
            <link rel="stylesheet" href="../assets/css/changepasswd.controller.css">
            <title>Groupe Scolaire Jeanne d’Arc</title>
        </head>
        <body>
            <div id="error">
                <h4>Le mot de passe a bien été changée</h4><br>
                <a href="../login.php"><button type="submit">Retour a la page de login</button></a>
            </div>
        </body>
        </html>
        HTML;
    } else {
        $stmt->close();
        $socket->close();

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="shortcut icon" href="../assets/img/icon.jpg" type="image/x-icon">
                <link rel="stylesheet" href="../assets/css/changepasswd.controller.css">
                <title>Groupe Scolaire Jeanne d’Arc</title>
            </head>
            <body>
                <div id="error">
                    <h4>Le mot de passe actuel n'est pas le bon</h4><br>
                    <a href="../index.php"><button type="submit">Retour a la page principal</button></a>
                </div>
            </body>
            </html>
        HTML;
    }
}