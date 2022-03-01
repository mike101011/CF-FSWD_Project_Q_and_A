<?php
function sessFct()
{
    if (isset($_SESSION["adm"])) {
        return $_SESSION["adm"];
    } else {
        return $_SESSION["user"];
    }
}
