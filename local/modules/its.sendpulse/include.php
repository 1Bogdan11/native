<?php

try {
    Bitrix\Main\Loader::registerAutoloadClasses(
        "its.sendpulse",
        array(
            "Sendpulse" => "lib/sendpulse.php",
        )
    );
} catch (\Bitrix\Main\LoaderException $e) {
}
