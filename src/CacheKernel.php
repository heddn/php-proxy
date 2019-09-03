<?php


namespace App;


use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class CacheKernel extends HttpCache
{
  /**
   * {@inheritDoc}
   */
  protected function getOptions()
  {
    return [
      'default_ttl' => 1800,
    ];
  }

}
