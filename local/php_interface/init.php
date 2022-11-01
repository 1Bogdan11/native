<?php

use Bex\Monolog\MonologAdapter;
use Symfony\Component\Dotenv\Dotenv;

const RECAPTCHA_PUBLIC_KEY = '6LcABEsgAAAAAASWCR3BGfS5TCAbHPCd9x39FpUt';
const RECAPTCHA_PRIVATE_KEY = '6LcABEsgAAAAAEEyYEtHHTGOLB0nINe9bc2LHSjN';

const DADATA_PUBLIC_KEY = '53b62f14be5138c0aea471ce8b284e4be20c64ec';

require_once __DIR__ . '/../../bitrix/vendor/autoload.php';

$dotEnv = new Dotenv();
$envPath = __DIR__ . '/../../.env';

if (file_exists($envPath)) {
    $dotEnv->load($envPath);
}

MonologAdapter::loadConfiguration();

require_once __DIR__ . '/bind.php';

$smtpModuleFile = __DIR__ . '/../../bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php';
if (file_exists($smtpModuleFile)) {
    require_once $smtpModuleFile;
}

