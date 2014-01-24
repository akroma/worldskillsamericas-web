<div class="xcrud-ajax">
<div class="xcrud-top-actions">
    <?php if($mode == 'edit'){ ?>
    <a class="xcrud-save xcrud-action-button btn btn-small" data-primary="<?php echo $this->primary_key?>" data-after="edit" href="javascript:void(0);"><i class="icon-ok"></i> <?php echo $this->lang('save')?></a>
    <?php }else{ ?>
    <a class="xcrud-save-new xcrud-action-button btn btn-small" data-primary="<?php echo $this->primary_key?>" data-after="create" href="javascript:void(0);"><i class="icon-ok-sign"></i> <?php echo $this->lang('save_new')?></a>
    <a class="xcrud-save-edit xcrud-action-button btn btn-small" data-primary="<?php echo $this->primary_key?>" data-after="edit" href="javascript:void(0);"><i class="icon-ok"></i> <?php echo $this->lang('save_edit')?></a>
    <?php } ?>
    <a class="xcrud-save-return xcrud-action-button btn btn-small" data-primary="<?php echo $this->primary_key?>" data-after="list" href="javascript:void(0);"><i class="icon-download-alt"></i> <?php echo $this->lang('save_return')?></a>
    <a class="xcrud-return xcrud-action-button btn btn-small" href="javascript:void(0);"><i class="icon-chevron-left"></i> <?php echo $this->lang('return')?></a>
    <?php if($this->is_title){ ?>
        <strong class="xcrud-header">
            <?php echo $this->table_name ?>
        </strong>
    <?php } ?>
</div>
<div class="xcrud-view">
<?php 
echo $this->create_tabs($fields, $mode, 'bs');
if ($fields)
{ ?>
    <table class="xcrud-details table table-striped table-condensed table-bordered">
        <?php
        foreach($fields as $item){
            $primary_class = $this->primary == $item['name'] ? ' primary_details' : '';
            echo '<tr>';
            echo '<td class="xcrud-label'.$primary_class.'">'.$item['label'].$this->get_field_tooltip($item['name'], $mode, $driver = 'bs').'</td>';
            echo '<td>'.$item['field'].'</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <?php } echo implode('', $hidden_fields); ?>
    <input type="hidden" class="xcrud-data" name="key" value="<?php echo $this->key?>" />
    <input type="hidden" class="xcrud-data" name="order" value="<?php echo $order_column?>" />
    <input type="hidden" class="xcrud-data" name="direct" value="<?php echo $order_direct?>" />
    <input type="hidden" class="xcrud-data" name="start" value="<?php echo $start?>" />
    <input type="hidden" class="xcrud-data" name="limit" value="<?php echo $limit?>" />
    <input type="hidden" class="xcrud-data" name="instance" value="<?php echo $this->instance_name()?>" />
    <input type="hidden" class="xcrud-data" name="column" value="<?php echo $column?>" />
    <?php echo $this->render_search_hidden($column, $phrase)?>
    <input type="hidden" class="xcrud-data" name="task" value="list" />
    <?php if(Xcrud_config::$dynamic_session) { ?>
    <input type="hidden" class="xcrud-data" name="sess_name" value="<?php echo $this->sess_name?>" />
    <?php } ?>
</div>
<div class="xcrud-nav">
    <span class="xcrud-benchmark"><?php echo $this->benchmark_end()?></span>
    <div class="clearfix"></div>
</div>
<div class="xcrud-overlay"></div>
</div>