<?php
include ('xcrud.php');
if (isset($_POST['instance']) && !empty($_POST['instance']))
{
    $xcrud = Xcrud::get_instance($_POST['instance']);
    $xcrud->_ajax_check();
    echo $xcrud->render();
} else
    exit('Restricted.');