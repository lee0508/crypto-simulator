<?php
session_start();
session_destroy();
header("Location: index5.php");
exit;
