<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
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
      $request = $this->client->request('GET', 'https://lmgtfy.com/?q=' . $asset, [
        'buffer' => false,
      ]);
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
}

