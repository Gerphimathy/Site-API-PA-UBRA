<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__."/includes/head.php";?>
    <title>Shop</title>
</head>
<body>
<?php include __DIR__."/includes/header.php";?>
<main>

<?php
$scripts[] = "shop.js";
$scripts[] = "sendRequest.js";

$skins = Skin::getAllSkinsShop(0,10, $__uid);
$count = Skin::getSkinTotalShop($__uid);
$pages = ceil($count/10);

$shop = true;
foreach ($skins as $skin) {
    include __DIR__."/includes/skin.php";
}
?>

</main>
<?php include __DIR__."/includes/footer.php";?>
</body>
</html>