<?php

$db_name = 'mysql:host=127.0.0.1;port=3307;dbname=shoes_db';

$username = 'root';
$userpassword = '';

$conn = new PDO($db_name, $username, $userpassword);
