<?php

namespace JohnDoe\BlogPackage\Tests;

use Mabrouk\Filterable\FilterableServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
  public function setUp(): void
  {
    parent::setUp();
    // additional setup
  }

  protected function getPackageProviders($app)
  {
    return [
        FilterableServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    //   perform environment setup
  }
}
