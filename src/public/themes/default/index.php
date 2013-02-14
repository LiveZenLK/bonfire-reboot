<?php echo Template::load_view('header', true); ?>

<div id="container">
	<?php echo Template::yield(); ?>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

<?php echo Template::load_view('footer', true); ?>