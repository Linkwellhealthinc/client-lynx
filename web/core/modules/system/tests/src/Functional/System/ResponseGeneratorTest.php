<?php

namespace Drupal\Tests\system\Functional\System;

use Drupal\rest\Entity\RestResourceConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests to see if generator header is added.
 *
 * @group system
 */
class ResponseGeneratorTest extends BrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  protected static $modules = ['hal', 'rest', 'node', 'basic_auth'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->drupalCreateContentType(['type' => 'page', 'name' => 'Basic page']);

    $account = $this->drupalCreateUser(['access content']);
    $this->drupalLogin($account);
  }

  /**
   * Tests to see if generator header is added.
   */
  public function testGeneratorHeaderAdded() {

    $node = $this->drupalCreateNode();

    [$version] = explode('.', \Drupal::VERSION, 2);
    $expectedGeneratorHeader = 'Drupal ' . $version . ' (https://www.drupal.org)';

    // Check to see if the header is added when viewing a normal content page
    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseHeaderEquals('Content-Type', 'text/html; charset=UTF-8');
    $this->assertSession()->responseHeaderEquals('X-Generator', $expectedGeneratorHeader);

    // Check to see if the header is also added for a non-successful response
    $this->drupalGet('llama');
    $this->assertSession()->statusCodeEquals(404);
    $this->assertSession()->responseHeaderEquals('Content-Type', 'text/html; charset=UTF-8');
    $this->assertSession()->responseHeaderEquals('X-Generator', $expectedGeneratorHeader);

    // Enable cookie-based authentication for the entity:node REST resource.
    /** @var \Drupal\rest\RestResourceConfigInterface $resource_config */
    $resource_config = RestResourceConfig::load('entity.node');
    $configuration = $resource_config->get('configuration');
    $configuration['authentication'][] = 'cookie';
    $resource_config->set('configuration', $configuration)->save();
    $this->rebuildAll();

    // Tests to see if this also works for a non-html request
    $this->drupalGet($node->toUrl()->setOption('query', ['_format' => 'hal_json']));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseHeaderEquals('Content-Type', 'application/hal+json');
    $this->assertSession()->responseHeaderEquals('X-Generator', $expectedGeneratorHeader);

  }

}
