<?php echo Template::load_view('_header', true); ?>

	<?php echo Template::load_view('_navbar', true); ?>

	<div>

		<?php echo Template::yield(); ?>

	</div>

<?php echo Template::load_view('_footer', true); ?>