<?php
require __DIR__ . '/../config/bootstrap.php';
unset($_SESSION['admin']);
redirect(url($base_path,'admin/login.php'));
