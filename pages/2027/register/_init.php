<?php
if (!isset($_SESSION['registration'])) {
    $_SESSION['registration'] = [];
}
$reg = &$_SESSION['registration'];
