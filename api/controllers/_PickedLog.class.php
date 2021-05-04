<?php
namespace EatWhat;

class PickedLogAPI extends API
{
    private $update;
    private $share;
    private $list;

    public function __construct ()
    {
        parent::__construct();
        $this->conn = parent::ConnectDB();

        $this->update = $_POST['rid'] ?? false;
        $this->share  = $_GET['pid']  ?? false;
        $this->list   = $_GET['list'] ?? $_POST['list'] ?? 'ndhu';
    }

    public function __destruct()
    {
        $this->conn->close();
    }

    public function Run ()
    {
        if ($this->update) {
            return $this->UpdateLog();
        } else {
            return $this->GetLog();
        }

        return false;
    }

    private function UpdateLog () : int
    {
        $query = 'INSERT PickedLog(RID, PickFrom) VALUES (?, ?)';
        $stmt  = $this->conn->prepare($query);

        $stmt->bind_param('ss', $this->update, $this->ip);
        $stmt->execute();

        $LastID = $this->conn->insert_id;

        $stmt->close();

        return $LastID;
    }

    private function GetLog () : array
    {
        $query = 'SELECT a.PID, b.Restaurant, b.OpenTime, a.PickTime, a.PickFrom FROM PickedLog AS a LEFT JOIN RestaurantList AS b ON a.RID = b.RID ORDER BY PID DESC';
        $stmt  = $this->conn->prepare($query);
    
        $pid = false;
        $res = false;
        $when = false;
        $time = false;
        $ip_r = false;
        $stmt->execute();
        $stmt->bind_result($pid, $res, $when, $time, $ip_r);

        $history = [];
        while($stmt->fetch()) {
            $when = $when == 'brunch' ? '早餐' : '晚餐';
            $history[] = [
                'pid' => $pid,
                'restaurant' => $res,
                'when' => $when,
                'time' => $time,
                'ip' => $ip_r
            ];
        }

        $stmt->close();

        return $history;
    }
}

?>
