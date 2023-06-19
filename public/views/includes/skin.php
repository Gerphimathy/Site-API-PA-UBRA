<?php

defined("ABSPATH") or die("Get out of here!");
isset($skin) or die("No skin set!");

$boat = (object)Boat::getBoatData($skin->id_boat);
?>

<div id="skin_<?php echo $skin->id; ?>">
    <h3><?php echo $skin->name; ?></h3>
    Pour le bateau :
    <h3><?php echo $boat->name; ?></h3>
</div>
