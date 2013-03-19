<div id="modal-add-menu" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">x</button>
        <h3><?php echo lang('menu_add'); ?></h3>
    </div>

    <div class="modal-body">
    <?php echo form_open(current_url(), 'class="form-horizontal'); ?>

        <div class="control-group">
            <label class="control-label" for="name">Menu Name</label>
            <div class="controls">
                <input type="text" name="name" value="" class="input-xlarge" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="description">Description</label>
            <div class="controls">
                <textarea name="description" class="input-xlarge" rows="3" ></textarea>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <input type="submit" name="submit-add-menu" class="btn btn-primary" value="Create Menu" /> or
        <a href="#" data-dismiss="modal">Close</a>
    </div>
    <?php echo form_close(); ?>
</div>