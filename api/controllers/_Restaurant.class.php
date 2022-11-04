<?php
namespace EatWhat;

class RestaurantAPI extends API
{
    private $resid;
    private $res;
    private $when;

    private $del;
    private $list;

    public function __construct ()
    {
        parent::__construct();
        $this->conn = parent::ConnectDB();

        $this->resid = $_POST['res']  ?? false;
        $this->res   = $_POST['new']  ?? false;
        $this->when  = $_POST['when'] ?? false;

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $_DELETE = json_decode(file_get_contents('php://input'), true);
        }

        $this->del  = $_DELETE['res'] ?? false;
        $this->list = $_GET['list']   ?? $_POST['list'] ?? $_DELETE['list'] ?? 'ndhu';
    }

    public function __destruct ()
    {
        $this->conn->close();
    }

    public function Run ()
    {
        if ($this->resid && $this->res) {
            return $this->UpdateRestaurant();
        } else if ($this->res && $this->when) {
            return $this->NewRestaurant();
        } else if ($this->del) {
            return $this->DeleteRestaurant();
        } else {
            return $this->GetRestaurants();
        }
    }

    private function NewRestaurant ()
    {
        $query = 'INSERT RestaurantList(restaurant, OpenTime, Region, UpdatedFrom, BackupName) VALUES (?, ?, ?, ?, ?)';
        $stmt  = $this->conn->prepare($query);

        $stmt->bind_param('sssss', $this->res, $this->when, $this->list, $this->ip, $this->res);
        $stmt->execute();
        $stmt->close();
    }

    private function DeleteRestaurant ()
    {
        $query = 'DELETE FROM RestaurantList WHERE rid=?';
        $stmt  = $this->conn->prepare($query);

        $stmt->bind_param('s', $this->del);
        $stmt->execute();
        $stmt->close();
    }

    private function UpdateRestaurant ()
    {
        $backupName = false;

        $query = 'SELECT Restaurant FROM RestaurantList WHERE RID=?';
        $stmt  = $this->conn->prepare($query);

        $stmt->bind_param('s', $this->resid);
        $stmt->execute();
        $stmt->bind_result($backupName);
        $stmt->fetch();
        $stmt->close();

        $query = 'UPDATE RestaurantList SET BackupName=?, Restaurant=?, UpdatedFrom=? WHERE RID=?';
        $stmt  = $this->conn->prepare($query);

        $stmt->bind_param('ssss', $backupName, $this->res, $this->ip, $this->resid);
        $stmt->execute();
        $stmt->close();
    }

    private function GetRestaurants () : array
    {
        $rid = false;
        $res = false;

        $query = 'SELECT RID, Restaurant FROM RestaurantList WHERE OpenTime=\'brunch\' AND Region=?';
        $stmt  = $this->conn->prepare($query);

        $stmt->bind_param('s', $this->list);
        $stmt->execute();
        $stmt->bind_result($rid, $res);

        $brunch = [];
        while ($stmt->fetch()) {
            $brunch[] = [
                'rid' => $rid,
                'restaurant' => $res,
            ];
        }
        
        $stmt->close();

        $query = 'SELECT RID, Restaurant FROM RestaurantList WHERE OpenTime=\'dinner\' AND Region=?';
        $stmt  = $this->conn->prepare($query);

        $stmt->bind_param('s', $this->list);
        $stmt->execute();
        $stmt->bind_result($rid, $res);

        $dinner = [];
        while ($stmt->fetch()) {
            $dinner[] = [
                'rid' => $rid,
                'restaurant' => $res,
            ];
        }

        $stmt->close();

        $food = [
            'brunch' => $brunch,
            'dinner' => $dinner,
        ];

        return $food;
    }
}

?>
