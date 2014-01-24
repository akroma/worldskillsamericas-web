<?php
include ('xcrud.php');
if (isset($_GET['instance']) && !empty($_GET['instance']))
{
    $xcrud = Xcrud::get_instance($_GET['instance']);
    $xcrud->_image_check();
    $xcrud->render_image();
} else
    exit('Restricted.');