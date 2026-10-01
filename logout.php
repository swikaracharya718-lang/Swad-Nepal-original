<?php
require_once 'includes/functions.php';
session_unset();
session_destroy();
session_start();
flash_set('You have been logged out.', 'success');
redirect('login.php');
