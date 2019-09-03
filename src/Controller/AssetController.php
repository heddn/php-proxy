<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AssetController extends AbstractController
{
  /**
   * The HTTP client.
   *
   * @var HttpClientInterface
   */
  private $client;

  public function __construct(HttpClientInterface $client)
  {
    $this->client = $client;
  }

    /**
     * @Route("/img/{asset}", name="asset", methods={"GET"}, requirements={"asset"=".+"})
     */
    public function index(string $asset)
    {
      try {
        $request = $this->client->request('GET', 'https://placekitten.com/' . $asset, [
          'buffer' => false,
        ]);
        // Fail quickly.
        if ($request->getStatusCode() >= 400) {
          return new Response('Error', 500);
        }
        $headers = $request->getHeaders(false);
        unset($headers['content-encoding'], $headers['set-cookie']);
        $streamResponse = new StreamedResponse();
        $streamResponse->setCallback(function() use ($request) {
          foreach ($this->client->stream($request) as $chunk) {
            echo $chunk->getContent();
          }
        });
        $streamResponse->headers = new ResponseHeaderBag($headers);
        $streamResponse->setStatusCode($request->getStatusCode());
        return $streamResponse;
      }
      catch (TransportExceptionInterface $exception) {
        return new Response($exception->getMessage(), 500);
      }

    }
}

