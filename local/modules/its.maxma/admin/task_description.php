<?php

use Its\Maxma\Right;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

return (new Right())->getModuleTasksList();
