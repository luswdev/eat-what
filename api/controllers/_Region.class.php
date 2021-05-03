<?php
namespace EatWhat;

class RegionAPI extends API
{
    private $regid;
    private $new;
    private $id;
    private $del;

    public function __construct ()
    {
        parent::__construct();
        $this->conn = parent::ConnectDB();

        $this->regid = $_POST['reg'] ?? false;
        $this->new   = $_POST['new'] ?? false;
        $this->id    = $_POST['id']  ?? false;

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $_DELETE = json_decode(file_get_contents('php://input'), true);
        }

        $this->del = $_DELETE['del'] ?? false;
    }

    public function __destruct ()
    {
        $this->conn->close();
    }

    public function Run ()
    {
        if ($this->regid && $this->new) {
            return $this->UpdateRegion();
        } else if ($this->new && $this->id) {
            return $this->NewRegion();
        } else if ($this->del) {
            return $this->DelRegion();
        } else {
            return $this->GetRegions();
        }
    }

    private function NewRegion ()
    {
        $query = 'INSERT RegionList(RegionID, RegionName, UpdatedFrom) VALUES (?, ?, ?)';
        $stmt  = $this->conn->prepare($query);
    
        $stmt->bind_param('sss', $this->id, $this->new, $this->ip);
        $stmt->execute();
        $stmt->close();

    }

    private function DelRegion ()
    {
        $query = 'DELETE FROM RegionList WHERE RegionName=?';
        $stmt  = $this->conn->prepare($query);
    
        $stmt->bind_param('s', $this->del);
        $stmt->execute();
        $stmt->close();
    }

    private function UpdateRegion ()
    {
        $backupName = false;

        $query = 'SELECT RegionName FROM RegionList WHERE RegionID=?';
        $stmt  = $this->conn->prepare($query);
    
        $stmt->bind_param('s', $this->regid);
        $stmt->execute();
        $stmt->bind_result($backupName);
        $stmt->fetch();
        $stmt->close();
    
        $query = 'UPDATE RegionList SET RegionName=?, UpdatedFrom=?, BackupName=? WHERE RegionID=?';
        $stmt  = $this->conn->prepare($query);
    
        $stmt->bind_param('ssss', $this->new, $this->ip, $backupName, $this->regid);
        $stmt->execute();
        $stmt->close();
    }

    private function GetRegions () : array
    {
        $rid = false;
        $reg = false;

        $query = 'SELECT RegionID, RegionName FROM RegionList ORDER BY UUID ASC';
        $stmt  = $this->conn->prepare($query);
    
        $stmt->execute();
        $stmt->bind_result($rid, $reg);
    
        while ($stmt->fetch()) {
            $regs[] = [
                'name' => $rid,
                'title' => $reg,
            ];
        }
    
        $stmt->close();
    
        return $regs;
    }
}

?>
