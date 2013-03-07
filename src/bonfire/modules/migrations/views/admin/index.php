<section class="intro">
	<div class="container">

		<h1><?php echo lang('mig_title') ?></h1>

		<p class="lead"><?php echo lang('mig_intro') ?></p>

	</div>
</section>

<section class="content">
	<div class="container">

		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-app" data-toggle="tab"><?php echo lang('bf_app') ?></a></li>
			<li><a href="#tab-modules" data-toggle="tab"><?php echo lang('bf_modules') ?></a></li>
			<li><a href="#tab-core" data-toggle="tab"><?php echo lang('bf_bonfire') ?></a></li>
		</ul>


		<div class="tab-content">

			<!-- Application -->
			<div class="tab-pane active" id="tab-app">

				<p><?php echo lang('mig_installed_version') ?>: <b><?php echo $app_installed ?></b> / <?php echo lang('mig_most_recent_version') ?>: <b><?php echo $app_latest ?></b></p>

				<?php if (!isset($app_latest) || empty($app_latest)) : ?>
					<div class="alert alert-warning">
						<?php echo lang('mig_no_migrations'); ?>
					</div>
				<?php endif; ?>

			</div>

			<!-- Modules -->
			<div class="tab-pane" id="tab-modules">

				<?php if (isset($mod_migrations) && is_array($mod_migrations)) :?>
					<table class="table table-striped">
						<thead>
							<tr>
								<th style="vertical-align: bottom;"><?php echo lang('mig_tbl_module'); ?></th>
								<th style="width: 6em"><?php echo lang('mig_tbl_installed_ver'); ?></th>
								<th style="width: 6em"><?php echo lang('mig_tbl_latest_ver'); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($mod_migrations as $module => $migrations) : ?>
							<tr>
								<td><?php echo ucfirst($module) ?></td>
								<td><?php echo $migrations['installed_version'] ?></td>
								<td><?php echo $migrations['latest_version'] ?></td>
								<td style="width: 35em; text-align: right">
									<?php echo form_open(site_url(SITE_AREA .'/developer/migrations/migrate_module/'. $module), 'class="form-horizontal"'); ?>
									<input type="hidden" name="is_module" value="1" />

									<select name="version">
										<option value="uninstall"><?php echo lang('mig_uninstall'); ?></option>
									<?php foreach ($migrations as $migration) : ?>
										<?php if(is_array($migration)): ?>
											<?php foreach ($migration as $filename) :?>
												<option><?php echo $filename; ?></option>
											<?php endforeach; ?>
										<?php endif;?>
									<?php endforeach; ?>
									</select>

									<input type="submit" name="migrate" class="btn btn-primary" value="<?php e(lang('mig_migrate_module')); ?>" />
									<?php echo form_close(); ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

				<?php else : ?>
					<div class="alert alert-warning">
						<?php echo lang('mig_no_migrations') ?>
					</div>
				<?php endif; ?>

			</div>

			<!-- Bonfire -->
			<div class="tab-pane" id="tab-core">

				<p><?php echo lang('mig_installed_version') ?>: <b><?php echo $core_installed ?></b> / <?php echo lang('mig_most_recent_version') ?>: <b><?php echo $core_installed ?></b></p>

				<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
					<input type="hidden" name="core_only" value="1" />

					<?php if (count($core_migrations)) : ?>
					<div class="control-group">
						<label class="control-label" for="migration"><?php echo lang('mig_choose_migration'); ?></label>
						<div class="controls">
							<select name="migration" id="migration">
							<?php foreach ($core_migrations as $migration) :?>
								<option value="<?php echo (int)substr($migration, 0, 3) ?>" <?php echo ((int)substr($migration, 0, 3) == $this->uri->segment(5)) ? 'selected="selected"' : '' ?>><?php echo $migration ?></option>
							<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="form-actions">
						<input type="submit" name="migrate" class="btn btn-primary" value="<?php echo lang('mig_migrate_button'); ?>" /> or <?php echo anchor(SITE_AREA .'/developer/migrations', '<i class="icon-refresh icon-white">&nbsp;</i>&nbsp;' . lang('bf_action_cancel'), 'class="btn btn-danger"'); ?>
					</div>
					<?php else: ?>
						<p><?php echo lang('mig_no_migrations') ?></p>
					<?php endif; ?>
				<?php echo form_close(); ?>

			</div>

		</div>

	</div>
</section>