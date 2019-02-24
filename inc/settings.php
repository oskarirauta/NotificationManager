<?php

$products = [ // list of products. Generate store password hashes with password.php
  'product1' => [
    'store' => '$2y$10$kwA3SiAf92ATQEwYiQaCHOZHVEiW5ahiwhaeCznlaUlVijkeD2dUW',
    'cert' => 'password' // Certificate passwords must be stored in clear-text.
  ], [
  'product2' => [
    'store' => '$2y$10$hKxrS8vJh1o2sGIwNjZwtePQMvVGhgRqVsqmjz18awJ/NX5RyBhdW',
    'cert' => 'password'
  ]
];

$credentials = [ // use password.php to generate password hashes.
  'user1@email.com' => [
    'name' => 'Fullname 1',
    'password' => '$2y$10$hS2G0gTsXfAe6o44gEHZ1.l0ItVrfasT0dKLPSLk2TCIEOj1CfR92'
  ],
  'user2@email.com' => [
    'name' => 'Fullname 2',
    'password' => '$2y$10$ZDz4niKCI0/TZi.1118z7ug86hClEtfRBKqJGOQkoPJgdoZfqa5nK'
  ]
];

?>
