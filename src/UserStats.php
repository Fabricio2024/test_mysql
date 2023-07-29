<?php

include "Connection.php";

class UserStats extends Connection {
    private $conn;
    private $activeStatus;

    public function __construct() 
    {
        parent::__construct('mysql', 'dba', 'secret', 'challenge');
        $this->conn = $this->getConn();
        $this->activeStatus = "active";
    }


    function getStats($dateFrom, $dateTo, $totalClicks = 0) 
    {
        $sql = "SELECT CONCAT(u.first_name, ' ', u.last_name) as full_name,
                       SUM(us.views) as total_views, 
                       SUM(us.clicks) as total_clicks, 
                       SUM(us.conversions) as total_conversions,
                       MAX(date) as last_date
                FROM users as u
                JOIN user_stats as us on us.user_id = u.id
                WHERE u.status = '{$this->activeStatus}'
                AND date BETWEEN '{$dateFrom}' AND '{$dateTo}'
                GROUP BY u.id 
                HAVING SUM(us.clicks) >= {$totalClicks}
                ";
        $statement = $this->conn->query($sql);
        return $this->getStatement($statement);
    }

    private function getStatement($statement) 
    {
        $stats = [];

        if ($result = $statement) {
            while ($data = $result->fetch_object()) {
                $results[] = $data;
            }
        }

        foreach($results as $index => $user) {
            $last_date = date('Y-m-d', strtotime($user->last_date));
            $stats[$index]["full_name"] = $user->full_name;
            $stats[$index]["total_views"] = $user->total_views;
            $stats[$index]["total_clicks"] = $user->total_clicks;
            $stats[$index]["total_conversions"] = $user->total_conversions;
            $stats[$index]["cr"] = $this->calculateCr($user->total_conversions, $user->total_clicks);
            $stats[$index]["last_date"] = $last_date;
        }

        return $stats;
    }

    private function calculateCr($total_conversation, $total_clicks) 
    {
        return number_format(($total_conversation / $total_clicks) * 100, 2);
    }
}