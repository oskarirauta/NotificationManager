<?php

$products = [
  'myproduct' => [ // Use password.php to generate store password. cert password is in cleartext.
    'store' => '$2y$10$1pACPfObCg9/Q02/avejv.RHCt/MLgtb03s7Zwfla6/.a4cSMV5gG',
    'cert' => 'password' // Default store password is 'password'
  ]
];

$credentials = [ // Use password.php to generate password hash. Default: admin
  'userid' => 'myuser', // Add your userid here.
  'password' => '$2y$10$R3LOrI94FG6xzNEqq5t1eOEyf9MjAIqQFImG.0YviWpVrFRRoi7cK'
];

?>
