<?php

namespace Its\Maxma\Api;

use Bitrix\Main\Localization\Loc;
use CloudLoyalty\Api\Exception\ProcessingException;
use CloudLoyalty\Api\Exception\TransportException;

class Result
{
    protected bool $success = true;

    protected array $data = [];
    protected array $errors = [];
    protected array $warnings = [];

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    protected function getExceptionArray(\Throwable $e): array
    {
        $result = [
            'code' => $e->getCode(),
            'message' => "{$e->getMessage()} in {$e->getFile()} on {$e->getLine()}",
            'trace' => $e->getTraceAsString(),
        ];
        if ($e instanceof ProcessingException) {
            $result['hint'] = $e->getHint();
            $result['message'] = $e->getDescription();
        }
        return $result;
    }

    public function setExceptionError(\Throwable $e, string $method, array $data = [], array $ignoreCodes = []): void
    {
        $type = 'Internal';
        $message = Loc::getMessage('ITS_MAXMA_API_RESULT_INTERNAL_ERROR');

        if ($e instanceof TransportException) {
            $type = 'Network';
            $message = Loc::getMessage('ITS_MAXMA_API_RESULT_NETWORK_ERROR');
        }

        if ($e instanceof ProcessingException) {
            if (in_array($e->getCode(), $ignoreCodes)) {
                return;
            }
            $type = 'Processing';
            $message = $e->getDescription();
        }

        $this->addError($message);
        Log::getInstance()->flush([
            'type' => "{$type} error in {$method}!",
            'exception' => $this->getExceptionArray($e),
            'data' => $data,
        ]);
    }

    public function addError(string $message): void
    {
        $this->success = false;
        $this->errors[] = $message;
    }

    public function addWarning(string $message): void
    {
        $this->warnings[] = $message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
