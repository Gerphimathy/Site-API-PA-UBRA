<?php


function addScripts(array $scripts):void{
    foreach ($scripts as $script) {
        echo '<script src="'.BASE_URL.'js/'.$script.'" type="text/javascript"></script>';
    }
}