<?php
/**
 * @file
 * DrupalCultureFeedSearchService
 */

use \CultuurNet\Auth\ConsumerCredentials;

/**
 * Singleton for the CultureFeed Search Service.
 */
class DrupalCultureFeedSearchService {

  /**
   * @var DrupalCultureFeedSearchService
   */
  private static $searchService = NULL;

  /**
   * @var \CultuurNet\Search\Guzzle\Service
   */
  private $service = NULL;

  /**
   * Constructor
   *
   * @param ConsumerCredentials $consumerCredentials
   */
  private function __construct(ConsumerCredentials $consumerCredentials) {

    $endpoint = variable_get('culturefeed_search_api_location', CULTUREFEED_SEARCH_API_LOCATION);
    $service = new \CultuurNet\Search\Guzzle\Service($endpoint, $consumerCredentials);

    if (variable_get('culturefeed_search_cache_enabled', FALSE)) {
      $this->service = new DrupalCultureFeedSearchService_Cache($service,
        $consumerCredentials,
        DrupalCultureFeed::getLoggedInUserId());
    }
    else {
      $this->service = $service;
    }

  }

  /**
   * getClient().
   *
   * @param ConsumerCredentials $consumerCredentials
   * @return Ambigous <CultureFeed, DrupalCultureFeed_Cache>
   */
  public static function getClient(ConsumerCredentials $consumerCredentials) {
    if (!self::$searchService) {
      self::$searchService = new DrupalCultureFeedSearchService($consumerCredentials);
    }

    return self::$searchService;
  }

  /**
   * @see \CultuurNet\Search\Service::search().
   */
  public function search(Array $parameters = array(), $path = 'search') {
    return $this->service->search($parameters, $path);
  }

  /**
   * @see \CultuurNet\Search\Service::search().
   */
  public function searchPages(Array $parameters = array()) {
    return $this->service->searchPages($parameters);
  }

  /**
   * @see \CultuurNet\Search\Service::searchSuggestions().
   */
  public function searchSuggestions($search_string, $type = NULL) {
    return $this->service->searchSuggestions($search_string);
  }

}