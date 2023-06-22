<?php

defined("ABSPATH") or die("Get out of here!");
isset($skin) or die("No skin set!");

if( gettype($skin) === "array") $skin = (object)$skin;
$boat = (object)Boat::getBoatData($skin->id_boat);

$is_admin = $is_admin ?? false;
?>

<div id="skin_<?php echo $skin->id; ?>">
    <h3><?php echo $skin->name; ?></h3>
    / <span><?php echo $skin->identifier; ?></span>
    Pour le bateau :
    <h3><?php echo $boat->name; ?></h3>
    / <span><?php echo $boat->identifier; ?></span>

    <img src="<?php echo BASE_URL."uploads/skins/".$skin->id.".png"; ?>" alt="<?php echo $skin->name; ?>">

    <?php if ($shop && $__uid > 0): ?>
        <span>Prix : <?php echo $skin->price; ?> points</span>
        <form>
            <input class="id_skin" type="hidden" name="id_skin" value="<?php echo $skin->id; ?>">
            <button class="buy" name="buy" type="button">Acheter</button>
        </form>
    <?php endif; ?>
</div>
