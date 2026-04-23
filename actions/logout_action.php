<?php
/**
 * Uitlog-actie
 * Vernietigt de sessie en stuurt terug naar de loginpagina.
 */
session_start();
session_unset();
session_destroy();

header('Location: /login.php');
exit;
