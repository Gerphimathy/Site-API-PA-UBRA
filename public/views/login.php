<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__."/includes/head.php";?>
</head>
<body>
<?php include __DIR__."/includes/header.php";?>
<main>

<?php

$scripts[] = "sendRequest.js";
if (!isset($_SESSION["token"])):
    $scripts[] = "login.js";
    ?>
    <form id="login-form">
        <label>
            <input type="email" name="login" placeholder="Nom d'utilisateur">
        </label>
        <label>
            <input type="password" name="password" placeholder="Mot de passe">
        </label>
        <button type="button" id="login-trigger">Se connecter</button>
    </form>
    <form id="register-form">
        <label>
            <input type="email" name="login" placeholder="Nom d'utilisateur">
        </label>
        <label>
            <input type="password" name="password" placeholder="Mot de passe">
        </label>
        <label>
            <input type="password" name="password2" placeholder="Confirmer le mot de passe">
        </label>
        <button type="button" id="register-trigger">S'inscrire</button>
    </form>

    <form id="sesh-form" method="post" action="/actions/create_sesh.php">
        <input type="hidden" name="token" value="">
    </form>
<?php else:
    $scripts[] = "user.js"
    ?>
    <p>Vous êtes connecté !</p>
    <?php
        include_once __DIR__."/../../api/models/User.php";
        include_once __DIR__."/../../api/models/Skin.php";
        include_once __DIR__."/../../api/models/Boat.php";
        $user_data = User::getUserData($__uid);
    ?>

    <label>
        Username
        <input id="username" type="text" value="<?php echo $user_data["username"]; ?>">
        <button id="username_refresh">Changer le nom d'utilisateur</button>
    </label>
<br>
    <label>
        Code d'identification
        <input id="id_code" type="password" readonly value="<?php echo $user_data["id_code"]; ?>">
        <button id="id_code_refresh">Générer un nouveau code</button>
        <button id="id_code_reveal">Révéler le code</button>
        <button id="id_code_copy">Copier le code</button>
    </label>
    <p>Points: <?php echo $user_data["points"]; ?></p>

    <div>
        <?php $skins = Skin::ownedSkins($__uid);
        foreach ($skins as $skin){
            include __DIR__."/includes/skin.php";
        }
        ?>
    </div>
<?php endif; ?>

</main>
<?php include __DIR__."/includes/footer.php";?>
</body>
</html>