<?php
require __DIR__ . '/config/bootstrap.php';
unset($_SESSION['user']);
flash_set('success','Logged out.');
redirect(url($base_path,'index.php'));
