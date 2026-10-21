<?php
require __DIR__ . '/../config/app.php';
header('Content-Type: text/html; charset=utf-8');
session_start();
session_destroy();
header('Location: ' . BASE . '/login.php');
exit;
