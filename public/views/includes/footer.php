<?php defined('ABSPATH') or die("Get out of here!"); ?>

<footer>
    <?php if (isset($scripts)) addScripts($scripts);?>

    <script>
        //Values
        const token = "<?php echo $_SESSION["token"] ?? null; ?>";
    </script>
</footer>