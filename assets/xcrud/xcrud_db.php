<?php class Xcrud_db
{
    private static $_instance = array();
    private $connect;
    private $result;
    private $dbhost;
    private $dbuser;
    private $dbpass;
    private $dbname;
    private $dbencoding;
    private $magic_quotes_gpc;

    public static function get_instance($params = false)
    {
        if (is_array($params))
        {
            list($dbuser, $dbpass, $dbname, $dbhost, $dbencoding) = $params;
            $instance_name = sha1($dbuser . $dbpass . $dbname . $dbhost . $dbencoding);
        } else
        {
            $instance_name = 'db_instance_default';
        }
        if (!isset(self::$_instance[$instance_name]) or null === self::$_instance[$instance_name])
        {
            if (!is_array($params))
            {
                $dbuser = Xcrud_config::$dbuser;
                $dbpass = Xcrud_config::$dbpass;
                $dbname = Xcrud_config::$dbname;
                $dbhost = Xcrud_config::$dbhost;
                $dbencoding = Xcrud_config::$dbencoding;
            }
            self::$_instance[$instance_name] = new self($dbuser, $dbpass, $dbname, $dbhost, $dbencoding);
        }
        return self::$_instance[$instance_name];
    }
    private function __construct($dbuser, $dbpass, $dbname, $dbhost, $dbencoding)
    {
        $this->magic_quotes_gpc = get_magic_quotes_gpc();
        if (strpos($dbhost, ':') !== false)
        {
            list($host, $port) = explode(':', $dbhost, 2);
            $this->connect = mysqli_connect($host, $dbuser, $dbpass, $dbname, $port);
        } else
            $this->connect = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        if (!$this->connect)
            $this->error('Connection error. Can not connect to database');
        $this->connect->set_charset($dbencoding);
        if ($this->connect->error)
            $this->error($this->connect->error);
        if (Xcrud_config::$db_time_zone)
            $this->connect->query('SET time_zone = \'' . Xcrud_config::$db_time_zone . '\'');
    }
    public function query($query = '')
    {
        $this->result = $this->connect->query($query, MYSQLI_USE_RESULT); //echo '<pre>'.$query.'</pre>';
        if ($this->connect->error)
            $this->error($this->connect->error . '<pre>' . $query . '</pre>');
        return $this->connect->affected_rows;
    }
    public function insert_id()
    {
        return $this->connect->insert_id;
    }
    public function result()
    {
        $out = array();
        if ($this->result)
        {
            while ($obj = $this->result->fetch_assoc())
            {
                $out[] = $obj;
            }
            $this->result->free();
        }
        return $out;
    }
    public function row()
    {
        $obj = $this->result->fetch_assoc();
        $this->result->free();
        return $obj;
    }
    public function escape($val, $not_qu = false)
    {
        if (is_int($val))
            return (int)$val;
        if ($not_qu)
            return $this->magic_quotes_gpc ? $val : $this->connect->real_escape_string($val);
        return '\'' . ($this->magic_quotes_gpc ? $val : $this->connect->real_escape_string($val)) . '\'';
    }
    public function escape_like($val, $pattern = array('%', '%'))
    {
        if (is_int($val))
            return '\'' . $pattern[0] . (int)$val . $pattern[1] . '\'';
        return '\'' . $pattern[0] . ($this->magic_quotes_gpc ? $val : $this->connect->real_escape_string($val)) . $pattern[1] .
            '\'';
    }
    public function return_csv($table = '', $head = array())
    {
        ini_set('auto_detect_line_endings', true);
        header('Content-Type: text/csv; charset=' . Xcrud_config::$mbencoding);
        header('Content-Disposition: attachment; filename=' . $table . '.csv');
        $output = fopen('php://output', 'w');
        fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // bom
        if ($head)
            fputcsv($output, $head, Xcrud_config::$csv_delimiter, Xcrud_config::$csv_enclosure);
        while ($row = $this->result->fetch_row())
        {
            fputcsv($output, $row, Xcrud_config::$csv_delimiter, Xcrud_config::$csv_enclosure);
        }
        //$this->result->free();
        exit();
    }
    public function csv_head($table = '')
    {
        $head = array();
        $result = $this->connect->query("SHOW COLUMNS FROM `{$table}`", MYSQLI_USE_RESULT);
        while ($row = $result->fetch_assoc())
        {
            $head[] = $row->Field;
        }
        return $head;
    }
    private function error($text = 'Error!')
    {
        exit('<div class="xcrud-error" style="padding:15px;color:#EE9401;margin:10px;border:1px solid#EE9401;border-radius:2px;">' .
            $text . '</div>');
    }
}
