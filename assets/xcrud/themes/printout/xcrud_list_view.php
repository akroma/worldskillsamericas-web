<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $this->table_name?></title>
    <style type="text/css">
        h1{
            font-size: 22px;
        }
        table{
            border-spacing: 0;
            margin: 0;
            border-collapse: collapse;
            min-width:100%;
            table-layout:fixed;
            font-size: 12px;
            line-height: 1.5;
            border: 1px solid #eee;
        }
        table td,table th{
            border: 1px solid #eee;
            padding: 2px;
        }
        table th{
            background: #f5f5f5;
        }
    </style>
</head>
<body>
    <input type="hidden" class="xcrud-data" name="key" value="<?php echo $this->key?>" />
    <h1 class="xcrud-header">
        <?php echo $this->table_name?>
    </h1>
        <div class="xcrud-list-container">
        <table class="xcrud-list">
            <thead>
            <tr class="xcrud-th">
                <th class="xcrud-num">&#35;</th>
                <?php
                foreach($this->columns_names as $field=>$title){
                    if(isset($this->field_type[$field]) && $this->field_type[$field] == 'password') continue;
                    echo '<th';
                    $primary = $this->primary == $field ? ' primary' : '';
                    echo ($order_column == $field) ? ' class="xcrud-column xcrud-current '.$order_direct.$primary.'"' : ' class="xcrud-column'.$primary.'"';
                    echo ' data-order-dir="' . ($order_column == $field ? ($order_direct=='asc' ? 'desc' : 'asc') : $order_direct).'"';
                    echo ' data-column="'.$field.'"';
                    echo '><strong>';
                    echo /*($order_column == $field ? ($order_direct=='asc' ? '&uarr; ' : '&darr; ') : '') .*/ $title;
                    echo '</strong></th>';
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php $i=0;
            if($list){
            foreach($list as $key=>$row){
                echo '<tr class="xcrud-row-'.$i.'">';
                echo '<td class="xcrud-num">'.($key+1).'</td>';
                foreach($row as $field=>$value){
                    if($field == 'primary_key' or (isset($this->field_type[$field]) && $this->field_type[$field] == 'password')) continue;
                    echo '<td';
                    if($order_column == $field) echo ' class="xcrud-current"';
                    echo '>';
                    echo $this->_render_list_item($field, $value, $row['primary_key'], $row);
                    echo '</td>';
                }
                echo '</tr>';
                $i = 1-$i;
            }
            }else{
                echo '<tr class="xcrud-row-'.$i.'"><td colspan="'.(count($this->columns_names)+2).'">Entries not found.</td></tr>';
            }
            ?>
            </tbody>
            <?php
            if($this->sum){ ?>
            <tfoot>
            <tr class="xcrud-th">
                <td class="xcrud-num xcrud-sum">&Sigma;</td>
                <?php
                foreach($this->columns_names as $field=>$title){
                    if(isset($this->field_type[$field]) && $this->field_type[$field] == 'password') continue;
                    echo '<td';
                    $primary = $this->primary == $field ? ' primary' : '';
                    echo ($order_column == $field) ? ' class="xcrud-current xcrud-sum"' : ' class="xcrud-sum"';
                    echo '><strong>';
                    echo (isset($this->sum_row[$field]) ? $this->_render_list_item($field, $this->sum_row[$field], 0) : '&nbsp;');
                    echo '</strong></td>';
                }
                
                ?>
            </tr>
            </tfoot>
            <?php }
            ?>
        </table>
        </div>
</body>
</html>