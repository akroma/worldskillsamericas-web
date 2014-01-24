<?php /** f0ska xCRUD v.1.5.7.3 05/2013 */
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
class Xcrud
{
    private $demo_mode = false;
    protected static $_instance = array();
    protected static $head_loaded = false; // external head loading flag
    protected $_instance_name = 'xcrud';
    protected $instance_count;
    protected $table;
    protected $table_name;
    protected $primary; // name of primary
    protected $primary_key; // value of a primary
    protected $where = array();
    protected $order_by = array();
    protected $join = array();
    protected $fields = array();
    protected $fields_names = array();
    protected $labels = array();
    protected $columns = array();
    protected $columns_names = array();
    protected $is_add = true;
    protected $is_edit = true;
    protected $is_view = true;
    protected $is_remove = true;
    protected $is_csv = true;
    protected $is_search = true;
    protected $is_print = true;
    protected $is_title = true;
    protected $is_numbers = true;
    protected $buttons = array();
    protected $readonly = array();
    protected $disabled = array();
    protected $readonly_on_create = array();
    protected $disabled_on_create = array();
    protected $readonly_on_edit = array();
    protected $disabled_on_edit = array();
    protected $validation_required = array();
    protected $validation_pattern = array();
    protected $before_insert = array();
    protected $before_update = array();
    protected $before_remove = array();
    protected $before_view = array();
    protected $after_insert = array();
    protected $after_update = array();
    protected $after_remove = array();
    protected $field_type = array();
    protected $field_maxsize = array();
    protected $defaults = array();
    protected $limit = 20;
    protected $limit_list = array(
        '20',
        '50',
        '100',
        'all');
    protected $column_cut = 50;
    protected $no_editor = array();
    protected $show_primary_ai_field = false;
    protected $show_primary_ai_column = false;
    protected $disable_jquery = false;
    protected $disable_jquery_ui = false;
    protected $disable_plugins = false;
    protected $crud_url;
    protected $url;
    protected $self_path;
    protected $is_ajax_request;
    protected $key;
    protected $jquery_no_conflict = false;
    protected $sess_expire = 30;
    protected $tinymce_folder_url = '';
    protected $tinymce_init_url = '';
    protected $force_tinymce = false;
    protected $benchmark = false;
    protected $search_pattern = array('%', '%');
    protected $connection = false;
    protected $start_minimized = false;
    protected $remove_confirm = false;
    //v.1.1
    protected $upload_folder = array();
    protected $upload_config = array();
    protected $upload_folder_def = '../uploads';
    protected $upload_to_save = array();
    protected $upload_to_remove = array();
    protected $binary = array();
    // v.1.2
    protected $pass_var_create = array();
    protected $pass_var_edit = array();
    // v.1.3
    protected $reverse_columns = array();
    protected $reverse_fields = array();
    // v.1.4
    protected $no_quotes = array();
    protected $table_join = array();
    protected $inner_where = array();
    protected $inner_table_instance = array();
    protected $inner_instances = array();
    protected $condition = array();
    protected $theme = 'default';
    protected $unique = array();
    protected $fk_join = array();
    protected $is_duplicate = false;
    protected $links_label = array();
    protected $emails_label = array();
    protected $sum = array();
    protected $alert_create;
    protected $alert_edit;
    //1.5
    protected $subselect = array();
    protected $subselect_before = array();
    protected $is_pagination = true;
    protected $highlight = array();
    protected $modal = array();
    protected $column_class = array();
    protected $no_select = array(); // only subselect flag for correct sorting
    protected $primary_ai = false;
    protected $is_inner = false;
    protected $language = 'en';
    protected $lang_arr = array();
    protected $hidden_fields = array(); // allows save data in non in form fields
    protected $subselect_query = array();
    protected $where_pri = array();
    protected $field_params = array();
    protected $mass_alert_create = array();
    protected $mass_alert_edit = array();
    protected $column_callback = array();
    protected $field_callback = array();
    protected $replace_insert = array();
    protected $replace_update = array();
    protected $replace_remove = array();
    protected $send_external_create = array();
    protected $send_external_edit = array();
    protected $locked_fields = array(); // disallow save data in form fields
    protected $column_pattern = array();
    protected $field_tabs = array();
    protected $field_marker = array();
    protected $field_tooltip = array();
    protected $table_tooltip = array();
    protected $search_columns = array();
    protected $search_default = null;

