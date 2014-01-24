<?php if(!$this->is_ajax_request){ echo $this->load_css(); ?>
<div class="xcrud">
    <?php if($this->is_title){ ?>
    <h2 class="xcrud-header">
        <a class="xcrud-toggle<?php echo ($this->start_minimized)? ' closed' : ''?>" href="javascript:void(0);"></a>
        <?php echo $this->table_name?>
        <?php echo $this->get_table_tooltip('ui'); ?>
    </h2>
    <?php } ?>
    <div class="xcrud-container"<?php echo ($this->start_minimized)? ' style="display:none;"' : ''?>>
<?php } ?>
    <?php if($this->is_inner && $this->is_title){ ?>
    <h3 class="xcrud-inner-header">
        <?php echo $this->table_name?>
    </h3>
    <?php } ?>
        <div class="xcrud-ajax">
        <?php if($this->is_add or $this->is_csv or $this->is_print){ 
            if($this->is_csv){ ?>
            <form class="xcrud-csv-export" method="post" target="_blank" action="<?php echo $this->crud_url?>/xcrud_csv.php" accept-charset="<?php echo Xcrud_config::$dbencoding?>">
            <?php } ?>
        <div class="xcrud-top-actions">
            <?php if($this->is_csv){ ?>
                <button type="submit" class="xcrud-action-button xcrud-csv" ><?php echo $this->lang('export_csv')?></button>
            <?php }
            if($this->is_print){ ?>
                <a class="xcrud-print xcrud-action-button" href="javascript:void(0);" onclick="jQuery.print_window(window.open('', '<?php echo $this->lang('print')?>','height=460,width=760'),jQuery(this).closest('.xcrud-ajax'));"><?php echo $this->lang('print')?></a>
            <?php }
            if($this->is_add){ ?>
            <a class="xcrud-add xcrud-action-button" href="javascript:void(0);"><?php echo $this->lang('add')?></a>
            <?php } 
            if(Xcrud_config::$top_pagination){ echo $this->get_limit_list($limit, 'xcrud-limit-list'); echo $pagination; } ?>
        </div>
        <?php } ?>
        <div class="xcrud-list-container">
        <table class="xcrud-list">
            <thead>
            <tr class="xcrud-th">
                <?php if($this->is_numbers){ ?>
                <th class="xcrud-num">&#35;</th>
                <?php }
                foreach($this->columns_names as $field => $fieldname){
                    if(!$this->check_row_visibility($field)) continue;
                    echo '<th';
                    $primary = $this->primary == $field ? ' primary' : '';
                    echo ($order_column == $field) ? ' class="xcrud-column xcrud-current '.$order_direct.$primary.'"' : ' class="xcrud-column'.$primary.'"';
                    echo ' data-order-dir="' . ($order_column == $field ? ($order_direct=='asc' ? 'desc' : 'asc') : $order_direct).'"';
                    echo ' data-column="'.$field.'"';
                    echo '>';
                    echo ($order_column == $field ? ($order_direct=='asc' ? '&uarr; ' : '&darr; ') : '') . $fieldname;
                    echo '</th>';
                }
                if($this->is_edit || $this->is_remove || $this->is_view || $this->buttons)
                    echo '<th class="xcrud-actions">&nbsp;</th>';
                ?>
            </tr>
            </thead>
            <tbody>
            <?php $i=0;
            if($list){
            foreach($list as $key=>$row){
                echo '<tr class="xcrud-row-'.$i.'">';
                if($this->is_numbers) echo '<td class="xcrud-num">'.($key+$start+1).'</td>';
                foreach($row as $field=>$value){
                    if(!$this->check_row_visibility($field)) continue;
                    echo '<td' . $this->_cell_attrib($field, $value, $order_column, false);
                    //if($order_column == $field) echo ' class="xcrud-current"';
                    echo '>';
                    echo $this->_render_list_item($field, $value, $row['primary_key'], $row);
                    echo '</td>';
                }
                if($this->is_edit || $this->is_remove || $this->is_view || $this->buttons){
                    echo '<td class="xcrud-actions'.(Xcrud_config::$fixed_action_buttons?' xcrud-fix':'').'">';
                    echo $this->_render_list_buttons($row);
                    echo '</td>';
                }
                echo '</tr>';
                $i = 1-$i;
            }
            }else{
                echo '<tr class="xcrud-row-'.$i.'"><td colspan="'.(count($this->columns_names)+2).'">'.$this->lang('table_empty').'</td></tr>';
            }
            ?>
            </tbody>
            <?php
            if($this->sum && $list){ ?>
            <tfoot>
            <tr class="xcrud-th">
                <?php if($this->is_edit || $this->is_remove || $this->is_view || $this->buttons)
                    echo '<td class="xcrud-sum">&nbsp;</td>';
                foreach($this->columns_names as $field=>$title){
                    if(isset($this->field_type[$field]) && $this->field_type[$field] == 'password') continue;
                    echo '<td' . $this->_cell_attrib($field, $value, $order_column, true);
                    echo '>';
                    echo $this->render_sum_item($field);
                    echo '</td>';
                }
                
                ?>
            </tr>
            </tfoot>
            <?php }
            ?>
        </table>
        </div>
        <div class="xcrud-nav">
        <?php if ($this->is_pagination){ 
            echo $this->get_limit_list($limit, 'xcrud-limit-list');
            echo $pagination; } ?>
            <?php if($this->is_search){ echo $this->render_search($column, $phrase); } ?>
            <span class="xcrud-benchmark"><?php echo $this->benchmark_end()?></span>
            <input type="hidden" class="xcrud-data" name="key" value="<?php echo $this->key?>" />
            <input type="hidden" class="xcrud-data" name="order" value="<?php echo $order_column?>" />
            <input type="hidden" class="xcrud-data" name="direct" value="<?php echo $order_direct?>" />
            <input type="hidden" class="xcrud-data" name="start" value="<?php echo $start?>" />
            <input type="hidden" class="xcrud-data" name="limit" value="<?php echo $limit?>" />
            <input type="hidden" class="xcrud-data" name="instance" value="<?php echo $this->instance_name()?>" />
            <input type="hidden" class="xcrud-data" name="task" value="list" />
            <?php if(Xcrud_config::$dynamic_session) { ?>
            <input type="hidden" class="xcrud-data" name="sess_name" value="<?php echo $this->sess_name?>" />
            <?php } ?>
        </div>
        
        <?php if($this->is_csv){ ?>
            </form>
        <?php } ?>
        
        <div class="xcrud-overlay"></div>
        </div>
<?php if(!$this->is_ajax_request){ ?>
    </div>
</div>
<?php echo $this->load_js(); } ?>