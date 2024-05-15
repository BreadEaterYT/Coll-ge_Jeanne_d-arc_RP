<?php
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/img/icon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/register.css">
    <title>Groupe Scolaire Jeanne d’Arc</title>
</head>
<body>
    <div id="registerform">
        <img src="./assets/img/icon.jpg">
        <h4>Groupe Scolaire Jeanne d’Arc</h4><br>
        <form action="./controllers/register.controller.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="username" id="username" placeholder="Pseudo..." required><br><br>
            <input type="email" name="email" id="email" placeholder="Email..." required><br><br>
            <input type="text" name="discordusername" id="discordusername" placeholder="Pseudo Discord..." required><br><br>
            <input type="password" name="password" id="password" placeholder="Mot de passe..." required><br><br>
            <input type="file" name="file" id="file" accept="image/*" required><br><br>
            <button type="submit">Register</button><br><br>
        </form>
        <?php
        if ($_GET["creationfailed"] === urlencode(true)){
        echo <<<HTML
        <style>
            #registerform{
                width: 400px;
                margin: 50px auto;
                background-color: white;
                text-align: center;
                align-content: center;
                border-radius: 10px;
                height: 786px;
            }
                
            #creationfailed{
                width: 200px;
                margin:0 auto;
                background-color: rgba(255, 0, 0, 0.8);
                text-align: center;
                align-content: center;
                padding-bottom: 5px;
                padding-right: 5px;
                padding-left: 5px;
                padding-top: 5px;
                border-radius: 10px
            }
        </style>
        <div id="creationfailed">
            <h5>Un compte a été deja crée avec ce pseudo !</h5>
        </div><br>
        HTML;
        }
        ?>
        <a href="login.php">Deja un compte ?</a>
    </div>
</body>
</html>