    protected function __construct()
    {
        $this->self_path = dirname(__file__);
        require_once ($this->self_path . '/xcrud_config.php');
        require_once ($this->self_path . '/xcrud_db.php');
        $this->sess_expire = Xcrud_config::$sess_expire;
        $this->sess_name = Xcrud_config::$sess_name;
        if (!session_id())
        {
            if (!headers_sent())
            {
                if (Xcrud_config::$dynamic_session && ($this->_post('sess_name') or $this->_get('sess_name')))
                {
                    $this->sess_name = $this->_post('sess_name') ? $this->_post('sess_name') : $this->_get('sess_name');
                }
                session_name($this->sess_name);
                session_cache_expire($this->sess_expire);
                session_set_cookie_params(0, '/');
                session_start();
            } else
                $this->error('xCRUD can not create session, because the output is already sent into browser. 
                Try to define xCRUD instance before the output start or use session_start() at the beginning of your script');
        } elseif (Xcrud_config::$dynamic_session)
        {
            $this->sess_name = session_name();
        }
        $this->is_ajax_request = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ==
            'xmlhttprequest') ? true : false;
        $this->crud_url = $this->_check_url(Xcrud_config::$scripts_url);
        $this->limit = Xcrud_config::$limit;
        $this->limit_list = Xcrud_config::$limit_list;
        $this->column_cut = Xcrud_config::$column_cut;
        $this->show_primary_ai_field = Xcrud_config::$show_primary_ai_field;
        $this->show_primary_ai_column = Xcrud_config::$show_primary_ai_column;
        $this->disable_plugins = Xcrud_config::$disable_plugins;
        $this->disable_jquery = Xcrud_config::$disable_jquery;
        $this->disable_jquery_ui = Xcrud_config::$disable_jquery_ui;
        $this->jquery_no_conflict = Xcrud_config::$jquery_no_conflict;
        $this->tinymce_folder_url = $this->_check_url(Xcrud_config::$tinymce_folder_url);
        $this->tinymce_init_url = $this->_check_url(Xcrud_config::$tinymce_init_url);
        $this->force_tinymce = Xcrud_config::$force_tinymce;
        $this->benchmark = Xcrud_config::$benchmark;
        $this->start_minimized = Xcrud_config::$start_minimized;
        $this->remove_confirm = Xcrud_config::$remove_confirm;
        $this->upload_folder_def = Xcrud_config::$upload_folder_def;
        $this->theme = Xcrud_config::$theme;
        $this->is_print = Xcrud_config::$enable_printout;
        $this->is_title = Xcrud_config::$enable_table_title;
        $this->is_csv = Xcrud_config::$enable_csv_export;
        $this->is_numbers = Xcrud_config::$enable_numbers;
        $this->is_pagination = Xcrud_config::$enable_pagination;
        $this->is_search = Xcrud_config::$enable_search;
        $this->language = Xcrud_config::$language;
    }
    protected function __clone()
    {
    }
    public static function get_instance($name = false)
    {
        if (!$name)
            $name = sha1(rand(0, 99999) . microtime());
        if (!isset(self::$_instance[$name]) || null === self::$_instance[$name])
        {
            self::$_instance[$name] = new self();
            self::$_instance[$name]->_instance_name = $name;
        }
        self::$_instance[$name]->instance_count = count(self::$_instance);
        return self::$_instance[$name];
    }
    public function connection($user = '', $pass = '', $table = '', $host = 'localhost', $encode = 'utf8')
    {
        if ($user && $pass && $table)
        {
            $this->connection = array(
                $user,
                $pass,
                $table,
                $host,
                $encode);
        }
        return $this;
    }
    public function start_minimized($bool = true)
    {
        $this->start_minimized = (bool)$bool;
        return $this;
    }
    public function remove_confirm($bool = true)
    {
        $this->remove_confirm = (bool)$bool;
        return $this;
    }
    public function disable_jquery($bool = true)
    {
        $this->disable_jquery = (bool)$bool;
        return $this;
    }
    public function disable_jquery_ui($bool = true)
    {
        $this->disable_jquery_ui = (bool)$bool;
        return $this;
    }
    public function scripts_url($url)
    {
        $this->crud_url = $this->_check_url($url);
        return $this;
    }
    public function jquery_no_conflict($bool = true)
    {
        $this->jquery_no_conflict = (bool)$bool;
        return $this;
    }
    public function theme($theme = 'default')
    {
        $this->theme = $theme;
    }
    public function limit($limit = 20)
    {
        $this->limit = $limit;
        return $this;
    }
    public function limit_list($limit_list = array(
        '20',
        '50',
        '100',
        'all'))
    {
        if ($limit_list)
        {
            if (is_array($limit_list))
                $this->limit_list = array_unique($limit_list);
            else
            {
                $this->limit_list = array_unique(explode(',', str_replace(' ', '', $limit_list)));
            }
        }
        return $this;
    }
    public function sess_expire($minutes = 30)
    {
        if ((int)$minutes)
            $this->sess_expire = (int)$minutes;
        return $this;
    }
    public function show_primary_ai_field($bool = true)
    {
        $this->show_primary_ai_field = (bool)$bool;
        return $this;
    }
    public function show_primary_ai_column($bool = true)
    {
        $this->show_primary_ai_column = (bool)$bool;
        return $this;
    }
    public function tinymce_folder_url($url = '')
    {
        $this->tinymce_folder_url = $this->_check_url($url);
        $this->force_tinymce = true;
        return $this;
    }
    public function tinymce_init_url($url = '')
    {
        $this->tinymce_init_url = $this->_check_url($url);
        $this->force_tinymce = false;
        return $this;
    }
    public function force_tinymce($bool = true)
    {
        $this->force_tinymce = (bool)$bool;
        return $this;
    }
    public function table($table = '')
    {
        $this->table = $table;
        return $this;
    }
    public function table_name($name = '', $tooltip = false, $icon = false)
    {
        if ($name)
            $this->table_name = $name;
        if ($tooltip)
        {
            $this->table_tooltip = array('tooltip' => $this->html_safe($tooltip), 'icon' => $icon);
        }
        return $this;
    }
    public function where($fields = '', $where_val = '', $table = false, $glue = 'AND', $index = false)
    {

        if ($fields)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'where');
            foreach ($fdata as $fitem)
            {
                if ($index)
                    $this->where[$index] = array(
                        'table' => $fitem['table'],
                        'field' => $fitem['field'],
                        'value' => isset($fitem['value']) ? $fitem['value'] : $where_val,
                        'glue' => $glue);
                else
                    $this->where[] = array(
                        'table' => $fitem['table'],
                        'field' => $fitem['field'],
                        'value' => isset($fitem['value']) ? $fitem['value'] : $where_val,
                        'glue' => $glue);
            }
            unset($fields, $fdata);
        } elseif ($where_val)
        {
            if ($index)
                $this->where[$index] = array('custom' => $where_val, 'glue' => $glue);
            else
                $this->where[] = array('custom' => $where_val, 'glue' => $glue);
            unset($where_val);
        }
        return $this;
    }
    public function or_where($fields = '', $where_val = '', $table = false)
    {
        return $this->where($fields = '', $where_val = '', $table = false, 'OR');
    }
    public function order_by($fields = '', $direction = 'asc', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'order_by');
        foreach ($fdata as $fitem)
        {
            $this->order_by[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $direction;
        }
        unset($fields);
        return $this;
    }
    public function relation($fields = '', $rel_tbl = '', $rel_field = '', $rel_name = '', $rel_where = array(), $table = false,
        $multi = false, $rel_concat_separator = ' ', $tree = false, $depend_field = '', $depend_on = '')
    {
        if ($fields && $rel_tbl && $rel_field && $rel_name)
        {
            if ($depend_on)
            {
                $fdata = $this->_parse_field_names($depend_on, $table, 'relation');
                $depend_on = $fdata[0]['table'] . '.' . $fdata[0]['field'];
            }
            $fdata = $this->_parse_field_names($fields, $table, 'relation');
            foreach ($fdata as $fitem)
            {
                $this->join[$fitem['table'] . '.' . $fitem['field']] = array(
                    'rel_tbl' => $rel_tbl,
                    'rel_alias' => 'alias' . rand(1111, 9999),
                    'rel_field' => $rel_field,
                    'rel_name' => $rel_name,
                    'rel_where' => $rel_where,
                    'rel_separator' => $rel_concat_separator,
                    'multi' => $multi,
                    'table' => $fitem['table'],
                    'field' => $fitem['field'],
                    'tree' => $tree,
                    'depend_field' => $depend_field,
                    'depend_on' => $depend_on);
            }
        }
        return $this;
    }
    public function fk_relation($field = '', $rel_tbl = '', $rel_field = '', $rel_name = '', $rel_where = array(), $fk_table =
        '', $in_fk_fiend = '', $out_fk_field = '', $table = false)
    {
        $this->fk_join[] = array(
            'table' => $table,
            'field' => $field,
            'rel_tbl' => $rel_tbl,
            'rel_alias' => 'alias' . rand(111, 999),
            'fk_alias' => 'alias' . rand(111, 999),
            'rel_field' => $rel_field,
            'rel_name' => $rel_name,
            'rel_where' => $rel_where,
            'fk_table' => $fk_table,
            'in_fk_fiend,' => $in_fk_fiend,
            'out_fk_field' => $out_fk_field);
        return $this;
    }
    public function join($fields = '', $join_tbl = '', $join_field = '', $alias = false, $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'join');
        $alias = $alias ? $alias : $join_tbl;
        $this->table_join[$alias] = array(
            'table' => $fdata[0]['table'],
            'field' => $fdata[0]['field'],
            'join_table' => $join_tbl,
            'join_field' => $join_field);
        $this->field_type[$this->table_join[$alias]['join_table'] . '.' . $this->table_join[$alias]['join_field']] = 'hidden';
        return $this;
    }
    public function nested_table($instance_name = '', $field = '', $inner_tbl = '', $tbl_field = '', $table = false)
    {
        if ($instance_name && $field && $inner_tbl && $tbl_field)
        {
            $table = $table ? $table : $this->_get_table('fields');
            if (strpos($field, '.'))
            {
                $tmp = explode('.', $field);
                $table = $tmp[0];
                $field = $tmp[1];
                unset($tmp);
            }
            $this->inner_table_instance[$table . '.' . $field] = $instance_name;
            $this->inner_instances[$instance_name] = Xcrud::get_instance($instance_name);
            $this->inner_instances[$instance_name]->table($inner_tbl);
            $this->inner_instances[$instance_name]->is_inner = true;
            $this->inner_where[$table . '.' . $field] = $inner_tbl . '.' . $tbl_field;
            return $this->inner_instances[$instance_name];
        }
    }
    public function primary($key = '')
    {
        if ($key)
        {
            $this->primary = $key;
        }
        return $this;
    }
    public function fields($fields = '', $reverse = false, $tabname = false, $mode = false, $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'fields');
        $act = false;
        switch ($mode)
        {
            case 'create':
                $act = 'create';
                break;
            case 'edit':
                $act = 'edit';
                break;
            case 'view':
                $act = 'view';
                break;
            default:
                $act = false;
                break;
        }
        if ($act)
            $this->field_marker[$act] = true;
        foreach ($fdata as $fitem)
        {
            if (!isset($this->fields[$fitem['table'] . '.' . $fitem['field']]))
            {
                if ($act)
                    $fitem[$act] = true;
                $this->fields[$fitem['table'] . '.' . $fitem['field']] = $fitem;
            } else
            {
                if ($act)
                    $this->fields[$fitem['table'] . '.' . $fitem['field']][$act] = true;
            }

            if (!$reverse && $tabname)
            {
                if ($act)
                    $this->field_tabs[$act][$fitem['table'] . '.' . $fitem['field']] = $tabname;
                else
                    $this->field_tabs['all'][$fitem['table'] . '.' . $fitem['field']] = $tabname;
            }
        }
        $this->reverse_fields = $reverse;
        unset($fields);
        return $this;
    }
    public function unique($fields = '', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'unique');
        foreach ($fdata as $fitem)
        {
            $this->unique[$fitem['table'] . '.' . $fitem['field']] = $fitem;
        }
        unset($fields);
        return $this;
    }
    public function label($fields = '', $label = '', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'label');
        foreach ($fdata as $fitem)
        {
            $this->labels[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $label;
        }
        return $this;
    }
    public function columns($columns = '', $reverse = false, $table = false)
    {
        $fdata = $this->_parse_field_names($columns, $table, 'columns');
        foreach ($fdata as $fitem)
        {
            $this->columns[$fitem['table'] . '.' . $fitem['field']] = array('table' => $fitem['table'], 'column' => $fitem['field']);
        }
        $this->reverse_columns = $reverse;
        unset($columns);
        return $this;
    }
    public function unset_add($bool = true)
    {
        $this->is_add = !(bool)$bool;
        return $this;
    }
    public function unset_edit($bool = true)
    {
        $this->is_edit = !(bool)$bool;
        return $this;
    }
    public function unset_view($bool = true)
    {
        $this->is_view = !(bool)$bool;
        return $this;
    }
    public function unset_remove($bool = true)
    {
        $this->is_remove = !(bool)$bool;
        return $this;
    }
    public function unset_csv($bool = true)
    {
        $this->is_csv = !(bool)$bool;
        return $this;
    }
    public function duplicate_button($bool = true)
    {
        $this->is_duplicate = (bool)$bool;
        return $this;
    }
    public function unset_search($bool = true)
    {
        $this->is_search = !(bool)$bool;
        return $this;
    }
    public function unset_print($bool = true)
    {
        $this->is_print = !(bool)$bool;
        return $this;
    }
    public function unset_pagination($bool = true)
    {
        $this->is_pagination = !(bool)$bool;
        return $this;
    }
    public function unset_title($bool = true)
    {
        $this->is_title = !(bool)$bool;
        return $this;
    }
    public function unset_numbers($bool = true)
    {
        $this->is_numbers = !(bool)$bool;
        return $this;
    }

    public function button($link = '', $name = '', $icon = '', $class = '', $parameter_name = '', $parameter_field = '', $table = false)
    {
        if ($link)
        {
            $table = $table ? $table : $this->_get_table('button');
            $parameters = array();
            if ($parameter_name)
            {
                if (!is_array($parameter_name) && $parameter_name)
                {
                    if (!strpos($parameter_field, '.'))
                    {
                        $parameters[$table . '.' . $parameter_field] = array(
                            'table' => $table,
                            'column' => $parameter_field,
                            'name' => $parameter_name);
                    } else
                    {
                        $tmp = explode('.', $parameter_field);
                        $parameters[$tmp[0] . '.' . $tmp[1]] = array(
                            'table' => $tmp[0],
                            'column' => $tmp[1],
                            'name' => $parameter_name);
                    }
                } else
                {
                    foreach ($parameter_name as $key => $val)
                    {
                        if (!strpos($val, '.'))
                        {
                            $parameters[$table . '.' . $val] = array(
                                'table' => $table,
                                'column' => $val,
                                'name' => $key);
                        } else
                        {
                            $tmp = explode('.', $val);
                            $parameters[$tmp[0] . '.' . $tmp[1]] = array(
                                'table' => $tmp[0],
                                'column' => $tmp[1],
                                'name' => $key);
                        }
                    }
                }
            }
            $this->buttons[] = array(
                'link' => $link,
                'name' => $name,
                'icon' => $icon,
                'class' => $class,
                'params' => $parameters);
        }
        return $this;
    }
    public function change_type($fields = '', $type = '', $default = '', $max = '', $table = false)
    {
        if ($type)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'change_type');
            foreach ($fdata as $fitem)
            {

                switch ($type)
                {
                    case 'file':
                    case 'image':
                        $this->defaults[$fitem['table'] . '.' . $fitem['field']] = '';
                        if ($default)
                            $this->upload_folder[$fitem['table'] . '.' . $fitem['field']] = trim($default, '/');
                        else
                            $this->upload_folder[$fitem['table'] . '.' . $fitem['field']] = trim($this->upload_folder_def, '/');
                        if ($max)
                            $this->upload_config[$fitem['table'] . '.' . $fitem['field']] = $max;
                        break;
                    case 'price':
                        $this->field_maxsize[$fitem['table'] . '.' . $fitem['field']] = array(
                            'max' => (isset($max['max']) ? $max['max'] : 10),
                            'decimals' => (isset($max['decimals']) ? $max['decimals'] : 2),
                            'separator' => (isset($max['separator']) ? $max['separator'] : ','),
                            'prefix' => (isset($max['prefix']) ? $max['prefix'] : ''),
                            'suffix' => (isset($max['suffix']) ? $max['suffix'] : ''));
                        $this->defaults[$fitem['table'] . '.' . $fitem['field']] = $default;
                        break;
                    case 'location':
                        // $xcrud->change_type('lng:lat','googlemap','39.909736,-6.679687', array('text'=>'Your position','zoom'=>3,'width'=>500,'height'=>300));
                        list($lat, $lng) = explode(':', $fitem['field'], 2);
                        if ($default && strpos($default, ','))
                        {
                            list($lat_def, $lng_def) = explode(',', $default, 2);
                            $this->defaults[$fitem['table'] . '.' . $lng] = trim($lng_def);
                            $this->defaults[$fitem['table'] . '.' . $lat] = trim($lat_def);
                        }
                        $this->field_params['googlemap'] = array(
                            'text' => (isset($max['text']) ? $max['text'] : 'Your position'),
                            'zoom' => (isset($max['zoom']) ? $max['zoom'] : 3),
                            'width' => (isset($max['width']) ? $max['width'] : 500),
                            'height' => (isset($max['height']) ? $max['height'] : 300),
                            'hide_fields' => (isset($max['hide_fields']) ? $max['hide_fields'] : false),
                            'lat_field' => $fitem['table'] . '.' . $lat,
                            'lng_field' => $fitem['table'] . '.' . $lng,
                            'label' => (isset($max['label']) ? $max['label'] : ''));
                        if (!$this->field_params['googlemap']['hide_fields'])
                        {
                            $this->field_type[$fitem['table'] . '.' . $lng] = 'float';
                            $this->field_type[$fitem['table'] . '.' . $lat] = 'float';
                        } else
                        {
                            $this->field_type[$fitem['table'] . '.' . $lng] = 'hidden';
                            $this->field_type[$fitem['table'] . '.' . $lat] = 'hidden';
                        }
                        $this->field_maxsize[$fitem['table'] . '.' . $lat] = $this->field_maxsize[$fitem['table'] . '.' . $lng] = 12;
                        break;
                    default:
                        $this->defaults[$fitem['table'] . '.' . $fitem['field']] = $default;
                        $this->field_maxsize[$fitem['table'] . '.' . $fitem['field']] = $max;
                        break;
                }
                $this->field_type[$fitem['table'] . '.' . $fitem['field']] = $type;
            }
        }
        return $this;
    }
    public function pass_default($fields = '', $value = '', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'pass_default');
        foreach ($fdata as $fitem)
        {
            $this->defaults[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $value;
        }
        return $this;
    }
    public function pass_var($fields = '', $value = '', $type = 'all', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'pass_var');
        foreach ($fdata as $fitem)
        {
            if ($type != 'edit')
                $this->pass_var_create[$fitem['table'] . '.' . $fitem['field']] = array(
                    'table' => $fitem['table'],
                    'field' => $fitem['field'],
                    'value' => isset($fitem['value']) ? $fitem['value'] : $value);
            if ($type != 'create')
                $this->pass_var_edit[$fitem['table'] . '.' . $fitem['field']] = array(
                    'table' => $fitem['table'],
                    'field' => $fitem['field'],
                    'value' => isset($fitem['value']) ? $fitem['value'] : $value);
        }
        return $this;
    }
    public function no_quotes($fields = '', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'no_quotes');
        foreach ($fdata as $fitem)
        {
            $this->no_quotes[$fitem['table'] . '.' . $fitem['field']] = true;
        }
        return $this;
    }
    public function sum($fields = '', $class = '', $custom_text = '', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'sum');
        foreach ($fdata as $fitem)
        {
            $this->sum[$fitem['table'] . '.' . $fitem['field']] = array(
                'table' => $fitem['table'],
                'column' => $fitem['field'],
                'class' => isset($fitem['value']) ? $fitem['value'] : $class,
                'custom' => $custom_text);
        }
        return $this;
    }
    public function readonly_on_create($field = '', $table = false)
    {
        return $this->readonly($field, 'create', $table);
    }
    public function disabled_on_create($field = '', $table = false)
    {
        return $this->disabled($field, 'create', $table);
    }
    public function readonly_on_edit($field = '', $table = false)
    {
        return $this->readonly($field, 'edit', $table);
    }
    public function disabled_on_edit($field = '', $table = false)
    {
        return $this->disabled($field, 'edit', $table);
    }
    public function readonly($fields = '', $mode = 'all', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'readonly');
        foreach ($fdata as $fitem)
        {
            switch ($mode)
            {
                case 'create':
                    $this->readonly_on_create[$fitem['table'] . '.' . $fitem['field']] = true;
                    break;
                case 'edit':
                    $this->readonly_on_edit[$fitem['table'] . '.' . $fitem['field']] = true;
                    break;
                default:
                    $this->readonly_on_create[$fitem['table'] . '.' . $fitem['field']] = true;
                    $this->readonly_on_edit[$fitem['table'] . '.' . $fitem['field']] = true;
                    break;
            }
        }
        return $this;
    }
    public function disabled($fields = '', $mode = 'all', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'disabled');
        foreach ($fdata as $fitem)
        {
            switch ($mode)
            {
                case 'create':
                    $this->disabled_on_create[$fitem['table'] . '.' . $fitem['field']] = true;
                    break;
                case 'edit':
                    $this->disabled_on_edit[$fitem['table'] . '.' . $fitem['field']] = true;
                    break;
                default:
                    $this->disabled_on_create[$fitem['table'] . '.' . $fitem['field']] = true;
                    $this->disabled_on_edit[$fitem['table'] . '.' . $fitem['field']] = true;
                    break;
            }
        }
        return $this;
    }
    public function condition($fields = '', $operator = '', $value = '', $method = '', $param = '', $table = false)
    {
        if ($fields && $method && $operator)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'condition');
            foreach ($fdata as $fitem)
            {
                $this->condition[$fitem['table'] . '.' . $fitem['field']] = array(
                    'value' => $value,
                    'operator' => $operator,
                    'method' => $method,
                    'param' => $param);
            }
        }
        return $this;
    }
    public function instance_name()
    {
        return $this->_instance_name;
    }
    public function benchmark($bool = true)
    {
        $this->benchmark = (bool)$bool;
        return $this;
    }
    public function column_cut($int = 50)
    {
        $this->column_cut = (int)$int ? (int)$int : 50;
        return $this;
    }
    public function links_label($text = '')
    {
        if ($text)
        {
            $this->links_label['text'] = trim($text);
        }
        return $this;
    }
    public function emails_label($text = '')
    {
        if ($text)
        {
            $this->emails_label['text'] = trim($text);
        }
        return $this;
    }

    public function no_editor($fields = '', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'no_editor');
        foreach ($fdata as $fitem)
        {
            $this->no_editor[$fitem['table'] . '.' . $fitem['field']] = true;
        }
        return $this;
    }
    public function validation_required($fields = '', $chars = 1, $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'validation_required');
        foreach ($fdata as $fitem)
        {
            $this->validation_required[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $chars;
        }
        return $this;
    }
    public function validation_pattern($fields = '', $pattern = '', $table = false)
    {
        $fdata = $this->_parse_field_names($fields, $table, 'validation_pattern');
        foreach ($fdata as $fitem)
        {
            $this->validation_pattern[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $pattern;
        }
        return $this;
    }
    public function alert($columns = '', $cc = '', $subject = '', $message = '', $link = false, $field = false, $value = false,
        $table = false, $mode = 'all')
    {
        $fdata = $this->_parse_field_names($columns, $table, 'alert');
        if ($field && !strpos($field, '.'))
            $field = $this->table . '.' . $field;

        foreach ($fdata as $fitem)
        {
            if ($cc)
            {
                if (!is_array($cc))
                    $cc = explode(',', str_replace(' ', '', $cc));
            }
            if ($mode == 'all' or $mode == 'create')
                $this->alert_create[] = array(
                    'column' => $fitem['table'] . '.' . $fitem['field'],
                    'cc' => $cc,
                    'subject' => $subject,
                    'message' => $message,
                    'link' => $link,
                    'field' => $field,
                    'value' => $value);
            if ($mode == 'all' or $mode == 'edit')
                $this->alert_edit[] = array(
                    'column' => $fitem['table'] . '.' . $fitem['field'],
                    'cc' => $cc,
                    'subject' => $subject,
                    'message' => $message,
                    'link' => $link,
                    'field' => $field,
                    'value' => $value);
        }
        return $this;
    }
    public function alert_create($column = '', $cc = '', $subject = '', $message = '', $link = false, $field = false, $value = false,
        $table = false)
    {
        return $this->alert($column, $cc, $subject, $message, $link, $field, $value, $table, 'create');
    }
    public function alert_edit($column = '', $cc = '', $subject = '', $message = '', $link = false, $field = false, $value = false,
        $table = false)
    {
        return $this->alert($column, $cc, $subject, $message, $link, $field, $value, $table, 'edit');
    }
    public function mass_alert($email_table = '', $email_column = '', $emeil_where = '', $subject = '', $message = '', $link = false,
        $field = false, $value = false, $table = false, $mode = 'all')
    {
        if (!$table)
            $table = $this->_get_table('mass_alert');
        $field = $this->table . '.' . $field;
        if ($mode == 'all' or $mode == 'create')
            $this->mass_alert_create[] = array(
                'email_table' => $email_table,
                'email_column' => $email_column,
                'where' => $emeil_where,
                'subject' => $subject,
                'message' => $message,
                'link' => $link,
                'field' => $field,
                'value' => $value,
                'table' => $table);
        if ($mode == 'all' or $mode == 'edit')
            $this->mass_alert_edit[] = array(
                'email_table' => $email_table,
                'email_column' => $email_column,
                'where' => $emeil_where,
                'subject' => $subject,
                'message' => $message,
                'link' => $link,
                'field' => $field,
                'value' => $value,
                'table' => $table);

        return $this;
    }
    public function mass_alert_create($email_table = '', $email_column = '', $emeil_where = '', $subject = '', $message = '',
        $link = false, $field = false, $value = false, $table = false)
    {
        return $this->mass_alert($email_table, $email_column, $emeil_where, $subject, $message, $link, $field, $value, $table,
            'create');
    }
    public function mass_alert_edit($email_table = '', $email_column = '', $emeil_where = '', $subject = '', $message = '',
        $link = false, $field = false, $value = false, $table = false)
    {
        return $this->mass_alert($email_table, $email_column, $emeil_where, $subject, $message, $link, $field, $value, $table,
            'edit');
    }
    public function send_external($path, $data = array(), $method = 'include', $mode = 'all', $where_field = '', $where_val =
        '')
    {
        if ($where_field)
        {
            $fdata = $this->_parse_field_names($where_field, false, 'send_external');
            $where_field = $fdata[0]['table'] . '.' . $fdata[0]['field'];
        }
        if ($mode == 'all' or $mode == 'create')
            $this->send_external_create = array(
                'data' => $data,
                'path' => $path,
                'method' => $method,
                'where_field' => $where_field,
                'where_val' => $where_val);
        if ($mode == 'all' or $mode == 'edit')
            $this->send_external_edit = array(
                'data' => $data,
                'path' => $path,
                'method' => $method,
                'where_field' => $where_field,
                'where_val' => $where_val);
        return $this;
    }
    public function page_call($url = '', $data = array(), $where_param = '', $where_value = '', $method = 'get')
    {
        return $this->send_external($url, $data, $method, 'all', $where_param, $where_value);
    }
    public function page_call_create($url = '', $data = array(), $where_param = '', $where_value = '', $method = 'get')
    {
        return $this->send_external($url, $data, $method, 'create', $where_param, $where_value);
    }
    public function page_call_edit($url = '', $data = array(), $where_param = '', $where_value = '', $method = 'get')
    {
        return $this->send_external($url, $data, $method, 'edit', $where_param, $where_value);
    }
    public function subselect($column_name = '', $sql = '', $before = false, $table = false)
    {
        if ($column_name && $sql)
        {
            if ($before)
            {
                $fdata = $this->_parse_field_names($before, $table, 'subselect');
                $before = $fdata[0]['table'] . '.' . $fdata[0]['field'];
            }
            $table = $table ? $table : $this->_get_table('subselect');
            $this->subselect[$table . '.' . $column_name] = $sql;
            $this->subselect_before[$table . '.' . $column_name] = $before;
            $this->no_select[$table . '.' . $column_name] = true;
            $this->labels[$table . '.' . $column_name] = $column_name;
            $this->field_type[$table . '.' . $column_name] = 'subselect';
        }
        return $this;
    }
    public function highlight($columns = '', $operator = '', $value = '', $color = '', $class = '', $table = false)
    {
        if ($columns && $operator)
        {
            $fdata = $this->_parse_field_names($columns, $table, 'highlight');
            foreach ($fdata as $fitem)
            {
                $this->highlight[$fitem['table'] . '.' . $fitem['field']][] = array(
                    'value' => $value,
                    'operator' => $operator,
                    'color' => $color,
                    'class' => $class,
                    'table' => $fitem['table']);
            }
        }
        return $this;
    }
    public function modal($columns = '', $icon = 'icon-fullscreen', $table = false)
    {
        $fdata = $this->_parse_field_names($columns, $table, 'modal');
        foreach ($fdata as $fitem)
        {
            $this->modal[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $icon;
        }
        return $this;
    }
    public function column_class($columns = '', $class = '', $table = false)
    {
        $fdata = $this->_parse_field_names($columns, $table, 'column_class');
        foreach ($fdata as $fitem)
        {
            $this->column_class[$fitem['table'] . '.' . $fitem['field']][] = isset($fitem['value']) ? $fitem['value'] : $class;
        }
        return $this;
    }
    public function language($lang = 'en')
    {
        $this->language = $lang;
        return $this;
    }
    public function field_tooltip($fields = '', $tooltip = '', $icon = false, $table = false)
    {
        if ($fields && $tooltip)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'column_class');
            foreach ($fdata as $fitem)
            {
                $this->field_tooltip[$fitem['table'] . '.' . $fitem['field']] = array('tooltip' => $this->html_safe(isset($fitem['value']) ?
                        $fitem['value'] : $tooltip), 'icon' => $icon);
            }
            return $this;
        }
    }

    public function search_columns($fields = '', $default = null, $table = false)
    {
        if ($fields)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'search_columns');
            foreach ($fdata as $fitem)
            {
                $this->search_columns[$fitem['table'] . '.' . $fitem['field']] = array('table' => $fitem['table'], 'column' => $fitem['field']);
            }
        }
        if ($default)
        {
            $fdata = $this->_parse_field_names($default, $table, 'search_columns');
            $this->search_default = $fdata[0]['table'] . '.' . $fdata[0]['field'];
        }
        return $this;
    }

    public function before_insert($class = '', $func = '', $lib = '', $parent_act = '')
    {
        if ($func && $lib)
        {
            $this->before_insert['class'] = $class ? $class : '';
            $this->before_insert['func'] = $func;
            $this->before_insert['lib'] = $lib;
            $this->before_insert['act'] = $parent_act;
        }
        return $this;
    }
    public function before_update($class = '', $func = '', $lib = '', $parent_act = '')
    {
        if ($func && $lib)
        {
            $this->before_update['class'] = $class ? $class : '';
            $this->before_update['func'] = $func;
            $this->before_update['lib'] = $lib;
            $this->before_update['act'] = $parent_act;
        }
        return $this;
    }
    public function before_remove($class = '', $func = '', $lib = '', $parent_act = '')
    {
        if ($func && $lib)
        {
            $this->before_remove['class'] = $class ? $class : '';
            $this->before_remove['func'] = $func;
            $this->before_remove['lib'] = $lib;
            $this->before_remove['act'] = $parent_act;
        }
        return $this;
    }
    public function after_insert($class = '', $func = '', $lib = '', $parent_act = '')
    {
        if ($func && $lib)
        {
            $this->after_insert['class'] = $class ? $class : '';
            $this->after_insert['func'] = $func;
            $this->after_insert['lib'] = $lib;
            $this->after_insert['act'] = $parent_act;
        }
        return $this;
    }
    public function after_update($class = '', $func = '', $lib = '', $parent_act = '')
    {
        if ($func && $lib)
        {
            $this->after_update['class'] = $class ? $class : '';
            $this->after_update['func'] = $func;
            $this->after_update['lib'] = $lib;
            $this->after_update['act'] = $parent_act;
        }
        return $this;
    }
    public function after_remove($class = '', $func = '', $lib = '', $parent_act = '')
    {
        if ($func && $lib)
        {
            $this->after_remove['class'] = $class ? $class : '';
            $this->after_remove['func'] = $func;
            $this->after_remove['lib'] = $lib;
            $this->after_remove['act'] = $parent_act;
        }
        return $this;
    }
    public function column_callback($fields = '', $callback = '', $path = '', $table = false)
    {
        if ($fields && $callback && $path)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'column_callback');
            foreach ($fdata as $fitem)
            {
                $this->column_callback[$fitem['table'] . '.' . $fitem['field']] = array(
                    'name' => $fitem['table'] . '.' . $fitem['field'],
                    'real_name' => $fitem['field'],
                    'table' => $fitem['table'],
                    'path' => rtrim($path, '/'),
                    'callback' => $callback);
            }
            return $this;
        }
    }
    public function field_callback($fields = '', $callback = '', $path = '', $table = false)
    {
        if ($fields && $callback && $path)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'column_callback');
            foreach ($fdata as $fitem)
            {
                $this->field_callback[$fitem['table'] . '.' . $fitem['field']] = array(
                    'name' => $fitem['table'] . '.' . $fitem['field'],
                    'real_name' => $fitem['field'],
                    'table' => $fitem['table'],
                    'path' => rtrim($path, '/'),
                    'callback' => $callback);
            }
            return $this;
        }
    }
    public function _image_check()
    {
        $instanse_name = $this->instance_name();
        if (!isset($_SESSION['xcrud_session']['xcrud_' . $instanse_name]['key']) || $this->_get('key') != $_SESSION['xcrud_session']['xcrud_' .
            $instanse_name]['key'])
            $this->error('Your session has gone... Please, reload the page');
        else
            $this->_import_vars($instanse_name);
    }
    public function _ajax_check()
    {
        $instanse_name = $this->instance_name();
        if (!isset($_SESSION['xcrud_session']['xcrud_' . $instanse_name]['key']) || $this->_post('key') != $_SESSION['xcrud_session']['xcrud_' .
            $instanse_name]['key'])
            $this->error('Your session has gone... Please, reload the page');
        else
            $this->_import_vars($instanse_name);
    }
    public function render($task = false)
    {
        $this->benchmark_start();
        $this->key = $this->_post('task') == 'depend' ? $this->_post('key') : $this->_regenerate_key();
        if (!$this->table_name)
            $this->table_name = $this->_humanize($this->table);
        $this->_remove_and_save_uploads();
        $this->_export_vars();
        $this->_export_inner_vars();
        $this->_receive_post();
        $this->_get_language();
        if ($task == 'create')
            $_POST['task'] = 'create';
        switch ($this->_post('task'))
        {
            case 'create':
                $this->_set_field_types($this->_table_info(), 'create');
                $this->_sort_defaults();
                return $this->_create();
                break;
            case 'edit':
                $this->_set_field_types($this->_table_info(), 'edit');
                return $this->_entry($this->_post('primary'), 'edit');
                break;
            case 'save':
                $table_info = $this->_table_info();
                $this->_set_field_types($table_info);
                $primary = $this->_save();
                switch ($this->_post('after'))
                {
                    case 'create':
                        $this->_import_vars($this->_instance_name);
                        $this->_set_field_types($table_info, 'create');
                        $this->_sort_defaults();
                        return $this->_create();
                        break;
                    case 'edit':
                        $this->_import_vars($this->_instance_name);
                        $this->_set_field_types($table_info, 'edit');
                        return $this->_entry($primary, 'edit');
                        break;
                    case 'list':
                    default:
                        $this->_set_columns($table_info);
                        return $this->_list();
                        break;
                }
                break;
            case 'remove':
                $this->_set_columns($this->_table_info());
                $this->_remove($this->_post('primary'));
                return $this->_list();
                break;
            case 'upload':
                return $this->_upload();
                break;
            case 'remove_upload':
                return $this->_remove_upload();
                break;
            case 'unique':
                return $this->_check_unique_value();
                break;
            case 'clone':
                $table_info = $this->_table_info();
                $this->_set_columns($table_info, true);
                $this->_clone_row($this->_post('primary'), $table_info);
                return $this->_list();
                break;
            case 'print':
                $this->_set_columns($this->_table_info());
                $this->theme = 'printout';
                return $this->_list();
                break;
            case 'depend':
                echo $this->create_relation($this->_post('name'), $this->_post('value'), $this->_post('dependval'));
                break;
            case 'view':
                $this->_set_field_types($this->_table_info(), 'view');
                return $this->_entry($this->_post('primary'), 'view');
                break;
            case 'query':

                break;
            case 'external':

                break;
            case 'list':
            default:
                $this->_set_columns($this->_table_info());
                return $this->_list();
                break;
        }
    }
    public function render_inner()
    {
        $this->benchmark_start();
        //$this->instance_count = count(self::$_instance);
        $this->key = $this->_regenerate_key();
        if (!$this->table_name)
            $this->table_name = $this->_humanize($this->table);
        $this->_remove_and_save_uploads();
        $this->_export_vars();
        $this->_export_inner_vars();
        $this->_get_language();
        $this->_set_columns($this->_table_info());
        return $this->_list();
    }
    public function render_image()
    {
        $field = $this->_get('field');
        if (!$field)
            exit();
        if ($this->_get('primary_key'))
            $this->primary_key = $this->_get('primary_key');
        $thumb = $this->_get('thumb') ? true : false;
        $marker = (isset($this->upload_config[$field]['thumb_marker']) && $this->upload_config[$field]['thumb_marker']) ? $this->
            upload_config[$field]['thumb_marker'] : '_thumb';
        $image = array_search($field, array_reverse($this->upload_to_save));
        if (!$image)
        {
            list($tmp1, $tmp2) = explode('.', $field);
            $db = Xcrud_db::get_instance($this->connection);
            $db->query("SELECT `{$tmp2}` FROM `{$tmp1}` WHERE `{$this->primary}` = " . $db->escape($this->primary_key) . " LIMIT 1");
            $row = $db->row();
            $image = $row[$tmp2];
            if (isset($this->upload_config[$field]['blob']) && $this->upload_config[$field]['blob'] === true)
            {
                $output = $image;
                unset($image);
            } else
            {
                $folder = $this->upload_folder[$field];
                $image = ($thumb ? substr_replace($image, $marker, strrpos($image, '.'), 0) : $image);
                $path = $this->check_folder($folder, 'render_image') . '/' . $image;
                if (!is_file($path))
                {
                    header("HTTP/1.0 404 Not Found");
                    exit('Not Found');
                }
                $output = file_get_contents($path);
            }
        } else
        {
            $folder = $this->upload_folder[$field];
            $image = ($thumb ? substr_replace($image, $marker, strrpos($image, '.'), 0) : $image);
            $path = $this->check_folder($folder, 'render_image') . '/' . $image;
            if (!is_file($path))
            {
                header("HTTP/1.0 404 Not Found");
                exit('Not Found');
            }
            $output = file_get_contents($path);
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        if ($this->field_type[$field] == 'image')
        {
            header("Content-type: image/*");
        } else
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . $image . "\"");
            header("Content-Transfer-Encoding: binary");
        }
        if (isset($this->upload_config[$field]['blob']) && $this->upload_config[$field]['blob'] === true)
            header("Content-Length: " . strlen($output));
        else
            header("Content-Length: " . filesize($path));
        echo $output;
        exit();
    }
    public function render_csv()
    {
        $this->_receive_post();
        $db = Xcrud_db::get_instance($this->connection);
        if (Xcrud_config::$csv_all_fields)
        {
            $head = $db->csv_head($this->table);
            $db->query("SELECT * FROM `{$this->table}`");
        } else
        {
            $this->_set_columns($this->_table_info());
            $select = $this->_build_select_list(true);
            $table_join = $this->_build_table_join();
            $where = $this->_build_where();
            $order_by = $this->_build_order_by();
            $this->_set_column_names();
            $head = $this->columns_names;
            $db->query("SELECT {$select} FROM `{$this->table}` {$table_join} {$join} {$where} {$order_by}");
        }
        $db->return_csv($this->table, $head);
    }
    protected function _post($field = '')
    {
        if (isset($_POST[$field]))
        {
            if (!is_array($_POST[$field]))
                return trim($_POST[$field]);
            return $_POST[$field];
        } else
            return '';
    }
    protected function _get($field = '')
    {
        if (isset($_GET[$field]))
        {
            if (!is_array($_GET[$field]))
                return trim($_GET[$field]);
            return $_GET[$field];
        } else
            return '';
    }

    protected function _create()
    {
        if (!$this->is_add)
            return $this->error('Forbidden');
        $this->_set_field_names();
        $this->primary_key = 0;
        return $this->_render_edit($this->defaults, 'create');
    }
    protected function _entry($primary = '', $mode = 'edit')
    {
        if ((!$this->is_edit && $mode == 'edit') or (!$this->is_view && $mode == 'view'))
            return $this->error('Forbidden');
        $this->_set_field_names();
        $this->where_pri($this->primary, $primary);
        $select = $this->_build_select_details();
        $where = $this->_build_where();
        $table_join = $this->_build_table_join();

        $db = Xcrud_db::get_instance($this->connection);
        $db->query("SELECT {$select}\r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n LIMIT 1");
        $list = $db->row();
        if ($this->condition)
        {
            foreach ($this->condition as $field => $params)
            {
                if (isset($list->$field) && $this->_compare($list->$field, $params['operator'], $params['value']))
                {
                    $this->$params['method']($params['param']);
                }
            }
        }
        if ($mode == 'view')
            return $this->_render_details($list, $mode);
        else
            return $this->_render_edit($list, $mode);
    }

    protected function _insert($postdata, $no_processing = false, $no_processing_fields = array())
    {
        $set = array();
        $db = Xcrud_db::get_instance($this->connection);
        $fields = array_merge($this->fields, $this->hidden_fields);
        foreach ($postdata as $key => $val)
        {
            if (isset($fields[$key]) && !isset($this->locked_fields[$key]))
            {

                if (isset($this->field_type[$key]) && $this->field_type[$key] == 'password')
                {
                    if (trim($val) == '')
                        continue;
                    else
                    {
                        switch ($this->defaults[$key])
                        {
                            case 'md5':
                                $val = md5($val);
                                break;
                            case 'sha1':
                                $val = sha1($val);
                                break;
                        }
                    }
                }
                if (isset($this->field_type[$key]) && $this->field_type[$key] == 'timestamp')
                    $val = strtotime($val);
                
               

                if (is_array($val))
                {
                    $set[$fields[$key]['table']]["`{$fields[$key]['field']}`"] = $db->escape(implode(',', $val));
                } else
                    $set[$fields[$key]['table']]["`{$fields[$key]['field']}`"] = ((isset($this->no_quotes[$key]) && isset($this->
                        pass_var_create[$key])) ? $db->escape($val, true) : $db->escape($val));
            } elseif ($no_processing)
            {
                $set[$no_processing_fields[$key]['table']]["`{$no_processing_fields[$key]['field']}`"] = $db->escape($val);
            }
        }
        //$keys = array_keys($set[$this->table]);
        if (!$this->demo_mode)
            $db->query("INSERT INTO `{$this->table}` (" . implode(',', array_keys($set[$this->table])) . ") VALUES (" . implode(',',
                $set[$this->table]) . ")");
        if ($this->primary_ai)
        {
            $ins_id = $db->insert_id();
            $set[$this->table]["`{$this->primary}`"] = $db->escape($ins_id);
        } else
        {
            $ins_id = $postdata[$this->table . '.' . $this->primary];
        }
        if ($this->table_join)
        {
            foreach ($this->table_join as $alias => $param)
            {
                $set[$alias]["`{$param['join_field']}`"] = $set[$param['table']]["`{$param['field']}`"];
                if (!$this->demo_mode)
                    $db->query("INSERT INTO `{$param['join_table']}` (" . implode(',', array_keys($set[$alias])) . ") VALUES (" . implode(',',
                        $set[$alias]) . ")");
            }
        }
        unset($set, $postdata);
        return $ins_id;
    }
    protected function _update($postdata, $primary)
    {
        $res;
        $set = array();
        $db = Xcrud_db::get_instance($this->connection);
        $fields = array_merge($this->fields, $this->hidden_fields);
        foreach ($postdata as $key => $val)
        {
            if (isset($fields[$key]) && !isset($this->locked_fields[$key]))
            {
                if (isset($this->field_type[$key]) && $this->field_type[$key] == 'password')
                {
                    if (trim($val) == '')
                        continue;
                    else
                    {
                        switch ($this->defaults[$key])
                        {
                            case 'md5':
                                $val = md5($val);
                                break;
                            case 'sha1':
                                $val = sha1($val);
                                break;
                        }
                    }
                }
                if (isset($this->field_type[$key]) && $this->field_type[$key] == 'timestamp')
                    $val = strtotime($val);
                // echo $key.' = '.$val.'<br />';
                if (is_array($val))
                {
                    $set[] = "`{$fields[$key]['table']}`.`{$fields[$key]['field']}` = " . $db->escape(implode(',', $val));
                } else
                    $set[] = "`{$fields[$key]['table']}`.`{$fields[$key]['field']}` = " . ((isset($this->no_quotes[$key]) && isset($this->
                        pass_var_edit[$key])) ? $db->escape($val, true) : $db->escape(trim($val)));
            }
        }
        if (!$this->table_join)
        {
            if (!$this->demo_mode)
                $res = $db->query("UPDATE `{$this->table}` SET " . implode(',', $set) . " WHERE `{$this->primary}` = " . $db->escape($primary) .
                    " LIMIT 1");
        } else
        {
            //$tables = array('`' . $this->table . '`');
            $joins = array();
            foreach ($this->table_join as $alias => $param)
            {
                //$tables[] = '`' . $alias . '`';
                $joins[] = "INNER JOIN `{$param['join_table']}` AS `{$alias}` 
                    ON `{$param['table']}`.`{$param['field']}` = `{$alias}`.`{$param['join_field']}`";
            }
            if (!$this->demo_mode)
                $res = $db->query("UPDATE `{$this->table}` AS `{$this->table}` " . implode(' ', $joins) . " SET " . implode(',', $set) .
                    " WHERE `{$this->table}`.`{$this->primary}` = " . $db->escape($primary));
        }
        if (isset($postdata[$this->table . '.' . $this->primary]) && $res)
            $primary = $postdata[$this->table . '.' . $this->primary];
        unset($set, $postdata);
        return $primary;
    }
    protected function _remove($primary)
    {
        $del;
        if (!$this->is_remove)
            return $this->error('Forbidden');
        if ($this->before_remove)
        {
            $path = $this->check_file($this->before_remove['lib'], 'before_remove');
            include_once ($path);
            $callable = $this->before_remove['class'] ? array($this->before_remove['class'], $this->before_remove['func']) : $this->
                before_remove['func'];
            if (is_callable($callable))
            {
                call_user_func_array($callable, array($primary));
            }
        }
        if ($this->replace_remove)
        {
            $path = $this->check_file($this->replace_remove['lib'], 'replace_remove');
            include_once ($path);
            if (is_callable($this->replace_remove['callable']))
            {
                $primary = call_user_func_array($this->replace_remove['callable'], array($primary, $this));
            }
        } else
        {
            // remove case
            $db = Xcrud_db::get_instance($this->connection);
            $del_row = false;
            $fields = array();
            if (in_array('image', $this->field_type) or in_array('file', $this->field_type))
            {
                foreach ($this->field_type as $key => $type)
                {
                    if ($type == 'image' or $type == 'file')
                    {
                        $tmp = explode('.', $key);
                        $fields[$key] = "`{$tmp[0]}`.`{$tmp[1]}` AS `{$key}`";
                    }
                }
            }
            if (!$this->table_join)
            {
                if ($fields)
                {
                    $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` WHERE `{$this->primary}` = " . $db->escape($primary) .
                        " LIMIT 1");
                    $del_row = $db->row();
                }
                if (!$this->demo_mode)
                    $del = $db->query("DELETE FROM `{$this->table}` WHERE `{$this->primary}` = " . $db->escape($primary) . " LIMIT 1");
            } else
            {
                $tables = array('`' . $this->table . '`');
                $joins = array();
                foreach ($this->table_join as $alias => $param)
                {
                    $tables[] = '`' . $alias . '`';
                    $joins[] = "INNER JOIN `{$param['join_table']}` AS `{$alias}` 
                    ON `{$param['table']}`.`{$param['field']}` = `{$alias}`.`{$param['join_field']}`";
                }
                if ($fields)
                {
                    $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` AS `{$this->table}` " . implode(' ', $joins) .
                        " WHERE `{$this->table}`.`{$this->primary}` = " . $db->escape($primary));
                    $del_row = $db->row();
                }
                if (!$this->demo_mode)
                    $del = $db->query("DELETE " . implode(',', $tables) . " FROM `{$this->table}` AS `{$this->table}` " . implode(' ', $joins) .
                        " WHERE `{$this->table}`.`{$this->primary}` = " . $db->escape($primary));
            }
            if ($del_row && !$this->demo_mode)
            {
                foreach ($del_row as $key => $val)
                {
                    if ($val && !isset($this->upload_config[$key]['blob']))
                    {
                        $folder = $this->upload_folder[$key];
                        @unlink($folder . '/' . $val);
                        if ($this->_is_thumb($key))
                            @unlink($folder . '/' . $this->_thumb_name($key, $val));
                    }
                }
            }
            // end of remove case
        }
        if ($this->after_remove)
        {
            $path = $this->check_file($this->after_remove['lib'], 'after_remove');
            include_once ($path);
            $callable = $this->after_remove['class'] ? array($this->after_remove['class'], $this->after_remove['func']) : $this->
                after_remove['func'];
            if (is_callable($callable))
            {
                call_user_func_array($callable, array($primary));
            }
        }
        return $del;
    }
    protected function _save()
    {
        $primary = $this->_post('primary');
        $postdata = $this->_post('postdata');
        if ($this->upload_config)
        {
            foreach ($this->upload_config as $key => $opts)
            {
                if (isset($opts['blob']) && $opts['blob'] && isset($postdata[$key]) && $postdata[$key] != '')
                {
                    if ($postdata[$key] == 'blob-storage')
                    {
                        unset($postdata[$key]);
                        continue;
                    } else
                    {
                        $folder = $this->upload_folder[$key];
                        $path = $this->check_folder($folder, 'save') . '/' . $postdata[$key];
                        $postdata[$key] = file_get_contents($path);
                        @unlink($path);
                    }
                }
            }
        }
        if (!$primary)
        {
            if (!$this->is_add)
                return $this->error('Forbidden');
            if ($this->pass_var_create)
            {
                foreach ($this->pass_var_create as $field => $param)
                {
                    $postdata[$field] = $param['value'];
                    $this->hidden_fields[$field] = array('table' => $param['table'], 'field' => $param['field']);
                }
            }
            if ($this->alert_create)
            {
                foreach ($this->alert_create as $alert)
                {
                    if ($alert['field'] && isset($postdata[$alert['field']]) && $postdata[$alert['field']] != $alert['value'])
                        continue;
                    if (!isset($postdata[$alert['column']]) or !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/',
                        $postdata[$alert['column']]))
                        continue;
                    $alert['message'] = $this->replace_text_variables($alert['message'], $postdata);
                    if (Xcrud_config::$email_enable_html)
                        $message = $alert['message'] . '<br /><br />' . "\r\n" . ($alert['link'] ? '<a href="' . $alert['link'] .
                            '" target="_blank">' . $alert['link'] . '</a>' : '');
                    else
                        $message = $alert['message'] . "\r\n\r\n" . ($alert['link'] ? $alert['link'] : '');
                    $this->send_email($postdata[$alert['column']], $alert['subject'], $message, $alert['cc'], Xcrud_config::$email_enable_html);
                }
            }
            if ($this->mass_alert_create)
            {
                foreach ($this->mass_alert_create as $alert)
                {
                    if ($alert['field'] && isset($postdata[$alert['field']]) && $postdata[$alert['field']] != $alert['value'])
                        continue;
                    $alert['message'] = $this->replace_text_variables($alert['message'], $postdata);
                    $alert['where'] = $this->replace_text_variables($alert['where'], $postdata);
                    if (Xcrud_config::$email_enable_html)
                        $message = $alert['message'] . '<br /><br />' . "\r\n" . ($alert['link'] ? '<a href="' . $alert['link'] .
                            '" target="_blank">' . $alert['link'] . '</a>' : '');
                    else
                        $message = $alert['message'] . "\r\n\r\n" . ($alert['link'] ? $alert['link'] : '');
                    $db = Xcrud_db::get_instance();
                    $db->query("SELECT `{$alert['email_column']}` FROM `{$alert['email_table']}`" . ($alert['where'] ? ' WHERE ' . $alert['where'] :
                        ''));
                    foreach ($db->result() as $row)
                    {
                        $this->send_email($row[$alert['email_column']], $alert['subject'], $message, array(), Xcrud_config::$email_enable_html);
                    }
                }
            }
            if ($this->before_insert)
            {
                $path = $this->check_file($this->before_insert['lib'], 'before_insert');
                include_once ($path);
                $callable = $this->before_insert['class'] ? array($this->before_insert['class'], $this->before_insert['func']) : $this->
                    before_insert['func'];
                if (is_callable($callable))
                {
                    $postdata = call_user_func_array($callable, array($postdata, $this));
                }
            }
            if ($this->replace_insert)
            {
                $path = $this->check_file($this->replace_insert['lib'], 'replace_insert');
                include_once ($path);
                if (is_callable($this->replace_insert['callable']))
                {
                    $primary = call_user_func_array($this->replace_insert['callable'], array($postdata, $this));
                }
            } else
                $primary = $this->_insert($postdata);
            if ($this->after_insert)
            {
                $path = $this->check_file($this->after_insert['lib'], 'after_insert');
                include_once ($path);
                $callable = $this->after_insert['class'] ? array($this->after_insert['class'], $this->after_insert['func']) : $this->
                    after_insert['func'];
                if (is_callable($callable))
                {
                    call_user_func_array($callable, array(
                        $postdata,
                        $primary,
                        $this));
                }
            }
            if ($this->send_external_create)
            {
                if (!$this->send_external_create['where_field'] or $postdata[$this->send_external_create['where_field']] == $this->
                    send_external_create['where_val'])
                {
                    foreach ($this->send_external_create['data'] as $key => $value)
                    {
                        $this->send_external_create['data'][$key] = $this->replace_text_variables($value, $postdata + array($this->table . '.' .
                                $this->primary => $primary));
                    }
                    switch ($this->send_external_create['method'])
                    {
                        case 'include':
                            $path = $this->check_file($this->send_external_create['path'], 'send_external_create');
                            ob_start();
                            extract($this->send_external_create['data']);
                            include ($path);
                            ob_end_clean();
                            break;
                        case 'get':
                        case 'post':
                            $this->send_http_request($this->send_external_create['path'], $this->send_external_create['data'], $this->
                                send_external_create['method'], false);
                            break;
                    }
                }
            }
        } else
        {
            if (!$this->is_edit)
                return $this->error('Forbidden');
            if ($this->pass_var_edit)
            {
                foreach ($this->pass_var_edit as $field => $param)
                {
                    $postdata[$field] = $param['value'];
                    $this->hidden_fields[$field] = array('table' => $param['table'], 'field' => $param['field']);
                }
            }
            if ($this->alert_edit)
            {
                foreach ($this->alert_edit as $alert)
                {
                    if ($alert['field'] && isset($postdata[$alert['field']]) && $postdata[$alert['field']] != $alert['value'])
                        continue;
                    if (!isset($postdata[$alert['column']]) or !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/',
                        $postdata[$alert['column']]))
                        continue;
                    $alert['message'] = $this->replace_text_variables($alert['message'], $postdata);
                    if (Xcrud_config::$email_enable_html)
                        $message = $alert['message'] . '<br /><br />' . "\r\n" . ($alert['link'] ? '<a href="' . $alert['link'] .
                            '" target="_blank">' . $alert['link'] . '</a>' : '');
                    else
                        $message = $alert['message'] . "\r\n\r\n" . ($alert['link'] ? $alert['link'] : '');
                    $this->send_email($postdata[$alert['column']], $alert['subject'], $message, $alert['cc'], Xcrud_config::$email_enable_html);
                }
            }
            if ($this->mass_alert_edit)
            {
                foreach ($this->mass_alert_edit as $alert)
                {
                    if ($alert['field'] && isset($postdata[$alert['field']]) && $postdata[$alert['field']] != $alert['value'])
                        continue;
                    $alert['message'] = $this->replace_text_variables($alert['message'], $postdata);
                    $alert['where'] = $this->replace_text_variables($alert['where'], $postdata);
                    if (Xcrud_config::$email_enable_html)
                        $message = $alert['message'] . '<br /><br />' . "\r\n" . ($alert['link'] ? '<a href="' . $alert['link'] .
                            '" target="_blank">' . $alert['link'] . '</a>' : '');
                    else
                        $message = $alert['message'] . "\r\n\r\n" . ($alert['link'] ? $alert['link'] : '');
                    $db = Xcrud_db::get_instance();
                    $db->query("SELECT `{$alert['email_column']}` FROM `{$alert['email_table']}`" . ($alert['where'] ? ' WHERE ' . $alert['where'] :
                        ''));
                    foreach ($db->result() as $row)
                    {
                        $this->send_email($row[$alert['email_column']], $alert['subject'], $message, array(), Xcrud_config::$email_enable_html);
                    }
                }
            }
            if ($this->before_update)
            {
                $path = $this->check_file($this->before_update['lib'], 'before_update');
                include_once ($path);
                $callable = $this->before_update['class'] ? array($this->before_update['class'], $this->before_update['func']) : $this->
                    before_update['func'];
                if (is_callable($callable))
                {
                    $postdata = call_user_func_array($callable, array(
                        $postdata,
                        $primary,
                        $this));
                }
            }
            if ($this->replace_update)
            {
                $path = $this->check_file($this->replace_update['lib'], 'replace_update');
                include_once ($path);
                if (is_callable($this->replace_update['callable']))
                {
                    $primary = call_user_func_array($this->replace_update['callable'], array(
                        $postdata,
                        $primary,
                        $this));
                }
            } else
                $primary = $this->_update($postdata, $primary);
            if ($this->after_update)
            {
                $path = $this->check_file($this->after_update['lib'], 'after_update');
                include_once ($path);
                $callable = $this->after_update['class'] ? array($this->after_update['class'], $this->after_update['func']) : $this->
                    after_update['func'];
                if (is_callable($callable))
                {
                    call_user_func_array($callable, array(
                        $postdata,
                        $primary,
                        $this));
                }
            }
            if ($this->send_external_edit)
            {
                if (!$this->send_external_edit['where_field'] or $postdata[$this->send_external_edit['where_field']] == $this->
                    send_external_edit['where_val'])
                {
                    foreach ($this->send_external_edit['data'] as $key => $value)
                    {
                        $this->send_external_edit['data'][$key] = $this->replace_text_variables($value, $postdata);
                    }
                    switch ($this->send_external_edit['method'])
                    {
                        case 'include':
                            $path = $this->check_file($this->send_external_edit['path'], 'send_external_edit');
                            ob_start();
                            extract($this->send_external_edit['data']);
                            include ($path);
                            ob_end_clean();
                            break;
                        case 'get':
                        case 'post':
                            $this->send_http_request($this->send_external_edit['path'], $this->send_external_edit['data'], $this->
                                send_external_edit['method'], false);
                            break;
                    }
                }
            }
        }
        unset($postdata);
        return $primary;
    }
    protected function _list()
    {
        $select = $this->_build_select_list();
        $table_join = $this->_build_table_join();
        $where = $this->_build_where();
        $order_by = $this->_build_order_by();
        $sum_tmp = array();
        if ($this->sum)
        {
            foreach ($this->sum as $field => $param)
            {
                if (isset($this->columns[$field]))
                    $sum_tmp[$field] = "SUM(`{$param['table']}`.`{$param['column']}`) AS `{$field}`";
                else
                    $sum_tmp[$field] = "SUM({$this->subselect_query[$field]}) AS `{$field}`";
            }
        }
        $sum = $sum_tmp ? ', ' . implode(', ', $sum_tmp) : '';
        $db = Xcrud_db::get_instance($this->connection);
        $db->query("SELECT COUNT(`{$this->table}`.`{$this->primary}`) AS `count` {$sum} \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}");
        $this->sum_row = $db->row();
        $total = $this->sum_row['count'];
        $limit = $this->_build_limit($total);
        $db->query("SELECT {$select} \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n {$order_by}\r\n {$limit}");
        $list = $db->result();

        $this->_set_column_names();
        return $this->_render_list($list, $total);
    }
    protected function where_pri($fields = '', $where_val = '', $table = false, $glue = 'AND')
    {

        if ($fields)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'where_pri');
            foreach ($fdata as $fitem)
            {
                $this->where_pri[] = array(
                    'table' => $fitem['table'],
                    'field' => $fitem['field'],
                    'value' => isset($fitem['value']) ? $fitem['value'] : $where_val,
                    'glue' => $glue);
            }
            unset($fields, $fdata);
        } elseif ($where_val)
        {
            $this->where_pri[] = array('custom' => $where_val, 'glue' => $glue);
            unset($where_val);
        }
        return $this;
    }
    protected function _build_select_list($csv = false)
    {
        $this->find_grid_text_variables();
        if ($this->buttons)
        {
            foreach ($this->buttons as $button)
            {
                if ($button['params'])
                {
                    foreach ($button['params'] as $key => $value)
                    {
                        if (!isset($this->columns[$key]))
                        {
                            unset($value['name']);
                            $this->columns[$key] = $value;
                            $this->field_type[$key] = 'hidden';
                        }
                    }
                }
            }
        }
        $columns = array();
        $subselect_before = $this->subselect_before;
        foreach ($this->columns as $key => $val)
        {
            if ($val)
            {
                $field_index = $val['table'] . '.' . $val['column'];
                if ($name = array_search($field_index, $subselect_before))
                {
                    $columns[] = $this->subselect_query($name);
                    unset($subselect_before[$name]);
                }
                if (isset($this->join[$field_index]))
                {
                    if (is_array($this->join[$field_index]['rel_name']))
                    {
                        $tmp_fields = array();

                        foreach ($this->join[$field_index]['rel_name'] as $tmp)
                        {
                            $tmp_fields[] = "`{$tmp}` \r\n";
                        }
                        if ($this->join[$field_index])
                        {
                            $where = "FIND_IN_SET(`{$this->join[$field_index]['rel_tbl']}`.`{$this->join[$field_index]['rel_field']}`,`{$this->join[$field_index]['table']}`.`{$this->join[$field_index]['field']}`)";
                        } else
                        {
                            $where = "`{$this->join[$field_index]['rel_tbl']}`.`{$this->join[$field_index]['rel_field']}` = `{$this->join[$field_index]['table']}`.`{$this->join[$field_index]['field']}`";
                        }
                        $columns[] = "(SELECT GROUP_CONCAT(DISTINCT (CONCAT_WS('{$this->join[$field_index]['rel_separator']}'," . implode(',', $tmp_fields) .
                            ")) SEPARATOR ', ') 
                            FROM `{$this->join[$field_index]['rel_tbl']}` 
                            WHERE {$where}) 
                            AS `{$val['table']}.{$val['column']}` \r\n";
                    } elseif ($this->join[$field_index]['multi'])
                    {
                        $columns[] = "(SELECT GROUP_CONCAT(DISTINCT `{$this->join[$field_index]['rel_name']}` SEPARATOR ', ') 
                        FROM `{$this->join[$field_index]['rel_tbl']}` WHERE 
                        FIND_IN_SET(`{$this->join[$field_index]['rel_tbl']}`.`{$this->join[$field_index]['rel_field']}`,`{$this->join[$field_index]['table']}`.`{$this->join[$field_index]['field']}`) 
                        ORDER BY `{$this->join[$field_index]['rel_name']}` ASC)
                         AS `{$val['table']}.{$val['column']}` \r\n";
                    } else
                    {
                        $columns[] = "(SELECT `{$this->join[$field_index]['rel_alias']}`.`{$this->join[$field_index]['rel_name']}` 
                            FROM `{$this->join[$field_index]['rel_tbl']}` AS `{$this->join[$field_index]['rel_alias']}` 
                            WHERE `{$this->join[$field_index]['rel_alias']}`.`{$this->join[$field_index]['rel_field']}` = `{$this->join[$field_index]['table']}`.`{$this->join[$field_index]['field']}` 
                            LIMIT 1) 
                            AS `{$val['table']}.{$val['column']}` \r\n";
                    }
                } else
                {
                    $columns[] = "`{$val['table']}`.`{$val['column']}` AS `{$val['table']}.{$val['column']}` \r\n";
                }
            }
        }
        if ($subselect_before)
        {
            foreach ($subselect_before as $name => $none)
            {
                $columns[] = $this->subselect_query($name);
                unset($subselect_before[$name]);
            }
        }
        if (!$csv)
            $columns[] = "`{$this->table}`.`{$this->primary}` AS `primary_key` \r\n";
        return implode(',', $columns);
    }
    protected function subselect_query($name)
    {
        $sql = preg_replace_callback('/\{(.+)\}/Uu', array($this, 'subselect_callback'), $this->subselect[$name]);
        $this->subselect_query[$name] = $sql;
        return "({$sql}) AS `{$name}`";
    }
    protected function subselect_callback($matches)
    {
        if (strpos($matches[1], '.'))
        {
            /*if (!isset($this->columns[$matches[1]]))
            {*/
            $tmp = explode('.', $matches[1]);
            return '`' . $tmp[0] . '`.`' . $tmp[1] . '`';
            /* } else
            return '`' . $matches[1] . '`';*/
        } else
        {
            /*if (!isset($this->columns[$this->table . '.' . $matches[1]]))
            {
            $tmp = explode('.', $matches[1]);*/
            return '`' . $this->table . '`.`' . $matches[1] . '`';
            /* } else
            return '`' . $this->table . '.' . $matches[1] . '`';*/
        }
    }
    protected function _build_select_details()
    {
        $this->find_details_text_variables();
        $fields = array();
        if ($this->inner_table_instance)
        {
            foreach ($this->inner_table_instance as $field => $val)
            {
                if (!isset($this->fields[$field]))
                {
                    $fdata = $this->_parse_field_names($field, false, 'build_select_details');
                    $this->fields[$field] = array('table' => $fdata[0]['table'], 'field' => $fdata[0]['field']);
                    $this->field_type[$field] = 'hidden';
                }
            }
        }
        foreach ($this->fields as $key => $val)
        {
            if ($val)
                $fields[] = "`{$val['table']}`.`{$val['field']}` AS `$key`";
        }
        $fields[] = "`{$this->table}`.`{$this->primary}` AS `primary_key`";
        return implode(',', $fields);
    }
    protected function _build_table_join()
    {
        $join = '';
        if (count($this->table_join))
        {
            $join_arr = array();
            foreach ($this->table_join as $alias => $params)
            {
                $join_arr[] = "INNER JOIN `{$params['join_table']}` AS `{$alias}` 
                ON `{$params['table']}`.`{$params['field']}` = `{$alias}`.`{$params['join_field']}`";
            }
            $join .= implode(' ', $join_arr);
        }
        return $join;
    }

    protected function _build_where()
    {
        $db = Xcrud_db::get_instance($this->connection);
        $where_arr = array();
        $where_arr_pri = array();
        if ($this->where)
        {
            foreach ($this->where as $key => $params)
            {
                if (!is_int($key))
                {
                    $this->where_pri[] = $params;
                    continue;
                }
                if ($where_arr)
                    $where_arr[] = $params['glue'];
                if (!isset($params['custom']))
                {
                    if (is_array($params['value']))
                    {
                        $in_arr = array();
                        foreach ($params['value'] as $in_val)
                        {
                            $in_arr[] = $db->escape($in_val);
                        }
                        $where_arr[] = "`{$params['table']}`." . $this->_prepare_field_in($params['field']) . '(' . implode(',', $in_arr) . ')';
                    } else
                        $where_arr[] = "`{$params['table']}`." . $this->_prepare_field($params['field']) . $db->escape($params['value'], isset($this->
                            no_quotes[$params['table'] . '.' . $params['field']]));
                } else
                {
                    $where_arr[] = '(' . $params['custom'] . ')';
                }
            }
        }
        if ($this->where_pri)
        {
            foreach ($this->where_pri as $params)
            {
                if ($where_arr_pri)
                    $where_arr_pri[] = $params['glue'];
                $where_arr_pri[] = "`{$params['table']}`." . $this->_prepare_field($params['field']) . $db->escape($params['value']);
            }
        }

        $phrase = $this->_post('phrase');
        $column = $this->_post('column');
        if ($phrase && $column && ($this->_post('task') == 'list' or $this->_post('task') == 'print' or $this->_post('after') ==
            'list'))
        {
            $search_columns = array_merge($this->columns, $this->search_columns);
            if ($this->field_type == 'timestamp')
            {
                if (!is_array($phrase))
                    $this->timestamp_to_datetime($value);
            }
            //if ($where_arr)
            //    $where_arr[] = 'AND';
            if (is_array($phrase))
            {
                if ($this->field_type[$column] == 'date' && isset($phrase[0]) && $phrase[0] && isset($phrase[1]) && $phrase[1])
                {
                    if ($this->field_type == 'timestamp')
                    {
                        $phrase[0] = $this->timestamp_to_datetime($phrase[0]);
                        $phrase[1] = $this->timestamp_to_datetime($phrase[1]);
                    }
                    if ($where_arr)
                        $where_arr[] = 'AND';
                    $where_arr[] = "`{$search_columns[$column]['table']}`.`{$search_columns[$column]['column']}` BETWEEN " . $db->escape($phrase[0]) .
                        ' AND ' . $db->escape($phrase[1]);
                }
            } elseif (isset($this->join[$column]) && isset($search_columns[$column]))
            {
                if (is_array($this->join[$column]['rel_name']))
                {
                    $tmp_fields = array();

                    foreach ($this->join[$column]['rel_name'] as $tmp)
                    {
                        $tmp_fields[] = "`{$tmp}` \r\n";
                    }
                    if ($this->join[$column])
                    {
                        $where = "FIND_IN_SET(`{$this->join[$column]['rel_tbl']}`.`{$this->join[$column]['rel_field']}`,`{$this->join[$column]['table']}`.`{$this->join[$column]['field']}`)";
                    } else
                    {
                        $where = "`{$this->join[$column]['rel_tbl']}`.`{$this->join[$column]['rel_field']}` = `{$this->join[$column]['table']}`.`{$this->join[$column]['field']}`";
                    }
                    $select = "(SELECT GROUP_CONCAT(DISTINCT (CONCAT_WS('{$this->join[$column]['rel_separator']}'," . implode(',', $tmp_fields) .
                        ")) SEPARATOR ', ') 
                            FROM `{$this->join[$column]['rel_tbl']}` 
                            WHERE {$where})\r\n";
                } elseif ($this->join[$column]['multi'])
                {
                    $select = "(SELECT GROUP_CONCAT(DISTINCT `{$this->join[$column]['rel_name']}` SEPARATOR ', ') 
                        FROM `{$this->join[$column]['rel_tbl']}` WHERE 
                        FIND_IN_SET(`{$this->join[$column]['rel_tbl']}`.`{$this->join[$column]['rel_field']}`,`{$this->join[$column]['table']}`.`{$this->join[$column]['field']}`) 
                        ORDER BY `{$this->join[$column]['rel_name']}` ASC)\r\n";
                } else
                {
                    $select = "(SELECT `{$this->join[$column]['rel_alias']}`.`{$this->join[$column]['rel_name']}` 
                            FROM `{$this->join[$column]['rel_tbl']}` AS `{$this->join[$column]['rel_alias']}` 
                            WHERE `{$this->join[$column]['rel_alias']}`.`{$this->join[$column]['rel_field']}` = `{$this->join[$column]['table']}`.`{$this->join[$column]['field']}` 
                            LIMIT 1) \r\n";
                }
                if ($where_arr)
                    $where_arr[] = 'AND';
                $where_arr[] = "{$select} LIKE " . $db->escape_like($phrase, $this->search_pattern);
            } elseif (isset($this->subselect[$column]))
            {
                if ($where_arr)
                    $where_arr[] = 'AND';
                $where_arr[] = "({$this->subselect_query[$column]}) LIKE " . $db->escape_like($phrase, $this->search_pattern);
            } else
            {
                if ($where_arr)
                    $where_arr[] = 'AND';
                $where_arr[] = "`{$search_columns[$column]['table']}`.`{$search_columns[$column]['column']}` LIKE " . $db->escape_like($phrase,
                    $this->search_pattern);
            }
        }
        if ($where_arr or $where_arr_pri)
            return 'WHERE ' . ($where_arr ? '(' . implode(' ', $where_arr) . ')' : '') . ($where_arr_pri ? ($where_arr ? ' AND ' :
                '') . implode(' ', $where_arr_pri) : '');
        else
            return '';
    }
    protected function _build_rel_where($alias)
    {
        if (isset($this->join[$alias]))
        {
            if ($this->join[$alias]['rel_where'])
            {
                if (!is_array($this->join[$alias]['rel_where']))
                    return 'WHERE ' . $this->join[$alias]['rel_where'];
                else
                {
                    $db = Xcrud_db::get_instance($this->connection);
                    $where_arr = array();
                    foreach ($this->join[$alias]['rel_where'] as $field => $value)
                    {
                        $where_arr[] = $this->_prepare_field($field) . $db->escape($value);
                    }
                    return 'WHERE ' . implode(' AND ', $where_arr);
                }
            }
        }
    }
    protected function _receive_post()
    {
        if ($this->_post('order'))
        {
            $field = $this->_post('order');
            $direction = $this->_post('direct') == 'desc' ? 'desc' : 'asc';
            if (isset($this->order_by[$field]))
                unset($this->order_by[$field]);
            $this->order_by = array_merge(array($field => $direction), $this->order_by);
        }
    }
    protected function _build_order_by()
    {
        if (count($this->order_by))
        {
            $order_arr = array();
            foreach ($this->order_by as $field => $direction)
            {
                if (isset($this->columns[$field]) or isset($this->no_select[$field]) or !strpos($field, '.'))
                    $order_arr[] = "`{$field}` {$direction}";
                else
                {
                    $tmp = explode('.', $field);
                    $order_arr[] = "`{$tmp[0]}`.`{$tmp[1]}` {$direction}";
                }
            }
            return 'ORDER BY ' . implode(',', $order_arr);
        } else
        {
            if (isset($this->columns[$this->table . '.' . $this->primary]))
            {
                $this->order_by[$this->table . '.' . $this->primary] = 'ASC';
                return "ORDER BY `{$this->table}.{$this->primary}` ASC";
            } else
                return "ORDER BY `{$this->table}`.`{$this->primary}` ASC";
        }
    }
    protected function _build_limit($total)
    {
        $start = $this->_post('start') ? $this->_post('start') : 0;
        $limit = $this->_post('limit') ? $this->_post('limit') : $this->limit;
        if ($limit != 'all' && $this->theme != 'printout')
        {
            if ($start > 0 && $start >= $total)
            {
                $start = $total > $limit ? $total - $limit : 0;
            }
            $start = floor($start / $limit) * $limit;
            return "LIMIT {$start},{$limit}";
        } else
            return '';
    }
    protected function _table_info()
    {
        $table_info = array();
        $db = Xcrud_db::get_instance($this->connection);
        $db->query("SHOW COLUMNS FROM `{$this->table}`");
        $table_info[$this->table] = $db->result();
        if ($this->table_join)
        {
            foreach ($this->table_join as $alias => $join)
            {
                $db->query("SHOW COLUMNS FROM `{$join['join_table']}`");
                $table_info[$alias] = $db->result();
            }
        }
        return $table_info;
    }
    protected function _set_field_types($types_array, $mode = 'create')
    {
        if (is_array($types_array) && count($types_array))
        {
            $uni = false;
            $pri = false;
            $this->primary_ai = false;
            $fields = array();
            foreach ($types_array as $table => $types)
            {
                foreach ($types as $row)
                {
                    $field_index = $table . '.' . $row['Field'];
                    if ($row['Key'] == 'PRI' or $row['Key'] == 'UNI')
                        $this->unique[$field_index] = true;
                    if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment')
                    {
                        if ($table == $this->table)
                        {
                            $this->primary_ai = true;
                            if (!$this->primary)
                            {
                                $this->primary = $row['Field'];
                            }
                        }

                        if (!$this->show_primary_ai_field)
                        {
                            continue;
                        }
                        $this->disabled_on_create[$field_index] = true;
                        $this->disabled_on_edit[$field_index] = true;
                        $fields[$field_index] = array('table' => $table, 'field' => $row['Field']);
                    }
                    if ($row['Key'] == 'UNI' && !$uni)
                    {
                        $uni = $table == $this->table ? $row['Field'] : false;
                    }
                    if ($row['Key'] == 'PRI' && !$pri)
                    {
                        $pri = $table == $this->table ? $row['Field'] : false;
                    }
                    if (!$this->fields or ($this->field_marker && !isset($this->field_marker[$mode])))
                    {
                        $fields[$field_index] = array('table' => $table, 'field' => $row['Field']);
                    } elseif ($this->fields && $this->reverse_fields)
                    {
                        if (isset($this->field_marker[$mode]))
                        {
                            if ($this->fields[$field_index][$mode])
                                unset($this->fields[$field_index]);
                            else
                                $fields[$field_index] = array('table' => $table, 'field' => $row['Field']);
                        } else
                        {
                            if (isset($this->fields[$field_index]))
                                unset($this->fields[$field_index]);
                            else
                                $fields[$field_index] = array('table' => $table, 'field' => $row['Field']);
                        }
                    } elseif (isset($this->field_marker[$mode]) && !isset($this->fields[$field_index][$mode]))
                    {
                        unset($this->fields[$field_index]);
                        continue;
                    }
                    if (isset($this->join[$field_index]))
                    {
                        $this->field_type[$field_index] = 'relation';
                        if (!isset($this->defaults[$field_index]))
                            $this->defaults[$field_index] = $row['Default'];
                        //if (!isset($this->fields[$field_index]))
                        //    $fields[$field_index] = array('table' => $table, 'field' => $row['Field']);
                        continue;
                    }
                    if (!isset($this->field_type[$field_index]))
                    {
                        if (mb_strpos($row['Type'], '(') === false)
                        {
                            $type = $row['Type'];
                        } else
                        {
                            $l_pos = mb_strpos($row['Type'], '(');
                            $r_pos = mb_strpos($row['Type'], ')');
                            $type = mb_substr($row['Type'], 0, $l_pos);
                            $max_l = mb_substr($row['Type'], $l_pos + 1, $r_pos - $l_pos - 1);
                        }
                        switch ($type)
                        {
                            case 'tinyint':
                            case 'bit':
                            case 'bool':
                            case 'boolean':
                                if ($max_l == 1 && Xcrud_config::$make_checkbox)
                                {
                                    $this->field_type[$field_index] = 'bool';
                                    if (!isset($this->defaults[$field_index]))
                                        $this->defaults[$field_index] = $row['Default'];
                                } else
                                {
                                    $this->field_type[$field_index] = 'int';
                                    $this->field_maxsize[$field_index] = (int)$max_l;
                                    if (!isset($this->defaults[$field_index]))
                                        $this->defaults[$field_index] = $row['Default'];
                                }
                                break;
                            case 'smallint':
                            case 'mediumint':
                            case 'int':
                            case 'bigint':
                            case 'serial':
                                $this->field_type[$field_index] = 'int';
                                $this->field_maxsize[$field_index] = (int)$max_l;
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'decimal':
                            case 'numeric':
                            case 'float':
                            case 'double':
                            case 'real':
                                $this->field_type[$field_index] = 'float';
                                $this->field_maxsize[$field_index] = (int)$max_l + 1;
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'char':
                            case 'varchar':
                            case 'binary':
                            case 'varbinary':
                            default:
                                $this->field_type[$field_index] = 'text';
                                $this->field_maxsize[$field_index] = (int)$max_l;
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'text':
                            case 'tinytext':
                            case 'mediumtext':
                            case 'longtext':
                                if (!isset($this->no_editor[$field_index]))
                                    $this->field_type[$field_index] = 'texteditor';
                                else
                                    $this->field_type[$field_index] = 'textarea';
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'blob':
                            case 'tinyblob':
                            case 'mediumblob':
                            case 'longblob':
                                $this->field_type[$field_index] = 'binary';
                                $this->defaults[$field_index] = '';
                                break;
                            case 'date':
                                $this->field_type[$field_index] = 'date';
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'datetime':
                            case 'timestamp':
                                $this->field_type[$field_index] = 'datetime';
                                if (!isset($this->defaults[$field_index]))
                                {
                                    if ($row['Default'] == 'CURRENT_TIMESTAMP')
                                    {
                                        $db = Xcrud_db::get_instance($this->connection);
                                        $db->query('SELECT NOW() AS `now`');
                                        $tmstmp = $db->row();
                                        $this->defaults[$field_index] = $tmstmp['now'];
                                    } else
                                        $this->defaults[$field_index] = $row['Default'];
                                }
                                break;
                            case 'time':
                                $this->field_type[$field_index] = 'time';
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'year':
                                $this->field_type[$field_index] = 'year';
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'enum':
                                $this->field_type[$field_index] = 'select';
                                $this->field_maxsize[$field_index] = $max_l;
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                            case 'set':
                                $this->field_type[$field_index] = 'multiselect';
                                $this->field_maxsize[$field_index] = $max_l;
                                if (!isset($this->defaults[$field_index]))
                                    $this->defaults[$field_index] = $row['Default'];
                                break;
                        }
                    }
                }
            }
            if (!$this->fields or $this->reverse_fields or ($fields && !isset($this->field_marker[$mode])))
                $this->fields = $fields;
            if (!$this->primary)
            {
                if ($uni)
                    $this->primary = $uni;
                else
                    if ($pri)
                        $this->primary = $pri;
                    else
                        $this->primary = $types_array[$this->table][0]['Field'];
            }
            unset($fields, $types_array);
        }
    }
    protected function _set_columns($types_array, $check_dub_avail = false)
    {
        if (is_array($types_array) && count($types_array))
        {
            $columns = array();
            $uni = false;
            $pri = false;
            foreach ($types_array as $table => $types)
            {
                foreach ($types as $row)
                {
                    $field_index = "{$table}.{$row['Field']}";
                    if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment')
                    {
                        $this->primary_ai = $field_index;
                        if (!$this->primary)
                            $this->primary = $this->table == $table ? $row['Field'] : false;
                        if (!$this->show_primary_ai_column)
                        {
                            $this->field_type[$field_index] = 'int';
                            continue;
                        }

                    }
                    if ($row['Key'] == 'UNI' && !$uni)
                    {
                        $uni = $this->table == $table ? $row['Field'] : false;
                    }
                    if ($row['Key'] == 'PRI' && !$pri)
                    {
                        $pri = $this->table == $table ? $row['Field'] : false;
                    }
                    if (strstr($row['Type'], 'blob'))
                    {
                        $this->binary[$field_index] = true;
                    }
                    if (!$this->columns)
                    {
                        $columns[$field_index] = array('table' => $table, 'column' => $row['Field']);
                    } elseif ($this->columns && $this->reverse_columns)
                    {
                        if (isset($this->columns[$field_index]))
                            unset($this->columns[$field_index]);
                        else
                            $columns[$field_index] = array('table' => $table, 'column' => $row['Field']);
                    }
                    ######################
                    if (isset($this->join[$table][$row['Field']]))
                    {
                        $this->field_type[$table][$row['Field']] = 'relation';
                        continue;
                    }
                    if (!isset($this->field_type[$field_index]))
                    {
                        if (mb_strpos($row['Type'], '(') === false)
                        {
                            $type = $row['Type'];
                        } else
                        {
                            $l_pos = mb_strpos($row['Type'], '(');
                            $r_pos = mb_strpos($row['Type'], ')');
                            $type = mb_substr($row['Type'], 0, $l_pos);
                            $max_l = mb_substr($row['Type'], $l_pos + 1, $r_pos - $l_pos - 1);
                        }
                        switch ($type)
                        {
                            case 'tinyint':
                            case 'bit':
                            case 'bool':
                            case 'boolean':
                                if ($max_l == 1 && Xcrud_config::$make_checkbox)
                                {
                                    $this->field_type[$field_index] = 'bool';
                                } else
                                {
                                    $this->field_type[$field_index] = 'int';
                                }
                                break;
                            case 'smallint':
                            case 'mediumint':
                            case 'int':
                            case 'bigint':
                            case 'serial':
                                $this->field_type[$field_index] = 'int';
                                break;
                            case 'decimal':
                            case 'numeric':
                            case 'float':
                            case 'double':
                            case 'real':
                                $this->field_type[$field_index] = 'float';
                                break;
                            case 'char':
                            case 'varchar':
                            case 'binary':
                            case 'varbinary':
                            default:
                                $this->field_type[$field_index] = 'text';
                                break;
                            case 'text':
                            case 'tinytext':
                            case 'mediumtext':
                            case 'longtext':
                                if (!isset($this->no_editor[$field_index]))
                                    $this->field_type[$field_index] = 'texteditor';
                                else
                                    $this->field_type[$field_index] = 'textarea';
                                break;
                            case 'blob':
                            case 'tinyblob':
                            case 'mediumblob':
                            case 'longblob':
                                $this->field_type[$field_index] = 'binary';
                                break;
                            case 'date':
                                $this->field_type[$field_index] = 'date';
                                break;
                            case 'datetime':
                            case 'timestamp':
                                $this->field_type[$field_index] = 'datetime';
                                break;
                                /* case 'timestamp':
                                $this->field_type[$field_index] = 'timestamp';
                                break;*/
                            case 'time':
                                $this->field_type[$field_index] = 'time';
                                break;
                            case 'year':
                                $this->field_type[$field_index] = 'year';
                                break;
                            case 'enum':
                                $this->field_type[$field_index] = 'select';
                                $this->field_maxsize[$field_index] = $max_l;
                                break;
                            case 'set':
                                $this->field_type[$field_index] = 'multiselect';
                                $this->field_maxsize[$field_index] = $max_l;
                                break;
                        }
                    }
                    ######################
                }
            }
            if (!$this->columns or $this->reverse_columns)
                $this->columns = $columns;
            if (!$this->primary)
            {
                if ($uni)
                    $this->primary = $uni;
                else
                    if ($pri)
                        $this->primary = $pri;
                    else
                        $this->primary = $types_array[$this->table][0]['Field']; //if (!$this->show_primary_ai_column && isset($this->columns[$this->primary]))
                //    unset($this->columns[$this->primary]);
            }
        }
    }
    protected function _set_column_names()
    {
        $subselect_before = $this->subselect_before;
        foreach ($this->columns as $key => $col)
        {
            if ($name = array_search($key, $subselect_before))
            {
                $this->columns_names[$name] = $this->html_safe($this->labels[$name]);
                unset($subselect_before[$name]);
            }
            if (isset($this->labels[$key]))
                $this->columns_names[$key] = $this->html_safe($this->labels[$key]);
            else
                $this->columns_names[$key] = $this->html_safe($this->_humanize($col['column']));
        }
        if ($subselect_before)
        {
            foreach ($this->subselect_before as $name => $none)
            {
                $this->columns_names[$name] = $this->html_safe($this->labels[$name]);
                unset($subselect_before[$name]);
            }
        }
    }
    protected function _set_field_names()
    {
        foreach ($this->fields as $key => $field)
        {
            if (isset($this->labels[$key]))
                $this->fields_names[$key] = $this->html_safe($this->labels[$key]) . (isset($this->validation_required[$key]) ? '&#42;' :
                    '');
            else
                $this->fields_names[$key] = $this->html_safe($this->_humanize($field['field'])) . (isset($this->validation_required[$key]) ?
                    '&#42;' : '');
        }
    }
    protected function _render_list($list, $total)
    {
        if (count($this->order_by))
        {
            reset($this->order_by);
            $order_column = key($this->order_by);
            $order_direct = strtolower($this->order_by[$order_column]);
        } else
        {
            $order_column = $this->table . '.' . $this->primary;
            $order_direct = 'asc';
        }
        $start = $this->_post('start') ? $this->_post('start') : 0;
        $limit = $this->_post('limit') ? $this->_post('limit') : $this->limit;
        $total_columns = count($this->columns);
        if ($start > 0 && $start >= $total)
            $start = $total > $limit ? $total - $limit : 0;
        $start = ($limit != 'all') ? floor($start / $limit) * $limit : 'all';
        $phrase = $this->_post('phrase');
        $column = $this->_post('column');
        if (!$column)
        {
            if ($this->search_default)
                $column = $this->search_default;
            elseif ($this->search_columns)
            {
                $column = key($this->search_columns);
            } else
            {
                foreach ($this->columns_names as $column => $tmp)
                {
                    if (!$this->check_row_visibility($column))
                        continue;
                    else
                        break;
                }
            }
        }
        $pagination = $this->_pagination($total, $start, $limit);
        $this->_export_special();
        $view_file = $this->self_path . '/themes/' . $this->theme . '/xcrud_list_view.php';
        $this->check_file($view_file, 'render');
        ob_start();
        include ($view_file);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }
    protected function _render_edit($list, $mode)
    {
        $fields = array();
        $hidden_fields = array();
        if (count($this->order_by))
        {
            reset($this->order_by);
            $order_column = key($this->order_by);
            $order_direct = strtolower($this->order_by[$order_column]);
        } else
        {
            $order_column = $this->table . '.' . $this->primary;
            $order_direct = 'asc';
        }
        $order_column = $this->_post('order');
        $order_direct = $this->_post('direct');
        $start = $this->_post('start');
        $limit = $this->_post('limit');
        $total_columns = count($this->columns);
        $phrase = $this->_post('phrase');
        $column = $this->_post('column');
        $disabled = 'disabled_on_' . $mode;
        $readonly = 'readonly_on_' . $mode;
        $this->disabled = $this->$disabled;
        $this->readonly = $this->$readonly;
        if ($list)
        {
            $map_coords = array();
            foreach ($list as $key => $val)
            {
                if (isset($this->inner_table_instance[$key]) && $mode == 'edit')
                {
                    $this->inner_instances[$this->inner_table_instance[$key]] = Xcrud::get_instance($this->inner_table_instance[$key]);
                    $this->inner_instances[$this->inner_table_instance[$key]]->_import_vars();
                    $this->inner_instances[$this->inner_table_instance[$key]]->pass_default($this->inner_where[$key], $val);
                    if (strpos($val, ','))
                        $this->inner_instances[$this->inner_table_instance[$key]]->where($this->inner_where[$key], explode(',', trim($val, ',')), false,
                            'and', 'nt');
                    else
                        $this->inner_instances[$this->inner_table_instance[$key]]->where($this->inner_where[$key], $val, false, 'and', 'nt');
                }
                if ($key == 'primary_key')
                {
                    $this->primary_key = $val;
                    continue;
                }
                if ($this->field_type[$key] != 'hidden')
                {
                    if (isset($this->field_callback[$key]))
                    {
                        $path = $this->check_file($this->field_callback[$key]['path'], 'field_callback');
                        include_once ($path);
                        if (is_callable($this->field_callback[$key]['callback']))
                        {
                            $fields[] = array(
                                'label' => $this->fields_names[$key],
                                'field' => call_user_func_array($this->field_callback[$key]['callback'], array(
                                    $val,
                                    $key,
                                    $this->field_callback[$key]['real_name'],
                                    $this->field_callback[$key]['table'])),
                                'name' => $key);
                        }
                    } else
                    {
                        $func = 'create_' . $this->field_type[$key];
                        if (!method_exists($this, $func))
                            continue;
                        $fields[] = array(
                            'label' => $this->fields_names[$key],
                            'field' => $this->$func($key, $val),
                            'name' => $key);
                    }
                } else
                    $hidden_fields[] = $this->create_hidden($key, $val);
            }
            if (isset($this->field_params['googlemap']))
            {
                $fields[] = array(
                    'label' => $this->field_params['googlemap']['label'],
                    'field' => $this->crate_googlemap($this->field_params['googlemap']),
                    'name' => 'map');
            }
        }
        $this->_export_special();
        $view_file = $this->self_path . '/themes/' . $this->theme . '/xcrud_edit_view.php';
        $this->check_file($view_file, 'render');
        ob_start();
        include ($view_file);
        $out = ob_get_contents();
        ob_end_clean();
        if ($this->inner_instances && $mode == 'edit')
        {
            foreach ($this->inner_instances as $key => $instance)
            {
                $out .= '<div class="xcrud"><div class="xcrud-container">' . $instance->render_inner() . '</div></div>';
            }
        }
        return $out;
    }
    protected function _render_details($list, $mode)
    {
        $fields = array();
        $hidden_fields = array();
        if (count($this->order_by))
        {
            reset($this->order_by);
            $order_column = key($this->order_by);
            $order_direct = strtolower($this->order_by[$order_column]);
        } else
        {
            $order_column = $this->table . '.' . $this->primary;
            $order_direct = 'asc';
        }
        $order_column = $this->_post('order');
        $order_direct = $this->_post('direct');
        $start = $this->_post('start');
        $limit = $this->_post('limit');
        $phrase = $this->_post('phrase');
        $column = $this->_post('column');
        if ($list)
        {
            $map_coords = array();
            foreach ($list as $key => $val)
            {
                if (isset($this->inner_table_instance[$key]) && $mode == 'view')
                {
                    $this->inner_instances[$this->inner_table_instance[$key]] = Xcrud::get_instance($this->inner_table_instance[$key]);
                    $this->inner_instances[$this->inner_table_instance[$key]]->_import_vars();
                    $this->inner_instances[$this->inner_table_instance[$key]]->pass_default($this->inner_where[$key], $val);
                    if (strpos($val, ','))
                        $this->inner_instances[$this->inner_table_instance[$key]]->where($this->inner_where[$key], explode(',', trim($val, ',')), false,
                            'and', 'nt');
                    else
                        $this->inner_instances[$this->inner_table_instance[$key]]->where($this->inner_where[$key], $val, false, 'and', 'nt');
                    $this->inner_instances[$this->inner_table_instance[$key]]->unset_add()->unset_edit()->unset_remove();
                }
                if ($key == 'primary_key')
                {
                    $this->primary_key = $val;
                    continue;
                }
                if ($this->field_type[$key] != 'hidden')
                {
                    $fields[] = array(
                        'label' => $this->fields_names[$key],
                        'field' => $this->render_detail_field($key, $val),
                        'name' => $key);
                }
            }
            if (isset($this->field_params['googlemap']))
            {
                $fields[] = array(
                    'label' => $this->field_params['googlemap']['label'],
                    'field' => $this->crate_googlemap($this->field_params['googlemap'], 'view'),
                    'name' => 'map');
            }
        }
        $this->_export_special();
        $view_file = $this->self_path . '/themes/' . $this->theme . '/xcrud_detail_view.php';
        $this->check_file($view_file, 'render');
        ob_start();
        include ($view_file);
        $out = ob_get_contents();
        ob_end_clean();
        if ($this->inner_instances && $mode == 'view')
        {
            foreach ($this->inner_instances as $key => $instance)
            {
                $out .= '<div class="xcrud"><div class="xcrud-container">' . $instance->render_inner() . '</div></div>';
            }
        }
        return $out;
    }
    protected function _pagination($total, $start, $limit)
    {
        if ($total > $limit)
        {
            $pages = ceil($total / $limit);
            $curent = ceil(($start + $limit) / $limit);
            $links = array();
            for ($i = 1; $i <= $pages; ++$i)
            {
                $limit1 = $i * $limit - $limit;
                if ($i == $curent)
                    $links[$i] = '<li><span>' . $i . '</span></li>';
                else
                {
                    $links[$i] = '<li><a href="javascript:void(0);" data-start="' . $limit1 . '">' . $i . '</a></li>';
                }
            }

            $numlr = 2;
            $numpos = 10;
            $html = '<span class="xcrud-pagination pagination"><ul>';
            if ($pages > $numpos)
            {

                if ($curent <= $numlr + 3)
                {
                    for ($i = 1; $i <= $numpos - $numlr - 1; ++$i)
                    {
                        $html .= $links[$i];
                    }
                    $html .= '<li><span>&#133;</span></li>';
                    for ($i = $pages - $numlr + 1; $i <= $pages; ++$i)
                    {
                        $html .= $links[$i];
                    }
                } else
                    if ($curent >= $pages - $numlr - 2)
                    {
                        for ($i = 1; $i <= $numlr; ++$i)
                        {
                            $html .= $links[$i];
                        }
                        $html .= '<li><span>&#133;</span></li>';
                        for ($i = $pages - $numpos + $numlr + 2; $i <= $pages; ++$i)
                        {
                            $html .= $links[$i];
                        }
                    } else
                    {
                        for ($i = 1; $i <= $numlr; ++$i)
                        {
                            $html .= $links[$i];
                        }
                        $html .= '<li><span>&#133;</span></li>';
                        $offset = floor(($numpos - $numlr - $numlr - 1) / 2);
                        for ($i = $curent - $offset; $i <= $curent + $offset; ++$i)
                        {
                            $html .= $links[$i];
                        }

                        $html .= '<li><span>&#133;</span></li>';
                        for ($i = $pages - $numlr + 1; $i <= $pages; ++$i)
                        {
                            $html .= $links[$i];
                        }
                    }

            } else
            {
                $html .= implode('', $links);
            }
            $html .= '</ul></span>';
            return $html;
        }
    }
    protected function _cut($string, $len, $wordsafe = true, $dots = true)
    {
        $string = trim(strip_tags($string));
        $slen = mb_strlen($string, Xcrud_config::$mbencoding);
        if ($slen <= $len)
        {
            return $this->html_safe($string);
        }
        if ($wordsafe)
        {
            $end = $len;
            while ((mb_substr($string, --$len, 1, Xcrud_config::$mbencoding) != ' ') && ($len > 0))
            {
            }
            if ($len == 0)
            {
                $len = $end;
            }
            return $this->html_safe(mb_substr($string, 0, $len, Xcrud_config::$mbencoding)) . ($dots ? '&#133;' : '');
        }
        return $this->html_safe(mb_substr($string, 0, $len, Xcrud_config::$mbencoding)) . ($dots ? '&#133;' : '');
    }
    protected function _humanize($text)
    {
        return mb_convert_case(str_replace('_', ' ', $text), MB_CASE_TITLE, Xcrud_config::$mbencoding);
    }
    protected function _regenerate_key()
    {
        return sha1(microtime() . rand(0, 999));
    }
    protected function _export_inner_vars()
    {
        if ($this->inner_instances)
        {
            foreach ($this->inner_instances as $name => $instanse)
            {
                $instanse->_export_vars();
                if ($instanse->inner_instances)
                {
                    $instanse->_export_inner_vars();
                }
            }
        }
    }
    public function _export_vars($inst_name = '')
    {
        if (!$inst_name)
            $inst_name = $this->instance_name();
        $time = time();
        if ($this->instance_count == 1 && !$this->is_ajax_request && !$this->_post('instance'))
        {
            if (isset($_SESSION['xcrud_session']) && $_SESSION['xcrud_session'])
            {
                foreach ($_SESSION['xcrud_session'] as $s_key => $s_val)
                {
                    $old_time = isset($s_val['time']) ? (int)$s_val['time'] : 0;
                    if ($time > $old_time + 10)
                        unset($_SESSION['xcrud_session'][$s_key]);
                }
            }
        }
        $_SESSION['xcrud_session']['xcrud_' . $inst_name] = array(
            'key' => $this->key,
            'time' => $time,
            'table' => $this->table,
            'table_name' => $this->table_name,
            'where' => $this->where,
            'order_by' => $this->order_by,
            'join' => $this->join,
            'fields' => $this->fields,
            'labels' => $this->labels,
            'columns' => $this->columns,
            'columns_names' => $this->columns_names,
            'is_add' => $this->is_add,
            'is_edit' => $this->is_edit,
            'is_remove' => $this->is_remove,
            'is_csv' => $this->is_csv,
            'buttons' => $this->buttons,
            'validation_required' => $this->validation_required,
            'validation_pattern' => $this->validation_pattern,
            'before_insert' => $this->before_insert,
            'before_update' => $this->before_update,
            'before_remove' => $this->before_remove,
            'sess_expire' => $this->sess_expire,
            'after_insert' => $this->after_insert,
            'after_update' => $this->after_update,
            'after_remove' => $this->after_remove,
            'field_type' => $this->field_type,
            'field_maxsize' => $this->field_maxsize,
            'limit' => $this->limit,
            'limit_list' => $this->limit_list,
            'column_cut' => $this->column_cut,
            'no_editor' => $this->no_editor,
            'crud_url' => $this->crud_url,
            'show_primary_ai_field' => $this->show_primary_ai_field,
            'show_primary_ai_column' => $this->show_primary_ai_column,
            'disabled_on_create' => $this->disabled_on_create,
            'disabled_on_edit' => $this->disabled_on_edit,
            'readonly_on_create' => $this->readonly_on_create,
            'readonly_on_edit' => $this->readonly_on_edit,
            'benchmark' => $this->benchmark,
            'search_pattern' => $this->search_pattern,
            'connection' => $this->connection,
            'remove_confirm' => $this->remove_confirm,
            'upload_folder' => $this->upload_folder,
            'upload_config' => $this->upload_config,
            'pass_var_create' => $this->pass_var_create,
            'pass_var_edit' => $this->pass_var_edit,
            'reverse_columns' => $this->reverse_columns,
            'reverse_fields' => $this->reverse_fields,
            'no_quotes' => $this->no_quotes,
            'inner_table_instance' => $this->inner_table_instance,
            'inner_where' => $this->inner_where,
            'unique' => $this->unique,
            'theme' => $this->theme,
            'is_duplicate' => $this->is_duplicate,
            'links_label' => $this->links_label,
            'emails_label' => $this->emails_label,
            'sum' => $this->sum,
            'alert_create' => $this->alert_create,
            'alert_edit' => $this->alert_edit,
            'is_search' => $this->is_search,
            'is_print' => $this->is_print,
            'is_pagination' => $this->is_pagination,
            'subselect' => $this->subselect,
            'subselect_before' => $this->subselect_before,
            'highlight' => $this->highlight,
            'modal' => $this->modal,
            'column_class' => $this->column_class,
            'no_select' => $this->no_select,
            'is_inner' => $this->is_inner,
            'table_join' => $this->table_join,
            'is_title' => $this->is_title,
            'is_numbers' => $this->is_numbers,
            'language' => $this->language,
            'field_params' => $this->field_params,
            'mass_alert_create' => $this->mass_alert_create,
            'mass_alert_edit' => $this->mass_alert_edit,
            'column_callback' => $this->column_callback,
            'field_callback' => $this->field_callback,
            'replace_insert' => $this->replace_insert,
            'replace_update' => $this->replace_update,
            'replace_remove' => $this->replace_remove,
            'send_external_create' => $this->send_external_create,
            'send_external_edit' => $this->send_external_edit,
            'column_pattern' => $this->column_pattern,
            'field_tabs' => $this->field_tabs,
            'field_marker' => $this->field_marker,
            'is_view' => $this->is_view,
            'field_tooltip' => $this->field_tooltip,
            'table_tooltip' => $this->table_tooltip,
            'search_columns' => $this->search_columns,
            'search_default' => $this->search_default);
        $this->_export_special();
    }
    protected function _export_special()
    {
        $_SESSION['xcrud_session']['xcrud_' . $this->instance_name()] = array_merge($_SESSION['xcrud_session']['xcrud_' . $this->
            instance_name()], array(
            'upload_to_save' => $this->upload_to_save,
            'upload_to_remove' => $this->upload_to_remove,
            'primary_key' => $this->primary_key,
            'primary' => $this->primary,
            'defaults' => $this->defaults));
    }
    protected function _direct_export($param = '', $value = '')
    {
        $_SESSION['xcrud_session']['xcrud_' . $this->instance_name()][$param] = $value;
    }
    public function _import_vars($instanse_name = '')
    {
        if (!$instanse_name)
            $instanse_name = $this->instance_name();
        $this->table = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['table'];
        $this->table_name = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['table_name'];
        $this->primary = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['primary'];
        $this->where = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['where'];
        $this->order_by = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['order_by'];
        $this->join = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['join'];
        $this->fields = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['fields'];
        $this->labels = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['labels'];
        $this->columns = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['columns'];
        $this->columns_names = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['columns_names'];
        $this->is_add = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_add'];
        $this->is_edit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_edit'];
        $this->is_remove = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_remove'];
        $this->is_csv = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_csv'];
        $this->buttons = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['buttons'];
        $this->validation_required = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['validation_required'];
        $this->validation_pattern = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['validation_pattern'];
        $this->before_insert = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['before_insert'];
        $this->before_update = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['before_update'];
        $this->before_remove = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['before_remove'];
        $this->sess_expire = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['sess_expire'];
        $this->after_insert = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['after_insert'];
        $this->after_update = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['after_update'];
        $this->after_remove = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['after_remove'];
        $this->field_type = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['field_type'];
        $this->field_maxsize = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['field_maxsize'];
        $this->limit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['limit'];
        $this->limit_list = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['limit_list'];
        $this->column_cut = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['column_cut'];
        $this->no_editor = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['no_editor'];
        $this->crud_url = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['crud_url'];
        $this->show_primary_ai_field = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['show_primary_ai_field'];
        $this->show_primary_ai_column = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['show_primary_ai_column'];
        $this->disabled_on_create = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['disabled_on_create'];
        $this->disabled_on_edit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['disabled_on_edit'];
        $this->readonly_on_create = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['readonly_on_create'];
        $this->readonly_on_edit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['readonly_on_edit'];
        $this->benchmark = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['benchmark'];
        $this->search_pattern = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['search_pattern'];
        $this->connection = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['connection'];
        $this->remove_confirm = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['remove_confirm'];
        $this->upload_folder = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['upload_folder'];
        $this->upload_config = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['upload_config'];
        $this->upload_to_save = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['upload_to_save'];
        $this->upload_to_remove = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['upload_to_remove'];
        $this->primary_key = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['primary_key'];
        $this->defaults = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['defaults'];
        $this->pass_var_create = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['pass_var_create'];
        $this->pass_var_edit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['pass_var_edit'];
        $this->reverse_columns = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['reverse_columns'];
        $this->reverse_fields = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['reverse_fields'];
        $this->no_quotes = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['no_quotes'];
        $this->inner_table_instance = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['inner_table_instance'];
        $this->inner_where = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['inner_where'];
        $this->unique = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['unique'];
        $this->theme = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['theme'];
        $this->is_duplicate = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_duplicate'];
        $this->links_label = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['links_label'];
        $this->emails_label = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['emails_label'];
        $this->sum = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['sum'];
        $this->alert_create = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['alert_create'];
        $this->alert_edit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['alert_edit'];
        $this->is_search = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_search'];
        $this->is_print = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_print'];
        $this->is_pagination = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_pagination'];
        $this->subselect = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['subselect'];
        $this->subselect_before = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['subselect_before'];
        $this->highlight = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['highlight'];
        $this->modal = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['modal'];
        $this->column_class = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['column_class'];
        $this->no_select = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['no_select'];
        $this->is_inner = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_inner'];
        $this->table_join = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['table_join'];
        $this->is_title = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_title'];
        $this->is_numbers = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_numbers'];
        $this->language = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['language'];
        $this->field_params = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['field_params'];
        $this->mass_alert_create = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['mass_alert_create'];
        $this->mass_alert_edit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['mass_alert_edit'];
        $this->column_callback = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['column_callback'];
        $this->field_callback = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['field_callback'];
        $this->replace_insert = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['replace_insert'];
        $this->replace_update = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['replace_update'];
        $this->replace_remove = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['replace_remove'];
        $this->send_external_create = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['send_external_create'];
        $this->send_external_edit = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['send_external_edit'];
        $this->column_pattern = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['column_pattern'];
        $this->field_tabs = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['field_tabs'];
        $this->field_marker = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['field_marker'];
        $this->is_view = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['is_view'];
        $this->field_tooltip = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['field_tooltip'];
        $this->table_tooltip = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['table_tooltip'];
        $this->search_columns = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['search_columns'];
        $this->search_default = $_SESSION['xcrud_session']['xcrud_' . $instanse_name]['search_default'];
    }
    protected function create_bool($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $checked = (int)$value ? ' checked="checked"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        return '<input type="checkbox"' . $checked . ' value="1" name="' . $name . '" class="xcrud-input" data-type="bool" ' . $required .
            $readonly . $disabled . ' />';
    }
    protected function create_int($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $unique = isset($this->unique[$name]) ? ' unique' : '';
        return '<input type="text" value="' . (int)$value . '" name="' . $name . '" class="xcrud-input input-block-level' . $unique .
            '" data-type="int" data-pattern="integer" maxlength="' . $this->field_maxsize[$name] . '" ' . $required . $readonly . $disabled .
            ' />';
    }
    protected function create_float($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $unique = isset($this->unique[$name]) ? ' unique' : '';
        return '<input type="text" value="' . (float)$value . '" name="' . $name . '" class="xcrud-input input-block-level' . $unique .
            '" data-type="float" data-pattern="numeric" maxlength="' . $this->field_maxsize[$name] . '" ' . $required . $readonly .
            $disabled . ' />';
    }
    protected function create_price($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $unique = isset($this->unique[$name]) ? ' unique' : '';
        return '<input type="text" value="' . number_format($value, $this->field_maxsize[$name]['decimals'], '.', '') .
            '" name="' . $name . '" class="xcrud-input input-block-level' . $unique .
            '" data-type="price" data-pattern="numeric" maxlength="' . $this->field_maxsize[$name]['max'] . '" ' . $required . $readonly .
            $disabled . ' />';
    }
    protected function create_text($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $pattern = isset($this->validation_pattern[$name]) ? ' data-pattern="' . $this->validation_pattern[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $unique = isset($this->unique[$name]) ? ' unique' : '';
        return '<input type="text" value="' . $this->html_safe($value) . '" name="' . $name .
            '" class="xcrud-input input-block-level' . $unique . '" data-type="text" maxlength="' . $this->field_maxsize[$name] .
            '" ' . $required . $pattern . $readonly . $disabled . ' />';
    }
    protected function create_textarea($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $pattern = isset($this->validation_pattern[$name]) ? ' data-pattern="' . $this->validation_pattern[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        return '<textarea name="' . $name . '" rows="8" class="xcrud-input input-block-level" data-type="textarea" ' . $required .
            $pattern . $readonly . $disabled . '>' . $this->html_safe($value) . '</textarea>';
    }
    protected function create_texteditor($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $pattern = isset($this->validation_pattern[$name]) ? ' data-pattern="' . $this->validation_pattern[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        return '<textarea name="' . $name .
            '" rows="5" class="xcrud-input xcrud-texteditor input-block-level" data-type="texteditor" ' . $required . $pattern . $readonly .
            $disabled . '>' . $this->html_safe($value) . '</textarea>';
    }
    protected function create_date($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $range = '';
        $r = isset($this->field_maxsize[$name]) ? $this->field_maxsize[$name] : '';
        if ($r)
        {
            if (isset($r['range_end']))
            {
                $fdata = $this->_parse_field_names($r['range_end'], false, 'create_date');
                $range_end = $fdata[0]['table'] . '.' . $fdata[0]['field'];
                $range .= ' data-rangeend="' . $range_end . '" ';
            }
            if (isset($r['range_start']))
            {
                $fdata = $this->_parse_field_names($r['range_start'], false, 'create_date');
                $range_start = $fdata[0]['table'] . '.' . $fdata[0]['field'];
                $range .= ' data-rangestart="' . $range_start . '" ';
            }
        }
        return '<input type="text" value="' . $value . '" name="' . $name .
            '" class="xcrud-input xcrud-datepicker" data-type="date" maxlength="10" ' . $required . $readonly . $disabled . $range .
            ' />';
    }
    protected function create_datetime($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        return '<input type="text" value="' . $value . '" name="' . $name .
            '" class="xcrud-input xcrud-datepicker" data-type="datetime" maxlength="19" ' . $required . $readonly . $disabled .
            ' />';
    }
    protected function create_timestamp($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $value = $this->timestamp_to_datetime($value);
        return '<input type="text" value="' . $value . '" name="' . $name .
            '" class="xcrud-input xcrud-datepicker" data-type="timestamp" maxlength="11" ' . $required . $readonly . $disabled .
            ' />';
    }
    protected function create_time($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        return '<input type="text" value="' . $value . '" name="' . $name .
            '" class="xcrud-input xcrud-datepicker" data-type="time" maxlength="8" ' . $required . $readonly . $disabled . ' />';
    }
    protected function create_year($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        return '<input type="text" value="' . $value . '" name="' . $name .
            '" class="xcrud-input xcrud-datepicker" data-type="year" maxlength="4" ' . $required . $readonly . $disabled .
            ' data-pattern="natural" />';
    }
    protected function create_select($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        if (is_array($this->field_maxsize[$name]))
        {
            $out = '<select name="' . $name . '" ' . $required . $disabled . ' class="xcrud-input input-xlarge">';
            foreach ($this->field_maxsize[$name] as $optkey => $opt)
            {
                $selected = ($optkey == $value) ? ' selected="selected"' : '';
                $out .= '<option value="' . $optkey . '"' . $selected . '>' . $this->html_safe($opt) . '</option>';
            }
        } else
        {
            $tmp = explode(',', $this->field_maxsize[$name]);
            $out = '<select name="' . $name . '" ' . $required . $disabled . ' class="xcrud-input input-xlarge">';
            foreach ($tmp as $opt)
            {
                $opt = trim($opt, '\'');
                $selected = ($opt == $value) ? ' selected="selected"' : '';
                $out .= '<option value="' . $this->html_safe($opt) . '"' . $selected . '>' . $this->html_safe($opt) . '</option>';
            }
        }
        $out .= '</select>';
        return $out;
    }
    protected function create_multiselect($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $values = explode(',', $value);
        if (is_array($this->field_maxsize[$name]))
        {
            $size = count($this->field_maxsize[$name]) > 5 ? ' size="5"' : '';
            $out = '<select multiple="multiple" name="' . $name . '"' . $size . ' ' . $required . $disabled .
                ' class="xcrud-input input-xlarge">';
            foreach ($this->field_maxsize[$name] as $optkey => $opt)
            {
                $selected = (in_array($optkey, $values)) ? ' selected="selected"' : '';
                $out .= '<option value="' . $optkey . '"' . $selected . '>' . $opt . '</option>';
            }
        } else
        {
            $tmp = explode(',', $this->field_maxsize[$name]);
            $size = count($tmp) > 5 ? ' size="5"' : '';
            $out = '<select multiple="multiple" name="' . $name . '"' . $size . ' ' . $required . $disabled .
                ' class="xcrud-input input-xlarge">';
            foreach ($tmp as $opt)
            {
                $opt = trim($opt, '\'');
                $selected = (in_array($opt, $values)) ? ' selected="selected"' : '';
                $out .= '<option value="' . $this->html_safe($opt) . '"' . $selected . '>' . $this->html_safe($opt) . '</option>';
            }
        }

        $out .= '</select>';
        return $out;
    }
    protected function create_hidden($name, $value = '')
    {
        return '<input type="hidden" value="' . $this->html_safe($value) . '" name="' . $name . '" class="xcrud-input" />';
    }
    protected function create_password($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $pattern = isset($this->validation_pattern[$name]) ? ' data-pattern="' . $this->validation_pattern[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? ' readonly="readonly"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        return '<input type="password" value="" name="' . $name . '" class="xcrud-input" data-type="text" maxlength="' . $this->
            field_maxsize[$name] . '" ' . $required . $pattern . $readonly . $disabled . ' />';
    }
    protected function create_relation($name, $value = '', $dependval = false)
    {
        if (!isset($this->join[$name]))
        {
            return 'Restricted.';
        }
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $disabled = isset($this->disabled[$name]) ? ' disabled="disabled"' : '';
        $depend = $this->join[$name]['depend_on'] ? ' data-depend="' . $this->join[$name]['depend_on'] . '"' : '';
        $db = Xcrud_db::get_instance($this->connection);
        $where_arr = array();
        if ($this->join[$name]['multi'])
        {
            $multi = ' multiple="multiple" size="5"';
            $values = explode(',', $value);
        } else
        {
            $multi = '';
            $values = array($value);
        }
        if ($this->join[$name]['rel_where'])
        {
            if (is_array($this->join[$name]['rel_where']))
            {
                foreach ($this->join[$name]['rel_where'] as $field => $val)
                {
                    $where_arr[] = ($val ? (isset($this->no_quotes[$field]) ? $this->_prepare_field($field) . $db->escape($val, true) : $this->
                        _prepare_field($field) . $db->escape($val)) : $field);
                }
            } else
                $where_arr[] = $this->join[$name]['rel_where'];
        }
        if ($dependval !== false)
        {
            $where_arr[] = $this->_prepare_field($this->join[$name]['depend_field']) . $db->escape($dependval);
        }
        $out = '<select name="' . $name . '" ' . $required . $disabled . $multi . $depend . ' class="xcrud-input input-xlarge">';
        if ($this->join[$name]['depend_on'] && $dependval === false)
        {
            $options = false;
            if (Xcrud_config::$lists_null_opt)
                $out .= '<option value="' . $value . '" selected=""> ' . $this->lang('null_option') . ' </option>';
        } else
        {
            if ($where_arr)
                $where = 'WHERE ' . implode(' AND ', $where_arr);
            else
                $where = '';
            $db->query("SELECT `{$this->join[$name]['rel_field']}` AS `field`," . (is_array($this->join[$name]['rel_name']) ?
                "CONCAT_WS('{$this->join[$name]['rel_separator']}',`" . implode('`,`', $this->join[$name]['rel_name']) . "`)" : "`{$this->join[$name]['rel_name']}`") .
                " AS `name` " . $this->get_relation_tree_fields($this->join[$name]['tree']) . " FROM `{$this->join[$name]['rel_tbl']}` {$where} GROUP BY `field` ORDER BY " .
                $this->get_relation_ordering($this->join[$name]['tree']));
            $options = $this->resort_relation_opts($db->result(), $this->join[$name]['tree']);

            if (Xcrud_config::$lists_null_opt)
                $out .= '<option value=""> ' . $this->lang('null_option') . ' </option>';
        }
        if ($options)
        {
            foreach ($options as $opt)
            {
                $selected = (in_array($opt['field'], $values)) ? ' selected="selected"' : '';

                $out .= '<option value="' . $this->html_safe($opt['field']) . '"' . $selected . '>' . $this->html_safe($opt['name']) .
                    '</option>';
            }
        }
        $out .= '</select>';
        unset($options);
        return $out;
    }
    protected function get_relation_ordering($tree)
    {
        if ($tree && $tree['left_key'] && $tree['level_key'])
        {
            return '`' . $tree['left_key'] . '` ASC';
        } elseif ($tree && $tree['parent_key'])
        {
            return '`' . $tree['parent_key'] . '` ASC, `name` ASC';
        } else
            return '`name` ASC';
    }
    protected function get_relation_tree_fields($tree)
    {
        if ($tree && $tree['left_key'] && $tree['level_key'])
        {
            return ',`' . $tree['left_key'] . '`,`' . $tree['level_key'] . '`';
        } elseif ($tree && $tree['parent_key'])
        {
            return ',`' . $tree['parent_key'] . '`';
        } else
            return '';
    }
    protected function resort_relation_opts($options, $tree)
    {
        if ($tree && $tree['left_key'] && $tree['level_key'])
        {
            foreach ($options as $key => $opt)
            {
                $level = (int)$opt[$tree['level_key']];
                $out = '';
                for ($i = 0; $i < $level; ++$i)
                {
                    $out .= '. ';
                }
                if ($out)
                    $out .= ' └ ';
                $out .= $opt['name'];
                $options[$key]['name'] = $out;
            }
        } elseif ($tree && $tree['parent_key'])
        {
            $new_opts = array();
            foreach ($options as $key => $opt)
            {

            }
        }
        return $options;
    }
    protected function create_file($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? true : false;
        $disabled = isset($this->disabled[$name]) ? true : false;
        $btn_class = $this->theme == 'minimal' ? 'btn btn-small' : 'btn';
        $ext = trim(strtolower(strrchr($value, '.')), '.');
        $folder = $this->upload_folder[$name];
        $path = $this->check_folder($folder, 'file');
        $file_full_path = $path . '/' . $value;
        return '<div class="xcrud-file-container">
            <input type="hidden" name="' . $name . '" value="' . $value . '" class="xcrud-input" data-type="file" ' . $required .
            ' />
            ' . ($value ? '<a target="_blank" href="' . $this->file_link($name) . '" class="xcrud-file xcrud-' . trim($ext,
            '.') . '">' . $value . '</a>' . $this->_file_size($file_full_path) .
            '<a href="javascript:void(0);" class="xcrud-remove-file xcrud-action-button ' . $btn_class .
            ' btn-danger" data-type="file" data-file="' . $value . '" data-field="' . $name . '">' . $this->lang('remove') . '</a>' :
            '<span class="xcrud-nofile">' . $this->lang('no_file') . '</span>') . '
        </div>
        ' . (($readonly or $disabled) ? '' : '<span class="xcrud-add-file xcrud-action-button ' . $btn_class .
            ' btn-success"><i class="icon-upload icon-white"></i> ' . $this->lang('add_file') .
            '<input type="file" name="attach" id="' . str_replace('.', rand(10, 99), $name) . '-file" data-field="' . $name .
            '" data-type="file" class="xcrud-upload" value="" /></span>');
    }
    protected function create_image($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? true : false;
        $disabled = isset($this->disabled[$name]) ? true : false;
        $btn_class = $this->theme == 'minimal' ? 'btn btn-small' : 'btn';
        //$ext = trim(strtolower(strrchr($value, '.')), '.');
        return '<div class="xcrud-file-container">
            ' . ((isset($this->upload_config[$name]['blob']) && $this->upload_config[$name]['blob'] === true) ?
            '<input type="hidden" name="' . $name . '" value="' . ($value ? 'blob-storage' : '') .
            '" class="xcrud-input" data-type="file" ' . $required . ' />' : '<input type="hidden" name="' . $name . '" value="' . $value .
            '" class="xcrud-input" data-type="file" ' . $required . ' />') . ($value ?
            '<img class="xcrud-image img-polaroid" alt="" src="' . $this->file_link($name) . '" />
            <a href="javascript:void(0);" class="xcrud-remove-file xcrud-action-button ' . $btn_class .
            ' btn-danger" data-type="file" data-file="' . ((isset($this->upload_config[$name]['blob']) && $this->upload_config[$name]['blob']
            === true) ? 'blob-storage' : $value) . '" data-field="' . $name . '">' . $this->lang('remove') . '</a>' :
            '<span class="xcrud-noimage">' . $this->lang('no_image') . '</span>') . '
        </div>
        ' . (($readonly or $disabled) ? '' : '<span class="xcrud-add-file xcrud-action-button ' . $btn_class .
            ' btn-success"><i class="icon-upload icon-white"></i> ' . $this->lang('add_image') .
            '<input type="file" name="attach" id="' . str_replace('.', rand(10, 99), $name) . '-file" data-field="' . $name .
            '" data-type="image" accept="image/jpeg,image/png,image/gif" class="xcrud-upload" value="" /></span>');
    }
    protected function create_binary($name, $value = '')
    {
        return $value ? '[binary data]' : '[binary null]';
    }
    protected function create_remote_image($name, $value = '')
    {
        $required = isset($this->validation_required[$name]) ? ' data-required="' . $this->validation_required[$name] . '"' : '';
        $readonly = isset($this->readonly[$name]) ? true : false;
        $disabled = isset($this->disabled[$name]) ? true : false;
        $unique = isset($this->unique[$name]) ? ' unique' : '';
        return '<div class="xcrud-file-container">
            ' . ($value ? '<img class="xcrud-image img-polaroid" alt="" src="' . ($this->field_maxsize[$name] ? $this->
            field_maxsize[$name] : '') . $value . '" />' : '<span class="xcrud-noimage">' . $this->lang('no_file') . '</span>') . '
        </div>
        <input type="text" value="' . $this->html_safe($value) . '" name="' . $name .
            '" class="xcrud-input input-block-level' . $unique . '" data-type="text" maxlength="255" ' . $required . $readonly . $disabled .
            ' />';
    }

    /** in dev */
    protected function create_fk_relations($list)
    {
        if ($this->fk_join)
        {
            $db = Xcrud_db::get_instance($this->connection);
            foreach ($this->fk_join as $fk)
            {
                $fval = $list[$fk['field']];
                $where_arr = array();
                if ($fk['rel_where'])
                {
                    if (is_array($params['rel_where']))
                    {
                        foreach ($params['rel_where'] as $field => $val)
                        {
                            if ($val or is_int($val) or is_float($val))
                            {
                                $where_arr[] = "`a`." . $this->_prepare_field($field) . (isset($this->no_quotes[$field]) ? $db->escape($val, true) : $db->
                                    escape($val));
                            } else
                            {
                                $where_arr[] = $field;
                            }
                        }
                    } else
                        $where_arr[] = $params['rel_where'];
                }
                $db->query("SELECT `a`.`{$fk['rel_field']}`, `a`.`{$fk['rel_name']}`, IF(`b`.`{$fk['in_fk_field']}` = '{$fval}', 1, 0) AS `selected` 
                    FROM `{$fk['rel_table']}` AS `a` LEFT JOIN `{$fk['fk_table']}` AS `b` ON `a`.`{$fk['rel_field']}` = `b`.`{$fk['out_fk_field']}` 
                    WHERE `{$fk['in_fk_field']}` = '{$fval}' " . ($where_arr ? 'AND ' . implode(' AND ', $where_arr) :
                    ''))->result();
            }
        }
    }
    protected function benchmark_start()
    {
        if ($this->benchmark)
        {
            $start = explode(' ', microtime());
            $this->time_start = (float)($start[1] + $start[0]);
            $this->memory_start = memory_get_usage();
        }
    }
    protected function benchmark_end()
    {
        if ($this->benchmark)
        {
            $end = explode(' ', microtime());
            $this->time_end = (float)($end[1] + $end[0]);
            $this->memory_end = memory_get_usage();
            $out = $this->lang('exec_time') . ' ' . (number_format($this->time_end - $this->time_start, 3, '.', '')) . ' s';
            $out .= '<br />' . $this->lang('memory_usage') . ' ' . (number_format(($this->memory_end - $this->memory_start) / 1024 /
                1024, 2, '.', '')) . ' MB';
            return $out;
        }
    }
    protected function error($text = 'Error!')
    {
        exit('<div class="xcrud-error" style="padding:15px;color:#EE9401;margin:10px;border:1px solid#EE9401;border-radius:2px;">' .
            $text . '</div>');
    }
    protected function _upload()
    {
        switch ($this->_post('type'))
        {
            case 'image':
                return $this->_upload_image();
                break;
            case 'file':
                return $this->_upload_file();
                break;
            default:
                return $this->error('Upload Error');
                break;
        }
    }
    protected function _upload_file()
    {
        $field = $this->_post('field');
        $oldfile = $this->_post('oldfile') or 0;
        if (isset($_FILES) && isset($_FILES['attach']) && !$_FILES['attach']['error'])
        {
            $file = $_FILES['attach'];
            $ext = strtolower(strrchr($file['name'], '.'));
            if (isset($this->upload_config[$field]['not_rename']) && $this->upload_config[$field]['not_rename'] === true)
                $file_name = $this->_clean_file_name($file['name']);
            else
                $file_name = sha1(microtime()) . $ext;
            $folder = $this->upload_folder[$field];
            $path = $this->check_folder($folder, 'upload_file');
            $file_full_path = $path . '/' . $file_name;
            if (file_exists($file_full_path))
            {
                $file_name = substr_replace($file_name, date('ymdHis', time()), strrpos($file_name, '.'), 0);
                $file_full_path = $path . '/' . $file_name;
            }
            if ($oldfile != $file_name)
                $this->upload_to_remove[$oldfile] = $field;
            $this->upload_to_save[$file_name] = $field;
            $this->_export_special();
            move_uploaded_file($file['tmp_name'], $file_full_path);
            $btn_class = $this->theme == 'minimal' ? 'btn btn-small' : 'btn';
            $required = isset($this->validation_required[$field]) ? ' data-required="' . $this->validation_required[$name] . '"' :
                '';
            echo '<a target="_blank" href="' . $this->file_link($field /*, $file_name*/ ) . '" class="xcrud-file xcrud-' . trim($ext,
                '.') . '">' . $file_name . '</a>';
            echo $this->_file_size($file_full_path);
            echo '<a href="javascript:void(0);" class="xcrud-remove-file xcrud-action-button ' . $btn_class .
                ' btn-danger" data-type="file" data-file="' . $file_name . '" data-field="' . $field . '">' . $this->lang('remove') .
                '</a>';
            echo '<input type="hidden" name="' . $field . '" value="' . $file_name . '" class="xcrud-input" data-type="image" ' . $required .
                ' />';
            echo '<input type="hidden" name="new_key" value="' . $this->key . '" class="new_key" />';
        } else
            return $this->error('File is not uploaded');
    }
    protected function _upload_image()
    {
        $field = $this->_post('field');
        $oldfile = $this->_post('oldfile') or 0;
        if (isset($_FILES) && isset($_FILES['attach']) && !$_FILES['attach']['error'])
        {
            $file = $_FILES['attach']; //$ext = strtolower(strrchr($file['name'], '.'));
            //$file_name = sha1(microtime()) . $ext;
            $ext = strtolower(strrchr($file['name'], '.'));
            if (isset($this->upload_config[$field]['not_rename']) && $this->upload_config[$field]['not_rename'] === true)
                $file_name = $this->_clean_file_name($file['name']);
            else
                $file_name = sha1(microtime()) . $ext;
            $folder = $this->upload_folder[$field];
            $path = $this->check_folder($folder, 'upload_image');
            $file_full_path = $path . '/' . $file_name;
            if (file_exists($file_full_path))
            {
                $file_name = substr_replace($file_name, date('ymdHis', time()), strrpos($file_name, '.'), 0);
                $file_full_path = $path . '/' . $file_name;
            }
            $this->upload_to_remove[$oldfile] = $field;
            $this->upload_to_save[$file_name] = $field;
            $this->_export_special();
            move_uploaded_file($file['tmp_name'], $file_full_path);
            $btn_class = $this->theme == 'minimal' ? 'btn btn-small' : 'btn';
            $this->_try_change_size($field, $file_full_path);
            $this->_try_create_thumb($field, $file_full_path);
            $required = isset($this->validation_required[$field]) ? ' data-required="' . $this->validation_required[$field] . '"' :
                '';
            echo '<img class="xcrud-image img-polaroid" alt="" src="' . $this->file_link($field
                /*, ((isset($this->upload_config[$field]['blob']) &&
                $this->upload_config[$field]['blob'] === true) ? 'image.jpg' : $file_name)*/) . '" />';
            echo '<input type="hidden" name="' . $field . '" value="' . $file_name . '" class="xcrud-input" data-type="image" ' . $required .
                ' />';
            echo '<input type="hidden" name="new_key" value="' . $this->key . '" class="new_key" />';
            echo '<a href="javascript:void(0);" class="xcrud-remove-file xcrud-action-button ' . $btn_class .
                ' btn-danger" data-type="image" data-file="' . $file_name . '" data-field="' . $field . '">' . $this->lang('remove') .
                '</a>';
        } else
            return $this->error('File is not uploaded');
    }
    protected function _try_change_size($field, $imgpath)
    {
        $crop = (isset($this->upload_config[$field]['crop']) && $this->upload_config[$field]['crop']) ? true : false;
        $width = (isset($this->upload_config[$field]['width']) && $this->upload_config[$field]['width']) ? $this->upload_config[$field]['width'] : false;
        $height = (isset($this->upload_config[$field]['height']) && $this->upload_config[$field]['height']) ? $this->
            upload_config[$field]['height'] : false;
        if (isset($this->upload_config[$field]['save_orig']) && $this->upload_config[$field]['save_orig'])
        {
            $marker = (isset($this->upload_config[$field]['orig_marker']) && $this->upload_config[$field]['orig_marker']) ? $this->
                upload_config[$field]['orig_marker'] : '_orig';
            $orig_path = substr_replace($imgpath, $marker, mb_strrpos($imgpath, '.'), 0);
            if (!@copy($imgpath, $orig_path))
                $this->error('Can\'t save original image. Check directory permissions and paths.');
        }
        if ($crop && $width && $height)
        {
            $this->_image_crop($imgpath, $imgpath, $width, $height);
        } else
            if ($width or $height)
            {
                $this->_image_resize($imgpath, $imgpath, $width, $height);
            }
    }
    protected function _try_create_thumb($field, $imgpath)
    {
        $crop = (isset($this->upload_config[$field]['thumb_crop']) && $this->upload_config[$field]['thumb_crop']) ? true : false;
        $width = (isset($this->upload_config[$field]['thumb_width']) && $this->upload_config[$field]['thumb_width']) ? $this->
            upload_config[$field]['thumb_width'] : false;
        $height = (isset($this->upload_config[$field]['thumb_height']) && $this->upload_config[$field]['thumb_height']) ? $this->
            upload_config[$field]['thumb_height'] : false;
        $marker = (isset($this->upload_config[$field]['thumb_marker']) && $this->upload_config[$field]['thumb_marker']) ? $this->
            upload_config[$field]['thumb_marker'] : '_thumb';
        $thumb_path = substr_replace($imgpath, $marker, mb_strrpos($imgpath, '.'), 0);
        if ($crop && $width && $height)
        {
            $this->_image_crop($imgpath, $thumb_path, $width, $height);
        } else
            if ($width or $height)
            {
                $this->_image_resize($imgpath, $thumb_path, $width, $height);
            }
    }
    protected function _remove_upload()
    {
        switch ($this->_post('type'))
        {
            case 'image':
                return $this->_remove_image();
                break;
            case 'file':
                return $this->_remove_file();
                break;
            default:
                return $this->error('Remove Error');
                break;
        }
    }
    protected function _remove_file()
    {
        $field = $this->_post('field');
        $file = $this->_post('file');
        $this->upload_to_remove[$file] = $field;
        $this->_export_special();
        $required = isset($this->validation_required[$field]) ? ' data-required="' . $this->validation_required[$field] . '"' :
            '';
        echo '<input type="hidden" name="' . $field . '" value="" class="xcrud-input" data-type="file" ' . $required . ' />';
        echo '<input type="hidden" name="new_key" value="' . $this->key . '" class="new_key" />
            <span class="xcrud-nofile">' . $this->lang('no_file') . '</span>';
    }
    protected function _remove_image()
    {
        $field = $this->_post('field');
        $file = $this->_post('file');
        $required = isset($this->validation_required[$field]) ? ' data-required="' . $this->validation_required[$field] . '"' :
            '';
        $this->upload_to_remove[$file] = $field;
        $this->_export_special();
        echo '<input type="hidden" name="' . $field . '" value="" class="xcrud-input" data-type="image" ' . $required . ' />';
        echo '<input type="hidden" name="new_key" value="' . $this->key . '" class="new_key" />
            <span class="xcrud-noimage">' . $this->lang('no_image') . '</span>';
    }
    protected function _remove_and_save_uploads()
    {
        switch ($this->_post('task'))
        {

            case 'save':
                if (!$this->demo_mode)
                {
                    if ($this->upload_to_remove)
                    {
                        foreach ($this->upload_to_remove as $file => $field)
                        {
                            if ($file != 0)
                            {
                                $folder = $this->upload_folder[$field];
                                $path = $this->check_folder($folder, 'upload');
                                $file_full_path = $path . '/' . $file;
                                @unlink($file_full_path);
                                $marker = (isset($this->upload_config[$field]['thumb_marker']) && $this->upload_config[$field]['thumb_marker']) ? $this->
                                    upload_config[$field]['thumb_marker'] : '_thumb';
                                $thumb_path = substr_replace($file_full_path, $marker, mb_strrpos($file_full_path, '.'), 0);
                                @unlink($thumb_path);
                            }
                        }
                        $this->upload_to_save = array();
                        $this->upload_to_remove = array();
                    }
                }
                break;
            case 'list':
            case '':
                if ($this->upload_to_save)
                {
                    foreach ($this->upload_to_save as $file => $field)
                    {
                        $folder = $this->upload_folder[$field];
                        $path = $this->check_folder($folder, 'upload');
                        $file_full_path = $path . '/' . $file;
                        @unlink($file_full_path);
                        $marker = (isset($this->upload_config[$field]['thumb_marker']) && $this->upload_config[$field]['thumb_marker']) ? $this->
                            upload_config[$field]['thumb_marker'] : '_thumb';
                        $thumb_path = substr_replace($file_full_path, $marker, mb_strrpos($file_full_path, '.'), 0);
                        @unlink($thumb_path);
                    }
                    $f_bak = array();
                    foreach ($this->upload_to_remove as $file => $field)
                    {
                        if (!isset($f_bak[$field]))
                        {
                            $f_bak[$field] = true;
                            continue;
                        }
                        $folder = $this->upload_folder[$field];
                        $path = $this->check_folder($folder, 'upload');
                        $file_full_path = $path . '/' . $file;
                        @unlink($file_full_path);
                        $marker = (isset($this->upload_config[$field]['thumb_marker']) && $this->upload_config[$field]['thumb_marker']) ? $this->
                            upload_config[$field]['thumb_marker'] : '_thumb';
                        $thumb_path = substr_replace($file_full_path, $marker, mb_strrpos($file_full_path, '.'), 0);
                        @unlink($thumb_path);
                    }
                    $this->upload_to_save = array();
                    $this->upload_to_remove = array();
                }
                break;
        }
    }
    protected function _image_resize($src_file, $dest_file, $new_size_w = false, $new_size_h = false, $dest_qual = 92)
    {
        list($srcWidth, $srcHeight, $type) = getimagesize($src_file);
        switch ($type)
        {
            case 1:
                $srcHandle = imagecreatefromgif($src_file);
                break;
            case 2:
                $srcHandle = imagecreatefromjpeg($src_file);
                break;
            case 3:
                $srcHandle = imagecreatefrompng($src_file);
                break;
            default:
                $this->error('NO FILE');
                return false;
        }

        if ($srcWidth >= $srcHeight)
        {
            $ratio = (($new_size_w ? $srcWidth : $srcHeight) / ($new_size_w ? $new_size_w : $new_size_h));
            $ratio = max($ratio, 1.0);
            $destWidth = ($srcWidth / $ratio);
            $destHeight = ($srcHeight / $ratio);
            if ($destHeight > $new_size_h)
            {
                $ratio = ($destHeight / ($new_size_h ? $new_size_h : $new_size_w));
                $ratio = max($ratio, 1.0);
                $destWidth = ($destWidth / $ratio);
                $destHeight = ($destHeight / $ratio);
            }
        } elseif ($srcHeight > $srcWidth)
        {
            $ratio = (($new_size_h ? $srcHeight : $srcWidth) / ($new_size_h ? $new_size_h : $new_size_w));
            $ratio = max($ratio, 1.0);
            $destWidth = ($srcWidth / $ratio);
            $destHeight = ($srcHeight / $ratio);
            if ($destWidth > $new_size_w)
            {
                $ratio = ($destWidth / ($new_size_w ? $new_size_w : $new_size_h));
                $ratio = max($ratio, 1.0);
                $destWidth = ($destWidth / $ratio);
                $destHeight = ($destHeight / $ratio);
            }
        }
        $dstHandle = imagecreatetruecolor($destWidth, $destHeight);
        switch ($type)
        {
            case 1:
                $transparent_source_index = imagecolortransparent($srcHandle);
                if ($transparent_source_index !== -1)
                {
                    $transparent_color = imagecolorsforindex($srcHandle, $transparent_source_index);
                    $transparent_destination_index = imagecolorallocate($dstHandle, $transparent_color['red'], $transparent_color['green'],
                        $transparent_color['blue']);
                    imagecolortransparent($dstHandle, $transparent_destination_index);
                    imagefill($dstHandle, 0, 0, $transparent_destination_index);
                }
                break;
            case 3:
                imagealphablending($dstHandle, false);
                imagesavealpha($dstHandle, true);
                break;
        }
        imagecopyresampled($dstHandle, $srcHandle, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
        imagedestroy($srcHandle);
        switch ($type)
        {
            case 1:
                imagegif($dstHandle, $dest_file);
                break;
            case 2:
                imagejpeg($dstHandle, $dest_file, $dest_qual);
                break;
            case 3:
                imagepng($dstHandle, $dest_file);
                break;
            default:
                $this->error('File Type Not Supported!');
                return false;
        }
        imagedestroy($dstHandle);
        $newimgarray = array($destWidth, $destHeight);
        return $newimgarray;
    }
    protected function _image_crop($src_file, $dest_file, $new_size_w, $new_size_h, $dest_qual = 92)
    {
        list($srcWidth, $srcHeight, $type) = getimagesize($src_file);
        switch ($type)
        {
            case 1:
                $srcHandle = imagecreatefromgif($src_file);
                break;
            case 2:
                $srcHandle = imagecreatefromjpeg($src_file);
                break;
            case 3:
                $srcHandle = imagecreatefrompng($src_file);
                break;
            default:
                $this->error('NO FILE');
                return false;
        }
        if (!$srcHandle)
        {
            $this->error('Could not execute imagecreatefrom() function! ');
            return false;
        }
        if ($srcHeight < $srcWidth)
        {
            $ratio = (double)($srcHeight / $new_size_h);
            $cpyWidth = round($new_size_w * $ratio);
            if ($cpyWidth > $srcWidth)
            {
                $ratio = (double)($srcWidth / $new_size_w);
                $cpyWidth = $srcWidth;
                $cpyHeight = round($new_size_h * $ratio);
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            } else
            {
                $cpyHeight = $srcHeight;
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
                $yOffset = 0;
            }
        } else
        {
            $ratio = (double)($srcWidth / $new_size_w);
            $cpyHeight = round($new_size_h * $ratio);
            if ($cpyHeight > $srcHeight)
            {
                $ratio = (double)($srcHeight / $new_size_h);
                $cpyHeight = $srcHeight;
                $cpyWidth = round($new_size_w * $ratio);
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
                $yOffset = 0;
            } else
            {
                $cpyWidth = $srcWidth;
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            }
        }
        $dstHandle = ImageCreateTrueColor($new_size_w, $new_size_h);
        switch ($type)
        {
            case 1:
                $transparent_source_index = imagecolortransparent($srcHandle);
                if ($transparent_source_index !== -1)
                {
                    $transparent_color = imagecolorsforindex($srcHandle, $transparent_source_index);
                    $transparent_destination_index = imagecolorallocate($dstHandle, $transparent_color['red'], $transparent_color['green'],
                        $transparent_color['blue']);
                    imagecolortransparent($dstHandle, $transparent_destination_index);
                    imagefill($dstHandle, 0, 0, $transparent_destination_index);
                }
                break;
            case 3:
                imagealphablending($dstHandle, false);
                imagesavealpha($dstHandle, true);
                break;
        }
        if (!imagecopyresampled($dstHandle, $srcHandle, 0, 0, $xOffset, $yOffset, $new_size_w, $new_size_h, $cpyWidth, $cpyHeight))
        {
            $this->error('Could not execute imagecopyresampled() function!');
            return false;
        }
        imagedestroy($srcHandle);
        switch ($type)
        {
            case 1:
                imagegif($dstHandle, $dest_file);
                break;
            case 2:
                imagejpeg($dstHandle, $dest_file, $dest_qual);
                break;
            case 3:
                imagepng($dstHandle, $dest_file);
                break;
            default:
                $this->error('File Type Not Supported!');
                return false;
        }
        imagedestroy($dstHandle);
        return true;
    }
    protected function _sort_defaults()
    {
        $new_defaults = array();
        foreach ($this->fields as $field => $params)
        {
            $new_defaults[$field] = isset($this->defaults[$field]) ? $this->defaults[$field] : '';
        }
        $this->defaults = $new_defaults;
    }
    protected function _clean_file_name($txt)
    {
        $replace = array(
            'Š' => 'S',
            'Œ' => 'O',
            'Ž' => 'Z',
            'š' => 's',
            'œ' => 'oe',
            'ž' => 'z',
            'Ÿ' => 'Y',
            '¥' => 'Y',
            'µ' => 'u',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'І' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'і' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ý' => 'y',
            'ÿ' => 'y',
            'ă' => 'a',
            'ş' => 's',
            'ţ' => 't',
            'ț' => 't',
            'Ț' => 'T',
            'Ș' => 'S',
            'ș' => 's',
            'Ş' => 'S',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'E',
            'Ж' => 'J',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'I',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'SH',
            'Ы' => 'Y',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'j',
            'з' => 'z',
            'и' => 'i',
            'й' => 'i',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'H',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sh',
            'ы' => 'y',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            'Ā' => 'A',
            'ā' => 'a',
            'Č' => 'C',
            'č' => 'c',
            'Ē' => 'E',
            'ē' => 'e',
            'Ģ' => 'G',
            'ģ' => 'g',
            'Ī' => 'I',
            'ī' => 'i',
            'Ķ' => 'K',
            'ķ' => 'k',
            'Ļ' => 'L',
            'ļ' => 'l',
            'Ņ' => 'N',
            'ņ' => 'n',
            'Ū' => 'U',
            'ū' => 'u',
            ' ' => '_');
        $txt = str_replace(array_keys($replace), array_values($replace), $txt);
        $txt = preg_replace('/[^a-zA-Z0-9_\-\.]+/', '', $txt);
        return $txt;
    }
    protected function _file_size($path)
    {
        return number_format(is_file($path) ? filesize($path) / 1024 : 0, 2, '.', ' ') . ' KB';
    }
    protected function _prepare_field($field)
    {
        preg_match_all('/([^<>!=]+)/', $field, $matches);
        preg_match_all('/([<>!=]+)/', $field, $matches2);
        return '`' . trim($matches[0][0]) . '`' . ($matches2[0] ? implode('', $matches2[0]) : '=');
    }
    protected function _prepare_field_in($field)
    {
        preg_match_all('/([^!]+)/', $field, $matches);
        preg_match_all('/([!]+)/', $field, $matches2);
        return '`' . trim($matches[0][0]) . '`' . ($matches2[0] ? ' NOT IN' : ' IN');
    }
    protected function _compare($val1, $operator, $val2)
    {
        switch ($operator)
        {
            case '=':
                return ($val1 == $val2) ? true : false;
            case '>':
                return ($val1 > $val2) ? true : false;
            case '<':
                return ($val1 < $val2) ? true : false;
            case '>=':
                return ($val1 >= $val2) ? true : false;
            case '<=':
                return ($val1 <= $val2) ? true : false;
            case '!=':
                return ($val1 != $val2) ? true : false;
            case '^=':
                return (mb_strpos($val1, $val2, 0, Xcrud_config::$mbencoding) === 0) ? true : false;
            case '$=':
                return (mb_strpos($val1, $val2, 0, Xcrud_config::$mbencoding) == (mb_strlen($val1, Xcrud_config::$mbencoding) -
                    mb_strlen($val2, Xcrud_config::$mbencoding))) ? true : false;
            case '~=':
                return (mb_strpos($val1, $val2, 0, Xcrud_config::$mbencoding) !== false) ? true : false;
            default:
                return false;
        }
    }
    protected function _render_list_item($field, $value, $primary_key, $row)
    {
        $modal = '';
        if (isset($this->column_callback[$field]))
        {
            $path = $this->check_file($this->column_callback[$field]['path'], 'column_callback');
            include_once ($path);
            if (is_callable($this->column_callback[$field]['callback']) && $row)
            {
                return call_user_func_array($this->column_callback[$field]['callback'], array(
                    $value,
                    $field,
                    $this->column_callback[$field]['real_name'],
                    $this->column_callback[$field]['table'],
                    $primary_key,
                    $row));
            }
        }
        if (isset($this->column_pattern[$field]))
        {
            return $this->replace_text_variables($this->column_pattern[$field], $row, true);
        }
        if ($this->modal && $value)
        {
            if (isset($this->modal[$field]))
            {
                switch ($this->field_type[$field])
                {
                    case 'image':
                        $modal = '<a class="xcrud_modal" href="javascript:void(0);" data-header="' . $this->html_safe($this->columns_names[$field]) .
                            '" data-content="<img class=\'xcrud-image\' alt=\'\' src=\'' . $this->file_link($field, $primary_key, false) . '\' />">' . (Xcrud_config::
                            $images_in_grid ? '<img class="xcrud-image" style="max-height: ' . Xcrud_config::$images_in_grid_height .
                            'px;" alt="" src="' . $this->file_link($field, $primary_key, ((isset($this->upload_config[$field]['thumb_width'])) or
                            isset($this->upload_config[$field]['thumb_width'])) ? true : false) . '" />' : '<i class="' . $this->modal[$field] .
                            '"></i>') . '</a>';
                        break;
                    case 'remote_image':
                        $modal = '<a class="xcrud_modal" href="javascript:void(0);" data-header="' . $this->html_safe($this->columns_names[$field]) .
                            '" data-content="<img class=\'xcrud-image\' alt=\'\' src=\'' . $value . '\' />">' . (Xcrud_config::$images_in_grid ?
                            '<img class="xcrud-image" style="max-height: ' . Xcrud_config::$images_in_grid_height . 'px;" alt="" src="' . $value .
                            '" />' : '<i class="' . $this->modal[$field] . '"></i>') . '</a>';
                        break;
                    default:
                        $modal = '<a class="xcrud_modal" href="javascript:void(0);" data-header="' . $this->html_safe($this->columns_names[$field]) .
                            '" data-content="' . $this->html_safe($value) . '"><i class="' . $this->modal[$field] . '"></i></a>';
                        break;
                }
            }
        }
        if (isset($this->field_type[$field]))
        {
            if ($this->field_type[$field] == 'select' && is_array($this->field_maxsize[$field]))
                return $this->field_maxsize[$field][$value];
            if ($this->field_type[$field] == 'timestamp')
                return $this->timestamp_to_datetime($value);
            if ($this->field_type[$field] == 'bool')
                return ($value) ? $this->lang('bool_on') : $this->lang('bool_off');
            if ($this->field_type[$field] == 'price')
                return $this->field_maxsize[$field]['prefix'] . number_format((float)$value, (int)$this->field_maxsize[$field]['decimals'],
                    '.', $this->field_maxsize[$field]['separator']) . $this->field_maxsize[$field]['suffix'];
            if (Xcrud_config::$images_in_grid && $this->field_type[$field] == 'image' && $value)
            {
                return $modal ? $modal : '<img class="xcrud-image" style="max-height: ' . Xcrud_config::$images_in_grid_height .
                    'px;" alt="" src="' . $this->file_link($field, $primary_key, ((isset($this->upload_config[$field]['thumb_width'])) or
                    isset($this->upload_config[$field]['thumb_width'])) ? true : false) . '" />';
            }
            if (Xcrud_config::$images_in_grid && $this->field_type[$field] == 'remote_image' && $value)
            {
                return $modal ? $modal : '<img class="xcrud-image" style="max-height: ' . Xcrud_config::$images_in_grid_height .
                    'px;" alt="" src="' . $value . '" />';
            }
            if (($this->field_type[$field] == 'file' or $this->field_type[$field] == 'image') && $value)
            {
                $ext = trim(strtolower(strrchr($value, '.')), '.');
                return $modal ? $modal : '<a target="_blank" href="' . $this->file_link($field, $primary_key) .
                    '" class="xcrud-file xcrud-' . trim($ext, '.') . '">' . (isset($this->upload_config[$field]['text']) ? $this->
                    upload_config[$field]['text'] : $value) . '</a>';
            }
            if (Xcrud_config::$clickable_list_links && $this->field_type[$field] == 'text')
            {
                if (mb_strpos($value, 'http', 0, Xcrud_config::$mbencoding) !== false)
                    return '<a target="_blank" href="' . $value . '">' . ($this->links_label ? $this->links_label['text'] : $this->_cut($value,
                        $this->column_cut)) . '</a>';
                elseif (mb_strpos($value, '@', 0, Xcrud_config::$mbencoding) !== false)
                    return '<a target="_blank" href="mailto:' . $value . '">' . ($this->emails_label ? $this->emails_label['text'] : $this->
                        _cut($value, $this->column_cut)) . '</a>';
            }
            if (isset($this->binary[$field]))
            {
                return ($value) ? '[binary data]' : '';
            }
            return $modal ? $modal : $this->_cut($value, $this->column_cut);
        }
        return $modal ? $modal : $this->_cut($value, $this->column_cut);
    }
    protected function _render_list_buttons(&$row)
    {
        $out = '';
        $is_bootstrap = (Xcrud_config::$load_bootstrap or $this->theme != 'default') ? true : false;
        $use_btn = $this->theme == 'minimal' ? false : true;
        $icon_white = $this->theme == 'bootstrap' ? ' icon-white' : '';
        if ($this->buttons)
        {
            foreach ($this->buttons as $button)
            {
                $href = array();
                if ($button['params'])
                {
                    foreach ($button['params'] as $key => $value)
                    {
                        $href[] = rawurlencode($value['name']) . '=' . rawurlencode($row[$key]);
                    }
                }
                $button['link'] = $this->replace_text_variables($button['link'], $row, true);
                $out .= '<a class="xcrud-button-link ' . (($is_bootstrap && $use_btn && !$button['class']) ? ' btn btn-small ' : '') . $button['class'] .
                    '" title="' . $this->html_safe($button['name']) . '" href="' . $button['link'] . ($href ? ((mb_strpos($button['link'],
                    '?', 0, Xcrud_config::$mbencoding) === false) ? '?' : '&amp;') . implode('&amp;', $href) : '') . '"><i class="' . ($button['icon'] ?
                    $button['icon'] : 'icon-globe') . '"></i></a> ';
            }
        }
        $out .= $this->is_duplicate ? '<a class="xcrud-clone' . ($is_bootstrap && $use_btn ? ' btn btn-inverse btn-small' : '') .
            '" data-primary="' . ($row['primary_key']) . '" title="' . $this->lang('duplicate') .
            '" href="javascript:void(0);"><i class="icon-plus' . $icon_white . '"></i></a>' : '';
        $out .= $this->is_view ? ' <a class="xcrud-detail-view' . ($is_bootstrap && $use_btn ? ' btn btn-info btn-small' : '') .
            '" data-primary="' . ($row['primary_key']) . '" title="' . $this->lang('view') .
            '" href="javascript:void(0);"><i class="icon-search' . $icon_white . '"></i></a>' : '';
        $out .= $this->is_edit ? ' <a class="xcrud-edit' . ($is_bootstrap && $use_btn ? ' btn btn-warning btn-small' : '') .
            '" data-primary="' . ($row['primary_key']) . '" title="' . $this->lang('edit') .
            '" href="javascript:void(0);"><i class="icon-edit' . $icon_white . '"></i></a>' : '';
        $out .= $this->is_remove ? ' <a class="xcrud-remove' . ($is_bootstrap && $use_btn ? ' btn btn-danger btn-small' : '') . ($this->
            remove_confirm ? ' xcrud-confirm' : '') . '" data-primary="' . ($row['primary_key']) . '" title="' . $this->lang('remove') .
            '" href="javascript:void(0);"><i class="icon-remove' . $icon_white . '"></i></a>' : '';
        return $out;
    }
    protected function render_sum_item($field)
    {
        if (isset($this->sum_row[$field]))
        {
            if ($this->sum[$field]['custom'])
            {
                return str_replace('{value}', $this->_render_list_item($field, $this->sum_row[$field], 0, null), $this->sum[$field]['custom']);
            } else
            {
                return $this->_render_list_item($field, $this->sum_row[$field], 0, null);
            }
        } else
            return '&nbsp;';
    }

    protected function _check_unique_value()
    {
        $db = Xcrud_db::get_instance($this->connection);
        if (!$this->primary)
            $this->_set_columns($this->_table_info());
        $p_where = '';
        $field = $this->_post('field');
        $value = $db->escape($this->_post('value'));
        $tmp = explode('.', $field);
        if ($this->_post('primary'))
        {
            $primary = $db->escape($this->_post('primary'));
            $p_where = "AND `{$this->table}`.`{$this->primary}` != {$primary}";
        } elseif (!$this->primary_ai && "{$this->table}.{$this->primary}" == $field && !$this->_post('value'))
        {
            $data['unique'] = 0;
            $data['key'] = $this->key;
            $this->_export_special();
            echo json_encode($data);
            return;
        }

        $table_join = $this->_build_table_join();
        $db->query("SELECT COUNT(`{$this->table}`.`{$this->primary}`) AS `count` \r\n FROM `{$this->table}`\r\n {$table_join}\r\n 
                WHERE `{$tmp[0]}`.`{$tmp[1]}` = {$value} {$p_where}");
        $list = $db->row();
        $data = array();
        $data['unique'] = ($list['count'] > 0) ? 0 : 1;
        $data['key'] = $this->key;
        $this->_export_special();
        echo json_encode($data);
    }
    protected function _check_url($url)
    {
        if (!$url)
            return false;
        $host = trim($_SERVER['HTTP_HOST'], '/');
        $scheme = (!isset($_SERVER['HTTPS']) or !$_SERVER['HTTPS'] or strtolower($_SERVER['HTTPS']) == 'off' or strtolower($_SERVER['HTTPS']) ==
            'no') ? 'http://' : 'https://';

        $sh_pos = mb_strpos($url, '://', 0, Xcrud_config::$mbencoding);
        if ($sh_pos !== false)
        {
            $url = trim(mb_substr($url, $sh_pos + 3), '/');
            $eh_pos = mb_strpos($url, '/', 0, Xcrud_config::$mbencoding);
            $host_len = mb_strlen($host, Xcrud_config::$mbencoding);
            $u_host = ($eh_pos === false) ? $url : mb_substr($url, 0, $eh_pos, Xcrud_config::$mbencoding);
            if (ltrim($host, 'w.') == ltrim($u_host, 'w.')) // www fix

                return $scheme . $host . '/' . trim(mb_substr($url, $eh_pos), '/');
            else
                return $scheme . trim($url, '/');
        } else
            return $scheme . $host . '/' . trim($url, '/');
    }
    protected function file_link($field, $primary_key = false, $thumb = false)
    {
        return $this->crud_url . '/xcrud_image.php?instance=' . $this->instance_name() . '&amp;field=' . $field . '&amp;key=' .
            $this->key . (Xcrud_config::$dynamic_session ? '&amp;sess_name=' . $this->sess_name : '') . ($primary_key ?
            '&amp;primary_key=' . $primary_key : '') . ($thumb ? '&amp;thumb=true' : '');
    }
    protected function html_safe($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, Xcrud_config::$mbencoding);
    }
    protected function _clone_row($primary, $types_array)
    {
        if (is_array($types_array) && count($types_array))
        {
            $columns = array();
            $this->primary_ai = false;
            foreach ($types_array as $table => $types)
            {
                foreach ($types as $row)
                {
                    $field_index = "{$table}.{$row['Field']}";
                    if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment')
                    {
                        if ($table == $this->table)
                            $this->primary_ai = "`{$table}`.`{$row['Field']}`";
                    } elseif ($row['Key'] == 'UNI' && $row['Key'] == 'PRI')
                    {
                        $this->error('Duplication impossible. The table has a unique field.');
                    } else
                    {
                        $columns[$field_index] = array('table' => $table, 'field' => $row['Field']);
                    }
                }
            }
            if (!$this->primary_ai)
                $this->error('Duplication impossible. Table does not have a primary autoincrement field.');
            $select = $this->_build_select_clone($columns);
            $where = $this->_build_where();
            $table_join = $this->_build_table_join();
            $where_ai = $where ? "AND {$this->primary_ai} = " . (int)$primary : "WHERE {$this->primary_ai} = " . (int)$primary;
            $db = Xcrud_db::get_instance($this->connection);
            $db->query("SELECT {$select}\r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n {$where_ai} LIMIT 1");
            $postdata = $db->row();
            if ($this->pass_var_create)
            {
                foreach ($this->pass_var_create as $field => $value)
                {
                    $postdata[$field] = $value;
                }
            }
            if (!$this->demo_mode)
                $this->_insert($postdata, true, $columns);
        }
    }
    protected function _build_select_clone($columns)
    {
        $fields = array();
        foreach ($columns as $key => $val)
        {
            if ($val)
                $fields[] = "`{$val['table']}`.`{$val['field']}` AS `$key`";
        }
        return implode(',', $fields);
    }
    protected function send_email($to, $subject = '(No subject)', $message = '', $cc = array(), $html = true)
    {
        $header = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/' . ($html ? 'html' : 'plain') . '; charset=UTF-8' . "\r\n" .
            'From: ' . Xcrud_config::$email_from_name . ' <' . Xcrud_config::$email_from . ">\r\n";
        if ($cc)
            $header .= 'Cc: ' . implode(',', $cc) . "\r\n";
        if ($html)
            $message = '<!DOCTYPE HTML><html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>' . $subject .
                '</title></head><body>' . $message . '</body></html>';
        mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $header);
    }
    protected function _cell_attrib($field, $value, $order, $is_sum = false)
    {
        $out = '';
        if (isset($this->column_class[$field]))
            $column_class = $this->column_class[$field];
        else
            $column_class = array();
        if ($field == $order)
            $column_class[] = 'xcrud-current';
        if ($is_sum)
            $column_class[] = 'xcrud-sum';
        if (isset($this->highlight[$field]))
        {
            foreach ($this->highlight[$field] as $params)
            {
                if ($this->_compare($value, $params['operator'], $params['value']))
                {
                    if ($params['color'])
                        $out .= ' style="background-color: ' . $params['color'] . ';"';
                    if ($params['class']) //$out .= ' class="' . $params['class'] . '"';

                        $column_class[] = $params['class'];
                }
            }
        }
        if ($column_class)
        {
            $column_class = array_unique($column_class);
            $out .= ' class="' . implode(' ', $column_class) . '"';
        }
        return $out;
    }
    protected function check_row_visibility($field)
    {
        if ($field == 'primary_key' /*or isset($this->no_select[$field]) */ or (isset($this->field_type[$field]) && ($this->
            field_type[$field] == 'password' or $this->field_type[$field] == 'hidden')))
            return false;
        else
        {
            return true;
        }
    }
    protected function _get_table($method)
    {
        if (!$this->table)
            $this->error('You must define your table before using the <strong>' . $method . '</strong> method.');
        else
            return $this->table;
        return false;
    }
    protected function _get_language()
    {
        if (file_exists($this->self_path . '/languages/' . $this->language . '.ini'))
            $this->lang_arr = parse_ini_file($this->self_path . '/languages/' . $this->language . '.ini');
        elseif (file_exists($this->self_path . '/languages/en.ini'))
            $this->lang_arr = parse_ini_file($this->self_path . '/languages/en.ini');
    }
    protected function lang($text = '')
    {
        $text = mb_convert_case($text, MB_CASE_LOWER, Xcrud_config::$mbencoding);
        return htmlspecialchars((isset($this->lang_arr[$text]) ? $this->lang_arr[$text] : $text), ENT_QUOTES, Xcrud_config::$mbencoding);
    }
    protected function _thumb_name($key, $val)
    {
        if ($key && $val)
        {
            $marker = (isset($this->upload_config[$key]['thumb_marker']) && $this->upload_config[$key]['thumb_marker']) ? $this->
                upload_config[$key]['thumb_marker'] : '_thumb';
            return substr_replace($val, $marker, strrpos($val, '.'), 0);
        }
    }
    protected function _is_thumb($field)
    {
        if (isset($this->upload_config[$field]['thumb_width']) && $this->upload_config[$field]['thumb_width'] && isset($this->
            upload_config[$field]['thumb_height']) && $this->upload_config[$field]['thumb_height'])
            return true;
        else
            return false;
    }
    protected function _parse_field_names($fields = '', $table = false, $location = '')
    {
        $field_names = array();
        if ($fields)
        {
            $table = $table ? $table : $this->_get_table($location);
            if (is_array($fields))
            {
                foreach ($fields as $key => $val)
                {
                    if (is_int($key))
                    {
                        if (!strpos($val, '.'))
                            $field_names[] = array('table' => $table, 'field' => $val);
                        else
                        {
                            $tmp = explode('.', $val);
                            $field_names[] = array('table' => $tmp[0], 'field' => $tmp[1]);
                            unset($tmp);
                        }
                    } else
                    {
                        if (!strpos($key, '.'))
                            $field_names[] = array(
                                'table' => $table,
                                'field' => $key,
                                'value' => $val);
                        else
                        {
                            $tmp = explode('.', $key);
                            $field_names[] = array(
                                'table' => $tmp[0],
                                'field' => $tmp[1],
                                'value' => $val);
                            unset($tmp);
                        }
                    }
                }
            } else
            {
                $fields = explode(',', $fields);
                foreach ($fields as $key => $val)
                {
                    $val = trim($val);
                    if (!strpos($val, '.'))
                        $field_names[] = array('table' => $table, 'field' => $val);
                    else
                    {
                        $tmp = explode('.', $val);
                        $field_names[] = array('table' => $tmp[0], 'field' => $tmp[1]);
                        unset($tmp);
                    }
                }
            }
            unset($fields);
        } else
            $this->error('You must set field name(s) for the <strong>' . $location . '</strong> method.');
        return $field_names;
    }
    protected function load_css($ignore_instance = false)
    {
        $out = '';
        if (!self::$head_loaded)
        {
            if (($this->instance_count == 1 or $ignore_instance) && !Xcrud_config::$disable_plugins)
            {
                if (Xcrud_config::$load_bootstrap && $this->theme != 'default')
                    $out .= '<link href="' . $this->crud_url .
                        '/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />';
                if (!$this->disable_jquery_ui)
                    $out .= '<link href="' . $this->crud_url . '/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />';
                $out .= '<link href="' . $this->crud_url .
                    '/plugins/timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css" /><link href="' . $this->crud_url .
                    '/themes/' . $this->theme . '/xcrud.css" rel="stylesheet" type="text/css" />';
            }
        }
        return $out;
    }
    protected function load_js($ignore_instance = false)
    {
        $out = '';
        if (!self::$head_loaded)
        {
            if ($this->instance_count == 1 && !Xcrud_config::$disable_plugins or $ignore_instance)
            {
                if (!$this->disable_jquery)
                    $out .= '<script src="' . $this->crud_url . '/plugins/jquery.min.js"></script>';
                if (!$this->disable_jquery_ui)
                    $out .= '<script src="' . $this->crud_url . '/plugins/jquery-ui/jquery-ui.min.js"></script>';
                $out .= '<script type="text/javascript">
<!--

	var xcrud_url = "' . $this->crud_url . '";var tinymce_init_url = "' . $this->tinymce_init_url . '";' . (($this->
                    tinymce_folder_url or $this->force_tinymce) ? 'var tinymce_init=1;' : 'var tinymce_init=0;') . ($this->
                    jquery_no_conflict ? 'jQuery.noConflict();' : '') . 'var deleting_confirm = "' . $this->lang('deleting_confirm') .
                    '";var undefined_error = "' . $this->lang('undefined_error') . '";var validation_error = "' . $this->lang('validation_error') .
                    '";var image_type_error = "' . $this->lang('image_type_error') . '";var unique_error = "' . $this->lang('unique_error') .
                    '";
    
-->
</script>';
                if (Xcrud_config::$load_bootstrap && $this->theme != 'default')
                    $out .= '<script src="' . $this->crud_url . '/plugins/bootstrap/js/bootstrap.min.js"></script>';
                $out .= '<script src="' . $this->crud_url . '/plugins/timepicker/jquery-ui-timepicker-addon.js"></script>';
                if (isset($this->field_params['googlemap']))
                    $out .= '<script src="http://maps.google.com/maps/api/js?sensor=false"></script>';
                if ($this->tinymce_folder_url)
                    $out .= '<script src="' . $this->tinymce_folder_url . '/tiny_mce.js"></script>';
                if ($this->language != 'en')
                {
                    if (is_file($this->self_path . '/languages/datepicker/jquery.ui.datepicker-' . $this->language . '.js'))
                        $out .= '<script src="' . $this->crud_url . '/languages/datepicker/jquery.ui.datepicker-' . $this->language .
                            '.js"></script>';
                    if (is_file($this->self_path . '/languages/timepicker/jquery-ui-timepicker-' . $this->language . '.js'))
                        $out .= '<script src="' . $this->crud_url . '/languages/timepicker/jquery-ui-timepicker-' . $this->language .
                            '.js"></script>';
                }

                $out .= '<script src="' . $this->crud_url . '/plugins/xcrud.js"></script>';
            }
        }
        return $out;
    }
    protected function get_limit_list($limit = 20, $class = '', $buttons = false)
    {
        if (!in_array($this->limit, $this->limit_list))
        {
            $this->limit_list = array_merge(array($this->limit), $this->limit_list);
        }
        if ($buttons)
        {
            $out = '<div class="btn-group" data-toggle="buttons-radio">';
            foreach ($this->limit_list as $limts)
            {
                $out .= '<button type="button" class="' . $class . ($limts == $limit ? ' active' : '') . '">' . $limts . '</button>';
            }
            $out .= '</div>';
        } else
        {
            $out = '<select class="' . $class . '">';
            foreach ($this->limit_list as $limts)
            {
                $out .= '<option value="' . $limts . '"' . ($limts == $limit ? ' selected="selected"' : '') . '>' . $limts . '</option>';
            }
            $out .= '</select>';
        }
        return $out;
    }
    public function load_head()
    {
        $out = $this->load_css(true) . $this->load_js(true);
        self::$head_loaded = true;
        return $out;
    }
    protected function crate_googlemap($params, $mode = 'edit')
    {
        return '<div id="map' . rand(11111, 99999) . '" class="xcrud-googlemap" data-zoom="' . $params['zoom'] . '" data-text="' .
            $params['text'] . '" data-text="' . $params['text'] . '" style="width:' . $params['width'] . 'px;height:' . $params['height'] .
            'px" data-lat="' . $params['lat_field'] . '" data-lng="' . $params['lng_field'] . '" data-readonly="' . ($mode == 'edit' ?
            0 : 1) . '"></div>';
    }
    protected function check_file($path, $func_name)
    {
        list($root_folder) = explode('/', trim($this->self_path, '/'), 2);
        list($root_path) = explode('/', trim($path, '/'), 2);
        if (strpos($path, '../') !== false or $root_folder != $root_path)
            $path = $this->self_path . '/' . trim($path, '/');
        if (!is_file($path))
            $this->error('Wrong path or file is not exist! The <strong>' . $func_name . '</strong> method fails.<br /><small>' . $path .
                '</small>');
        return $path;
    }
    protected function check_folder($path, $func_name)
    {
        list($root_folder) = explode('/', trim($this->self_path, '/'), 2);
        list($root_path) = explode('/', trim($path, '/'), 2);
        if (strpos($path, '../') !== false or $root_folder != $root_path)
            $path = $this->self_path . '/' . trim($path, '/');
        if (!is_dir($path))
            $this->error('Wrong path or folder is not exist! The <strong>' . $func_name . '</strong> method fails.<br /><small>' . $path .
                '</small>');
        return $path;
    }

    public function replace_insert($callable = '', $path = 'functions.php')
    {
        if ($callable)
        {
            $this->replace_insert = array('callable' => $callable, 'lib' => $path);
        }
        return $this;
    }
    public function replace_update($callable = '', $path = 'functions.php')
    {
        if ($callable)
        {
            $this->replace_update = array('callable' => $callable, 'lib' => $path);
        }
        return $this;
    }
    public function replace_remove($callable = '', $path = 'functions.php')
    {
        if ($callable)
        {
            $this->replace_remove = array('callable' => $callable, 'lib' => $path);
        }
        return $this;
    }
    public function call_update($postdata, $primary)
    {
        return $this->_update($postdata, $primary);
    }
    protected function additional_columns($fields = '', $table = false)
    {
        if ($fields)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'additional_column');
            foreach ($fdata as $fitem)
            {
                $key = $fitem['table'] . '.' . $fitem['field'];
                if (!isset($this->columns[$key]))
                {
                    if (isset($this->subselect[$key]))
                        continue;
                    $this->columns[$key] = array('table' => $fitem['table'], 'column' => $fitem['field']);
                    $this->field_type[$key] = 'hidden';
                }
            }
        }
    }
    protected function additional_fields($fields = '', $table = false)
    {
        if ($fields)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'additional_column');
            foreach ($fdata as $fitem)
            {
                $key = $fitem['table'] . '.' . $fitem['field'];
                if (!isset($this->fields[$key]))
                {
                    $this->fields[$key] = array('field' => $fitem['field'], 'table' => $fitem['table']);
                    $this->field_type[$key] = 'hidden';
                    $this->locked_fields[$key] = true;
                }
            }
        }
    }
    public function unlock_field($fields = '', $table = false) // this can be used only with callbacks
    {
        if ($fields)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'additional_column');
            foreach ($fdata as $fitem)
            {
                $key = $fitem['table'] . '.' . $fitem['field'];
                if (!isset($this->fields[$key]))
                {
                    $this->hidden_fields[$key] = array('field' => $fitem['field'], 'table' => $fitem['table']);
                }
                if (isset($this->locked_fields[$key]))
                    unset($this->locked_fields[$key]);
            }
        }
    }

    protected function extract_fields_from_text($text, $mode = 'columns')
    {
        $found = preg_match_all('/\{([^\}]+)\}/u', $text, $matches);
        if ($found)
        {
            switch ($mode)
            {
                case 'columns':
                    $this->additional_columns($matches[1]);
                    break;
                case 'fields':
                    $this->additional_fields($matches[1]);
                    break;
            }
        }
    }
    protected function find_grid_text_variables()
    {
        if ($this->column_pattern)
        {
            foreach ($this->column_pattern as $key => $item)
            {
                $this->extract_fields_from_text($item, 'columns');
            }
        }
        if ($this->buttons)
        {
            foreach ($this->buttons as $button)
            {
                $this->extract_fields_from_text($button['link'], 'columns');
            }
        }
    }
    protected function find_details_text_variables()
    {
        if ($this->send_external_create)
        {
            foreach ($this->send_external_create['data'] as $item)
            {
                $this->extract_fields_from_text($item, 'fields');
            }
            if ($this->send_external_create['where_field'])
                $this->additional_fields($this->send_external_create['where_field']);
        }
        if ($this->send_external_edit)
        {
            foreach ($this->send_external_edit['data'] as $item)
            {
                $this->extract_fields_from_text($item, 'fields');
            }
            if ($this->send_external_edit['where_field'])
                $this->additional_fields($this->send_external_edit['where_field']);
        }

    }
    protected function replace_text_variables($value, $data, $safety = false)
    {
        foreach ($data as $key => $val)
        {
            $tmp = explode('.', $key);
            if (count($tmp) > 1)
                list($tbl, $fld) = $tmp;
            else
            {
                $tbl = $this->table;
                $fld = $val;
            }
            if (!is_array($val))
            {
                $value = str_ireplace('{' . $key . '}', $safety ? $this->html_safe($val) : $val, $value);
                if ($tbl == $this->table)
                    $value = str_ireplace('{' . $fld . '}', $safety ? $this->html_safe($val) : $val, $value);
            }
        }
        return $value;
    }

    public function column_pattern($fields, $patern, $table = false)
    {
        if ($fields && $patern)
        {
            $fdata = $this->_parse_field_names($fields, $table, 'additional_column');
            foreach ($fdata as $fitem)
            {
                $this->column_pattern[$fitem['table'] . '.' . $fitem['field']] = str_ireplace('{value}', '{' . $fitem['table'] . '.' . $fitem['field'] .
                    '}', $patern);
            }
        }

    }
    protected function render_detail_field($key, $value)
    {
        switch ($this->field_type[$key])
        {
            case 'image':
                return ($value ? '<img class="xcrud-image img-polaroid" alt="" src="' . $this->file_link($key) . '" />' :
                    '<span class="xcrud-noimage">' . $this->lang('no_image') . '</span>');
                break;
            case 'remote_image':
                return ($value ? '<img class="xcrud-image img-polaroid" alt="" src="' . $value . '" />' : '<span class="xcrud-noimage">' .
                    $this->lang('no_image') . '</span>');
                break;
            case 'file':
                $ext = trim(strtolower(strrchr($value, '.')), '.');
                $folder = $this->upload_folder[$key];
                $path = $this->check_folder($folder, 'file');
                $file_full_path = $path . '/' . $value;
                return ($value ? '<a target="_blank" href="' . $this->file_link($key) . '" class="xcrud-file xcrud-' . trim($ext, '.') .
                    '">' . $value . '</a>' . $this->_file_size($file_full_path) : '<span class="xcrud-nofile">' . $this->lang('no_file') .
                    '</span>');
                break;
            case 'relation':
                $db = Xcrud_db::get_instance($this->connection);
                if ($this->join[$key]['multi'])
                {
                    $value_arr = explode(',', $value);
                    $value = array();
                    foreach ($value_arr as $val)
                    {
                        $value[] = $db->escape($val);
                    }
                    $value = implode(',', $value);
                } else
                {
                    $value = $db->escape($value);
                }
                if (!is_array($this->join[$key]['rel_name']))
                    $this->join[$key]['rel_name'] = array($this->join[$key]['rel_name']);
                $db->query("SELECT GROUP_CONCAT(DISTINCT (CONCAT_WS('{$this->join[$key]['rel_separator']}',`" . implode('`,`', $this->join[$key]['rel_name']) .
                    "`)) SEPARATOR ', ') AS `value` 
                    FROM `{$this->join[$key]['rel_tbl']}` WHERE `{$this->join[$key]['rel_field']}` IN(" . $value . ')');
                $row = $db->row();
                return $row['value'];
                break;
            case 'binary':
                return $value ? '[binary data]' : '[binary null]';
                break;
            case 'bool':
                return ($value) ? $this->lang('bool_on') : $this->lang('bool_off');
                break;
            case 'price':
                return $this->field_maxsize[$key]['prefix'] . number_format($value, $this->field_maxsize[$key]['decimals'], '.', $this->
                    field_maxsize[$key]['separator']) . $this->field_maxsize[$key]['suffix'];
                break;
            case 'hidden':

                break;
            case 'timestamp':
                if ($this->field_type[$key] == 'timestamp')
                    return $this->timestamp_to_datetime($value);
                break;
            default:
                $hidden = '';
                if (isset($this->field_params['googlemap']))
                {
                    if ($this->field_params['googlemap']['lat_field'] == $key or $this->field_params['googlemap']['lng_field'] == $key)
                        $hidden .= '<input class="xcrud-input" type="hidden" name="' . $key . '" value="' . $value . '" />';
                }
                if (Xcrud_config::$clickable_list_links && $this->field_type[$key] == 'text')
                {
                    if (mb_strpos($value, 'http', 0, Xcrud_config::$mbencoding) !== false)
                        return '<a target="_blank" href="' . $value . '">' . ($this->links_label ? $this->links_label['text'] : $this->_cut($value,
                            $this->column_cut)) . '</a>';
                    elseif (mb_strpos($value, '@', 0, Xcrud_config::$mbencoding) !== false)
                        return '<a target="_blank" href="mailto:' . $value . '">' . ($this->emails_label ? $this->emails_label['text'] : $this->
                            _cut($value, $this->column_cut)) . '</a>';
                }
                return $hidden . $value;
                break;
        }
    }
    protected function get_browser_info($ch)
    {
        if ($_COOKIE)
        {
            $ca = http_build_query($_COOKIE);
            $ca = str_replace('&', ';', $ca);
            curl_setopt($ch, CURLOPT_COOKIE, $ca);
        }
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    }
    protected function send_http_request($url, $data, $method, $return_result = false)
    {
        //$path = $this->_check_url($url);
        $path = $url;
        $data = http_build_query($data);
        switch ($method)
        {
            case 'get':
                $ch = curl_init($path . ((mb_strpos($path, '?', 0, Xcrud_config::$mbencoding) === false) ? '?' : '&') . $data);
                break;
            case 'post':
                $ch = curl_init($path);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                return;
                break;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (!$return_result)
        {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
        } else
        {
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        }
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        if (Xcrud_config::$use_browser_info)
        {
            $this->get_browser_info($ch);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    protected function create_tabs(&$fields, $mode, $driver = 'ui')
    {
        $out = '';
        if ((isset($this->field_tabs['all']) && !isset($this->field_marker[$mode])) or (isset($this->field_tabs[$mode]) && isset
            ($this->field_marker[$mode])))
        {
            $tabbable_fields = array();
            $tabs = isset($this->field_marker[$mode]) ? $this->field_tabs[$mode] : $this->field_tabs['all'];
            foreach ($fields as $key => $item)
            {
                if (isset($tabs[$item['name']]))
                {
                    $tabbable_fields[$tabs[$item['name']]][] = $item;
                    unset($fields[$key]);
                }
            }
            unset($tabs);
            if ($tabbable_fields)
            {
                $out .= '<div class="xcrud-tabs-' . $driver . ($driver == 'bs' ? ' tabbable' : '') . '">';
                $tab_names = array_keys($tabbable_fields);
                $tab_ids = array();
                $out .= '<ul' . ($driver == 'bs' ? ' class="nav nav-tabs"' : '') . '>';
                foreach ($tab_names as $i => $tname)
                {
                    $tid = 'tid' . rand(100, 9999);
                    $tab_ids[] = $tid;
                    $out .= '<li' . (($driver == 'bs' && $i == 0) ? ' class="active"' : '') . '><a href="#' . $tid . '"' . ($driver == 'bs' ?
                        ' data-toggle="tab"' : '') . '>' . $tname . '</a></li>';
                }
                $out .= '</ul>';
                if ($driver == 'bs')
                    $out .= '<div class="tab-content">';
                foreach ($tab_names as $tkey => $tname)
                {
                    $out .= '<div id="' . $tab_ids[$tkey] . '"' . (($driver == 'bs') ? ' class="tab-pane' . ($tkey == 0 ? ' active' : '') .
                        '"' : '') . '><table class="xcrud-details' . ($driver == 'bs' ? ' table table-striped table-bordered' . ($this->theme ==
                        'minimal' ? ' table-condensed' : '') : '') . '">';
                    foreach ($tabbable_fields[$tname] as $item)
                    {
                        $primary_class = $this->primary == $item['name'] ? ' primary_details' : '';
                        $out .= '<tr>';
                        $out .= '<td class="xcrud-label' . ($mode == 'view' ? '-view ' : ' ') . $primary_class . '">' . $item['label'] . $this->
                            get_field_tooltip($item['name'], $mode, $driver) . '</td>';
                        $out .= '<td>' . $item['field'] . '</td>';
                        $out .= '</tr>';
                    }
                    $out .= '</table></div>';
                }
                if ($driver == 'bs')
                    $out .= '</div>';
                $out .= '</div>';
            }
        }
        return $out;
    }

    protected function get_table_tooltip($driver = 'ui')
    {
        $out = '';
        if ($this->table_tooltip)
        {
            $out .= ' <a href="javascript:void(0);" class="xcrud-tooltip-' . $driver . ' xcrud-button-link" title="' . $this->
                table_tooltip['tooltip'] . '"><i class="' . ($this->table_tooltip['icon'] ? $this->table_tooltip['icon'] : ($driver ==
                'ui' ? 'comment' : 'icon-info-sign')) . '"></i></a>';
        }
        return $out;
    }
    protected function get_field_tooltip($field, $mode, $driver = 'ui')
    {
        $out = '';
        if ($this->field_tooltip && isset($this->field_tooltip[$field]))
        {
            $out .= ' <a href="javascript:void(0);" class="xcrud-tooltip-' . $driver . ' xcrud-button-link" title="' . $this->
                field_tooltip[$field]['tooltip'] . '"><i class="' . ($this->field_tooltip[$field]['icon'] ? $this->field_tooltip[$field]['icon'] :
                ($driver == 'ui' ? 'comment' : 'icon-info-sign')) . '"></i></a>';
        }
        return $out;
    }
    protected function render_search($column, $phrase)
    {
        $out = '';
        $out .= (!$this->is_search_active($phrase) && !Xcrud_config::$search_opened) ?
            '<a class="xcrud-search-toggle xcrud-action-button btn" href="javascript:void(0);">' . $this->lang('search') . '</a>' :
            '';
        $out .= '<span class="xcrud-search"' . ((!$this->is_search_active($phrase) && !Xcrud_config::$search_opened) ?
            ' style="display:none;"' : '') . '>';

        if (isset($this->field_type[$column]) && ($this->field_type[$column] == 'date' or $this->field_type[$column] == 'datetime' or $this->field_type[$column] ==
            'timestamp'))
        {
            $out .= '<select class="xcrud-data xcrud-rangepreset xcrud-sp" name="column">';
            $out .= '<option value="">- select -</option>';
            $out .= '<option value="today">Today</option>';
            $out .= '<option value="week1">This Week (Mon - Today)</option>';
            $out .= '<option value="week2">This Week (Mon - Sun)</option>';
            $out .= '<option value="week3">Last Week</option>';
            $out .= '<option value="month1">This Month</option>';
            $out .= '<option value="month2">Last Month</option>';
            $out .= '<option value="month3">Last 3 Months</option>';
            $out .= '<option value="month4">Last 6 Months</option>';
            $out .= '<option value="year1">This Year</option>';
            $out .= '<option value="year2">Last Year</option>';
            $out .= '</select>';
            $out .= '<input type="text" class="xcrud-data xcrud-daterange xcrud-sp" name="phrase[0]" value="' . (isset($phrase[0]) ?
                $phrase[0] : '') . '" />';
            $out .= '<input type="text" class="xcrud-data xcrud-daterange xcrud-sp" name="phrase[1]" value="' . (isset($phrase[1]) ?
                $phrase[1] : '') . '" />';
        } else
            $out .= '<input type="text" class="xcrud-data xcrud-phrase xcrud-sp" name="phrase" value="' . (!is_array($phrase) ? $phrase :
                '') . '" />';


        $out .= '<select class="xcrud-data xcrud-filter" name="column">';

        if ($this->search_columns)
        {
            foreach ($this->search_columns as $field => $tmp)
            {
                if (isset($this->columns_names[$field]))
                {
                    $name = $this->columns_names[$field];
                } else
                {
                    if (isset($this->labels[$field]))
                        $name = $this->html_safe($this->labels[$field]);
                    else
                        $name = $this->html_safe($this->_humanize($tmp['column']));
                }
                $out .= '<option value="' . $field . '" data-type="' . $this->field_type[$field] . '"' . ($field == $column ?
                    ' selected="selected"' : '') . '>' . $name . '</option>';
            }
        } else
        {
            foreach ($this->columns_names as $field => $title)
            {
                if ($this->is_hidden($field))
                    continue;
                $out .= '<option value="' . $field . '" data-type="' . $this->field_type[$field] . '"' . ($field == $column ?
                    ' selected="selected"' : '') . '>' . $title . '</option>';
            }
        }

        $out .= '</select>
            <a class="xcrud-search-go xcrud-action-button btn btn-primary" href="javascript:void(0);">' . $this->lang('go') .
            '</a>' . (($this->is_search_active($phrase)) ?
            '<a class="xcrud-search-reset xcrud-action-button btn" href="javascript:void(0);">' . $this->lang('reset') . '</a>' : '') .
            '</span>';
        return $out;
    }
    protected function render_search_hidden($column, $phrase)
    {
        $out = '';
        if (isset($this->field_type[$column]) && ($this->field_type[$column] == 'date' or $this->field_type[$column] == 'datetime' or $this->field_type[$column] ==
            'timestamp'))
        {
            $out .= '<input type="hidden" class="xcrud-data" name="phrase[0]" value="' . (isset($phrase[0]) ? $phrase[0] : '') .
                '" />';
            $out .= '<input type="hidden" class="xcrud-data" name="phrase[1]" value="' . (isset($phrase[1]) ? $phrase[1] : '') .
                '" />';
        } else
            $out .= '<input type="hidden" class="xcrud-data" name="phrase" value="' . (!is_array($phrase) ? $phrase : '') . '" />';
        return $out;
    }
    protected function is_hidden($field)
    {
        if (isset($this->field_type[$field]))
        {
            if ($this->field_type[$field] == 'hidden')
                return true;
            else
                return false;
        }
        return true;
    }
    protected function is_search_active($phrase)
    {
        if (!$phrase or (isset($phrase[0]) && !$phrase[0]) or (isset($phrase[1]) && !$phrase[1]))
            return false;
        else
            return true;
    }
    protected function timestamp_to_datetime($value)
    {
        return date('Y-m-d H:i:s', intval(preg_replace('/[^0-9]+/', '', $value) == $value ? $value : strtotime($value)));
    }


}
