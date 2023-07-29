<?php

include "UserStats.php";

$userStasts = new UserStats();
$stats = $userStasts->getStats('2022-10-01', '2022-10-15', 9000);
echo '<pre>';
print_r($stats);
echo '</pre>';