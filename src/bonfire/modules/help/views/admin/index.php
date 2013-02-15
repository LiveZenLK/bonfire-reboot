<div class="content">
	<div class="container">

		<section class="page">

			<header>
				<?php if (isset($page_content_title)) : ?>
					<?php echo $page_content_title; ?>
				<?php else : ?>
					Need a hand?
				<?php endif; ?>
			</header>

			<div class="row-fluid">

				<div class="span3 sidebar">
					<?php echo isset($toc) ? $toc : ''; ?>
				</div>

				<div class="span9">

					<?php if (isset($page_content)) : ?>
						<?php echo $page_content; ?>
					<?php endif; ?>

				</div>
			</div>
		</section>
	</div>
</div>