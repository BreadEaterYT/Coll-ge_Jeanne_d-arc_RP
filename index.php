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

if (!isset($_SESSION["username"], $_SESSION["email"], $_SESSION["userid"], $_SESSION["creation_date"], $_SESSION["role"], $_SESSION["whitelisted"], $_SESSION["discordusername"], $_SESSION["profileimg"])){
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="./assets/img/icon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/index.css">
    <title>Groupe Scolaire Jeanne d’Arc</title>
</head>
<body>
    <nav>
        <div id="profile">
            <h4><?php echo $_SESSION["username"]?></h4><img src="<?php echo "./users/" . $_SESSION["username"] . "/" . $_SESSION["profileimg"]?>" id="profileimg">
        </div>
        <a href="https://discord.gg/4AQSu85Bhs"><img src="./assets/img/discord.svg" id="discord"></a>
    </nav>
    <div id="profilebox">
        <form action="./controllers/changepasswd.controller.php" method="POST">
            <p>Changer le mot de passe</p>
            <input type="password" name="actualpassword" id="actualpassword" placeholder="Mot de passe actuel..." required><br><br>
            <input type="password" name="newpassword" id="newpassword" placeholder="Nouveau mot de passe..." required><br><br>
            <button type="submit">Changer</button>
        </form>
    </div>
    <script src="./assets/js/profile.js"></script>
</body>
</html>