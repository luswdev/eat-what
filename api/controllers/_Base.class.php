<?php
namespace EatWhat;

class API
{
    protected $config;
    protected $conn;
    protected $ip;
    private $type;

    public function __construct ()
    {
        $this->config = json_decode(file_get_contents('../data/config.json'));

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }

        $this->type = $_GET['type'];
    }

    public function Run ()
    {
        $api = false;

        switch ($this->type) {
        case 'web-info':
            return $this->WebInfo();
        
        case 'picked-log':
            $api = new PickedLogAPI();
            break;

        case 'restaurant':
            $api = new RestaurantAPI();
            break;

        case 'region':
            $api = new RegionAPI();
            break;

        case 'rank':
            $api = new RankAPI();
            break;

        default:
            return ['error' => 'bad request'];
        }

        return $api->Run();
    }

    protected function ConnectDB () : object
    {
        $DBHOST = $this->config->db->host;
        $DBUSER = $this->config->db->user;
        $DBPASS = $this->config->db->password;
        $DBNAME = $this->config->db->table;

        $conn = new \mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
        return $conn;
    }

    public function WebInfo () : object
    {
        return $this->config->webinfo;
    }
}

?>
