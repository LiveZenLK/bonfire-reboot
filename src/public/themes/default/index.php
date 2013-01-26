<?php echo theme_view('parts/_header'); ?>

<div class="container body narrow-body"> <!-- Start of Main Container -->

<?php
	echo Template::yield('testing');
	echo Template::yield();
?>

<?php echo theme_view('parts/_footer'); ?>
