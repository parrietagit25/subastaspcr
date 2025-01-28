<?php 
session_start(); 
unset($_SESSION['code']); 
session_unset(); 
session_destroy();
if (!isset($_SESSION['code'])) {
    header("Location: index.php");
    exit();
}