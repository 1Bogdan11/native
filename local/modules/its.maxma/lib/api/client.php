<?php

namespace Its\Maxma\Api;

use CloudLoyalty\Api\Client as BaseClient;
use CloudLoyalty\Api\Generated\Model\GenerateGiftCardRequest;

class Client extends BaseClient
{
    public function itsGenerateGiftCard(GenerateGiftCardRequest $request)
    {
        return $this->call('generate-gift-card', $request, 'CloudLoyalty\Api\Generated\Model\GenerateGiftCardResponse');
    }
}
