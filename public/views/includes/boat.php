<?php

defined("ABSPATH") or die("Get out of here!");
isset($boat) or die("No boat set!");

if( gettype($boat) === "array") $boat = (object)$boat;

$is_admin = $is_admin ?? false;
?>

<div id="skin_<?php echo $boat->id; ?>">
    Le bateau : <span><?php echo $boat->identifier; ?></span>
    <h3><?php echo $boat->name; ?></h3>
    <img src="<?php echo BASE_URL."uploads/boats/".$boat->id.".png"; ?>" alt="<?php echo $boat->name; ?>">

</div>
