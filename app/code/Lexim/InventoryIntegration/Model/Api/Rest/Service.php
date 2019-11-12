<?php declare(strict_types=1);

namespace Lexim\InventoryIntegration\Model\Api\Rest;

use Lexim\InventoryIntegration\Model\Api\Exception as LeximApiException;
use Lexim\InventoryIntegration\Model\Api\ServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Magento\Framework\Serialize\Serializer\Json as Serializer;

class Service implements ServiceInterface
{
    /**
     * Holds headers to be sent in HTTP request
     *
     * @var array
     */
    private $headers = [];

    /**
     * The base URL to interact with
     *
     * @var string
     */
    private $uri = '';

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $password = '';

    /**
     * @var LoggerInterface $log
     */
    private $log;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Service constructor.
     * @param LoggerInterface $log
     * @param Serializer $serializer
     */
    public function __construct(
        LoggerInterface $log,
        Serializer $serializer
    ) {
        $this->log = $log;
        $this->serializer = $serializer;
        $this->client = new Client();
    }

    /**
     * @param string $url
     * @param string $body
     * @param string $method
     * @return array|ResponseInterface
     * @throws LeximApiException
     */
    public function makeRequest($url, $body = '', $method = self::POST)
    {
        $response = [
            'is_successful' => false
        ];
        try {
            $data = [
                'headers' => $this->headers,
                'json'    => $body
            ];
            $data = $this->getAuth($data);

            /** @var ResponseInterface $response */
            $response = $this->client->$method($this->uri . $url, $data);
            $response = $this->processResponse($response);
            $response['is_successful'] = true;
        } catch (BadResponseException $e) {
            $this->log->error('Bad Response: ' . $e->getMessage());
            $this->log->error((string)$e->getRequest()->getBody());
            $response['response_status_code'] = $e->getCode();
            $response['response_status_message'] = $e->getMessage();
            $response = $this->processResponse($response);
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                $this->log->error($errorResponse->getStatusCode() . ' ' . $errorResponse->getReasonPhrase());
                try {
                    $body = $this->processResponse($errorResponse);
                } catch (\Exception $e) {
                    $this->log->error('Exception: ' . $e->getMessage());
                    $response['exception_code'] = $e->getCode();
                }
                $response = array_merge($response, $body);
            }
            $response['exception_code'] = $e->getCode();
        } catch (\Exception $e) {
            $this->log->error('Exception: ' . $e->getMessage());
            $response['exception_code'] = $e->getCode();
        }
        $this->logRequestResponse($body, $response, $url);
        return $response;
    }

    /**
     * @param string $username
     * @param string $password
     * @param null $connectUrl
     * @return bool
     */
    public function connect($username, $password, $connectUrl = null)
    {
        $this->username = $username;
        $this->password = $password;
        if ($connectUrl) {
            $this->uri = $connectUrl;
        }
        return true;
    }

    /**
     * @param string $product
     * @param string $version
     * @param string $mageInfo
     */
    public function setUserAgent($product, $version, $mageInfo)
    {
        $baseUA = sprintf('Guzzle/%s;PHP/%s', \GuzzleHttp\Client::VERSION, PHP_VERSION);
        $this->setHeader(
            'User-Agent',
            sprintf('%s/%s;%s (%s)', $product, $version, $baseUA, $mageInfo)
        );
    }

    /**
     * Set auth data if username or password has been provided
     *
     * @param $data
     * @return mixed
     */
    private function getAuth($data)
    {
        if ($this->username || $this->password) {
            $data['auth'] = [$this->username, $this->password];
        }
        return $data;
    }

    /**
     * @param string $header
     * @param string|null $value
     */
    public function setHeader($header, $value = null)
    {
        if (!$value) {
            unset($this->headers[$header]);
            return;
        }
        $this->headers[$header] = $value;
    }

    /**
     * Process the response and return an array
     *
     * @param ResponseInterface|array $response
     * @return array
     * @throws LeximApiException
     */
    private function processResponse($response)
    {
        if (is_array($response)) {
            return $response;
        }
        try {
            $data = $this->serializer->unserialize((string)$response->getBody());
        } catch (\Exception $e) {
            $data = [
                'exception' => $e->getMessage()
            ];
        }
        if ($response->getStatusCode() === 401) {
            throw new LeximApiException(__($response->getReasonPhrase()));
        }
        $data['response_object'] = [
            'headers' => $response->getHeaders(),
            'body'    => $response->getBody()->getContents()
        ];
        $data['response_status_code'] = $response->getStatusCode();
        $data['response_status_message'] = $response->getReasonPhrase();
        return $data;
    }

    /**
     * @param $request
     * @param $response
     * @param $url
     */
    private function logRequestResponse($request, $response, $url)
    {
        $req = [
            'headers' => $this->headers,
            'body'    => $request
        ];
        $this->log->debug("Request Body", array($req));
        $this->log->debug("Response Body", array($response));
    }
}
