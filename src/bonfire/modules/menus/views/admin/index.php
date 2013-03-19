<section class="intro">
	<div class="container">
        <div class="pull-left">
		  <h1><?php echo lang('menu_menus'); ?></h1>
		  <p class="lead"><?php echo lang('menu_intro'); ?></p>
        </div>

        <ul class="unstyled pull-right span2">
            <li><a href="#modal-add-menu" data-toggle="modal"><i class="icon-plus"></i> <?php echo lang('menu_add'); ?></a></li>
        </ul>
	</div>
</section>

<section class="content">
	<div class="container">

    <?php if (isset($menus) && is_array($menus) && count($menus)) : ?>

    <?php else: ?>

        <div class="hero-unit">
            <h2><?php echo lang('menu_blank_header'); ?></h2>
            <p><?php echo lang('menu_blank_summary'); ?></p>

            <a href="#modal-add-menu" class="btn btn-large btn-primary" data-toggle="modal"><?php echo lang('menu_blank_button'); ?></a>
        </div>

    <?php endif; ?>

	</div>
</section>

<?php echo $this->load->view('menus/admin/modal_add_menu'); ?>