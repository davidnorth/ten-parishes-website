<?php

$_SESSION = [];
session_destroy();
return redirect('/admin/login');
