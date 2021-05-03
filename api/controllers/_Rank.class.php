<?php
namespace EatWhat;

class RankAPI extends API
{
    private $rankType;

    public function __construct ()
    {
        parent::__construct();
        $this->conn = parent::ConnectDB();

        $this->rankType = $_GET['rank-type'] ?? false;
    }

    public function __destruct ()
    {
        $this->conn->close();
    }

    public function Run ()
    {
        switch ($this->rankType) {
        case 'restaurant':
            return $this->RestaurantRank();

        case 'country':
            return $this->CountryRank();
        }
    }

    private function RestaurantRank () : array
    {
        $restaurant = false;
        $rank = false;

        $query = 'SELECT b.Restaurant, COUNT(*) Ranks FROM PickedLog a INNER JOIN RestaurantList b ON a.RID = b.RID WHERE b.OpenTime = \'brunch\' GROUP BY b.Restaurant ORDER BY Ranks DESC LIMIT 5';
        $stmt  = $this->conn->prepare($query);

        $stmt->execute();
        $stmt->bind_result($restaurant, $rank);

        while ($stmt->fetch()) {
            $brunch[] = [
                'restaurant' => $restaurant,
                'rank' => $rank
            ];
        }

        $stmt->close();

        $query = 'SELECT b.Restaurant, COUNT(*) Ranks FROM PickedLog a INNER JOIN RestaurantList b ON a.RID = b.RID WHERE b.OpenTime = \'dinner\' GROUP BY b.Restaurant ORDER BY Ranks DESC LIMIT 5';
        $stmt  = $this->conn->prepare($query);
        
        $stmt->execute();
        $stmt->bind_result($restaurant, $rank);

        while ($stmt->fetch()) {
            $dinner[] = [
                'restaurant' => $restaurant,
                'rank' => $rank
            ];
        }

        $stmt->close();

        $food = [
            'brunch' => $brunch,
            'dinner' => $dinner,
        ];
        
        return $food;
    }

    private function CountryRank () : array
    {
        $ip = false;
        $rank = false;

        $countryList = $this->GetCountryList();

        $query = 'SELECT PickFrom, COUNT(*) Ranks FROM PickedLog GROUP BY PickFrom ORDER BY Ranks DESC';
        $stmt  = $this->conn->prepare($query);

        $stmt->execute();
        $stmt->bind_result($ip, $rank);

        while ($stmt->fetch()) {
            $country = \geoip_country_name_by_name($ip);
            $key = array_search($country, $countryList);
            $country = \Locale::getDisplayRegion('-'.$key, 'zh-TW');
            $ipTable[] = [
                'ip' => $ip,
                'rank' => $rank,
                'country' => $country
            ];
        }

        $stmt->close();

        $ranked = [];
        foreach($ipTable as $ip) {
            $findKey = array_search($ip['country'], array_column($ranked, 'name'));
            if ($findKey !== false) {
                $ranked[$findKey]['value'] += $ip['rank'];
            } else {
                $ranked[] = [
                    'name' => $ip['country'],
                    'value' => $ip['rank']
                ];
            }
        }

        return $ranked;
    }

    private function GetCountryList () : array
    {
        include '_CountryList.php';
        return $countryList;
    }
}

?>
