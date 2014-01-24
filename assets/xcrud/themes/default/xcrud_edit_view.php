<div class="xcrud-ajax">
<div class="xcrud-top-actions">
    <?php if ($mode == 'edit')
{ ?>
    <a class="xcrud-save xcrud-action-button" data-primary="<?php echo $this->primary_key ?>" data-after="edit" href="javascript:void(0);"><?php echo
$this->lang('save') ?></a>
    <?php } else
{ ?>
    <a class="xcrud-save-new xcrud-action-button" data-primary="<?php echo $this->primary_key ?>" data-after="create" href="javascript:void(0);"><?php echo
$this->lang('save_new') ?></a>
    <a class="xcrud-save-edit xcrud-action-button" data-primary="<?php echo $this->primary_key ?>" data-after="edit" href="javascript:void(0);"><?php echo
$this->lang('save_edit') ?></a>
    <?php } ?>
    <a class="xcrud-save-return xcrud-action-button" data-primary="<?php echo $this->primary_key ?>" data-after="list" href="javascript:void(0);"><?php echo
$this->lang('save_return') ?></a>
    <a class="xcrud-return xcrud-action-button" href="javascript:void(0);"><?php echo $this->lang('return') ?></a>
</div>
<div class="xcrud-view">
<?php 
echo $this->create_tabs($fields, $mode, 'ui');
if ($fields)
{ ?>
    <table class="xcrud-details">
        <?php     foreach ($fields as $item)
    {
        $primary_class = $this->primary == $item['name'] ? ' primary_details' : '';
        echo '<tr>';
        echo '<td class="xcrud-label' . $primary_class . '">' . $item['label'] . $this->get_field_tooltip($item['name'], $mode, $driver = 'ui').'</td>';
        echo '<td>' . $item['field'] . '</td>';
        echo '</tr>';
    } ?>
    </table>
<?php }
echo implode('', $hidden_fields); ?>
    <input type="hidden" class="xcrud-data" name="key" value="<?php echo $this->key ?>" />
    <input type="hidden" class="xcrud-data" name="order" value="<?php echo $order_column ?>" />
    <input type="hidden" class="xcrud-data" name="direct" value="<?php echo $order_direct ?>" />
    <input type="hidden" class="xcrud-data" name="start" value="<?php echo $start ?>" />
    <input type="hidden" class="xcrud-data" name="limit" value="<?php echo $limit ?>" />
    <input type="hidden" class="xcrud-data" name="instance" value="<?php echo $this->instance_name() ?>" />
    <input type="hidden" class="xcrud-data" name="column" value="<?php echo $column ?>" />
    <?php echo $this->render_search_hidden($column, $phrase)?>
    <input type="hidden" class="xcrud-data" name="task" value="list" />
    <?php if (Xcrud_config::$dynamic_session)
{ ?>
    <input type="hidden" class="xcrud-data" name="sess_name" value="<?php echo $this->sess_name ?>" />
    <?php } ?>
</div>
<div class="xcrud-nav">
    <span class="xcrud-benchmark"><?php echo $this->benchmark_end() ?></span>
</div>
<div class="xcrud-overlay"></div>
</div>