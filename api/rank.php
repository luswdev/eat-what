<?php
$type = $_GET['type'] ?? false;

include_once('db.php');

if ($type) {
    switch ($type) {
        case 'restaurant':
            $query = 'SELECT b.Restaurant, COUNT(*) Ranks FROM PickedLog a INNER JOIN RestaurantList b ON a.RID = b.RID WHERE b.OpenTime = \'brunch\' GROUP BY b.Restaurant ORDER BY Ranks DESC LIMIT 5';
            $stmt  = $conn->prepare($query);

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
            $stmt  = $conn->prepare($query);
            
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
            
            $food = json_encode($food, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            print_r($food);
            break;

        case 'country':
            include_once('./country-list.php');

            $query = 'SELECT PickFrom, COUNT(*) Ranks FROM PickedLog GROUP BY PickFrom ORDER BY Ranks DESC';
            $stmt  = $conn->prepare($query);

            $stmt->execute();
            $stmt->bind_result($ip, $rank);

            while ($stmt->fetch()) {
                $country = geoip_country_name_by_name($ip);
                $key = array_search($country, $countryList);
                $country = Locale::getDisplayRegion('-'.$key, 'zh-TW');
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

            $ranked = json_encode($ranked, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            print_r($ranked);
            break;
    }
}

?>
