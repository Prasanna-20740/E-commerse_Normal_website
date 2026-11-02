<?php

/* =====================================================
   ADMIN AUTHENTICATION
===================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =====================================================
   CHECK ADMIN LOGIN
===================================================== */

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../login.php");

    exit;
}


/* =====================================================
   SESSION FIXATION PROTECTION
===================================================== */

if (!isset($_SESSION["session_created"])) {

    session_regenerate_id(true);

    $_SESSION["session_created"] = time();
}