<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AssetsController extends AbstractController
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
     * @Route("/img/{asset}", name="images", methods={"GET"}, requirements={"asset"=".+"})
     */
    public function index(string $asset)
    {
      Debug::enable();
      $request = $this->client->request('GET', 'https://lmgtfy.com/?q=' . $asset, [
        'buffer' => false,
      ]);
      $headers = $request->getHeaders(false);
      unset($headers['content-encoding'], $headers['set-cookie']);
      $response = new Response($request->getContent(false), $request->getStatusCode(), $headers);
      return $response;
    }
}

