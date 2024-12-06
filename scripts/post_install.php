<?php

if (function_exists("post_install") === false) {
function post_install()
{
    include("custom/post_install/SugarGatorConfigurator.php ");
}


