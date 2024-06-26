<?php

namespace LaravelFCM\Response;

use LaravelFCM\Response\Exceptions\InvalidRequestException;
use LaravelFCM\Response\Exceptions\ServerResponseException;
use LaravelFCM\Response\Exceptions\UnauthorizedRequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseResponse.
 */
abstract class BaseResponse
{
    const SUCCESS = 'success';

    const FAILURE = 'failure';

    const ERROR = 'error';

    const MESSAGE_ID = 'message_id';

    /**
     * @var bool
     */
    protected $logEnabled = false;

    /**
     * BaseResponse constructor.
     */
    public function __construct(ResponseInterface $response)
    {
        $this->isJsonResponse($response);
        $this->logEnabled = app('config')->get('fcm.log_enabled', false);
        $responseInJson = json_decode($response->getBody(), true);
        $this->parseResponse($responseInJson);
    }

    /**
     * Check if the response given by fcm is parsable.
     *
     *
     * @throws InvalidRequestException
     * @throws ServerResponseException
     * @throws UnauthorizedRequestException
     */
    private function isJsonResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 200) {
            return;
        }

        if ($response->getStatusCode() == 400) {
            throw new InvalidRequestException($response);
        }

        if ($response->getStatusCode() == 401) {
            throw new UnauthorizedRequestException($response);
        }

        throw new ServerResponseException($response);
    }

    /**
     * parse the response.
     *
     * @param  array  $responseInJson
     */
    abstract protected function parseResponse($responseInJson);

    /**
     * Log the response.
     */
    abstract protected function logResponse();
}
