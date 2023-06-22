<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include_once __DIR__."/includes/head.php";?>
    <title>Admin</title>
</head>
<body>
<?php include __DIR__."/includes/header.php";?>
<main>
    <?php
        $is_admin = User::isAdmin($__uid);
        if(!$is_admin){
            header("Location: /404");
            die();
        }
        $scripts[] = "admin.js";
    ?>
    <h2>Bateaux</h2>
    <?php
    $boats = Boat::getAllBoatsData();
    foreach ($boats as $boat){
        include __DIR__."/includes/boat.php";
    }
    ?>

    <form method="post" action="/actions/add_boat.php"  enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Nom du bateau" required>
        <input type="file" name="image" accept="image/png, image/jpeg, image/webp" required>
        <input type="text" name="identifier" placeholder="identifiant unique" minlength="10" maxlength="10" required>
        <button type="submit">Ajouter bateau</button>
    </form>

    <h3>Skins</h3>
    <?php
    $shop = false;
    $skins = Skin::getAllSkinsShop(0, 10);
    foreach ($skins as $skin){
        include __DIR__."/includes/skin.php";
    }
    ?>

    <form method="post" action="/actions/add_skin.php"  enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Nom du skin" required>
        <input type="file" name="image" accept="image/png, image/jpeg, image/webp" required>
        <input type="number" name="price" placeholder="Prix en points" required>
        <input type="text" name="identifier" placeholder="identifiant unique" minlength="10" maxlength="10" required>
        <select name="id_boat" required>
            <?php
            $boats = Boat::getAllBoatsData();
            foreach ($boats as $boat){
                if (gettype($boat) === "array") $boat = (object)$boat;
                echo '<option value="'.$boat->id.'">'.$boat->name.'</option>';
            }
            ?>
        </select>
        <button type="submit">Ajouter skin</button>
    </form>

</main>
<?php include __DIR__."/includes/footer.php";?>
</body>
</html>