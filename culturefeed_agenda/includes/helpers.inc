<?php
/**
 * @file
 * Helper functions for the culturefeed agenda.
 **/

/**
 * Get the current active agenda object.
 */
function culturefeed_agenda_get_active_object() {

  $supported_types = array(
    'e' => 'culturefeed_agenda_event',
    'p' => 'culturefeed_agenda_production',
  );

  if (arg(0) == 'agenda' && isset($supported_types[arg(1)])) {
    return menu_get_object($supported_types[arg(1)], 3);
  }

}

/**
 * Get all the links from a given item.
 * @var CultureFeed_Cdb_Item_Base $item
 */
function _culturefeed_agenda_get_links_from_item(CultureFeed_Cdb_Item_Base $item) {

  global $language;
  $detail = $item->getDetails()->getDetailByLanguage($language->language);

  $links = array();
  // General links.
  if ($detail) {
    $media_links = $detail->getMedia()->byMediaType(CultureFeed_Cdb_Data_File::MEDIA_TYPE_WEBRESOURCE);
		foreach ($media_links as $media_link) {
			$links[] = $media_link->getHLink();
		}
	}

  // Contact info links.
  if (method_exists($item, 'getContactInfo') && $item->getContactInfo()) {
    $contact_links = _culturefeed_agenda_get_contact_links($item->getContactInfo());
    $links += $contact_links['main'];
    $links += $contact_links['other'];
  }

  // Location links.
  if (method_exists($item, 'getLocation') && $item->getLocation() && $item->getLocation()->getActor() && $item->getLocation()->getActor()->getContactInfo()) {
    $contact_links += _culturefeed_agenda_get_contact_links($item->getLocation()->getActor()->getContactInfo());
    $links += $contact_links['main'];
    $links += $contact_links['other'];
  }

  // Organiser links.
  if (method_exists($item, 'getOrganiser') && $item->getOrganiser() && $item->getOrganiser()->getActor() && $item->getOrganiser()->getActor->getContactInfo()) {
    $contact_links = _culturefeed_agenda_get_contact_links($item->getOrganiser()->getActor->getContactInfo());
    $links += $contact_links['main'];
    $links += $contact_links['other'];
  }

  // Performer links.
  if ($detail && method_exists($detail, 'getPerformers') && $detail->getPerformers()) {
    $performers = $detail->getPerformers();
    foreach ($performers as $performer) {
      if ($performer->getActor() && $performer->getActor()->getContactInfo()) {
        $contact_links = _culturefeed_agenda_get_contact_links($performer->getActor()->getContactInfo());
        $links += $contact_links['main'];
        $links += $contact_links['other'];
      }
    }
  }

  // Cleaning up links.
  foreach ($links as $i => $link) {
    $links[$i] = rtrim($link, '/');
  }

  $links = array_unique($links);

  return $links;

}

/**
 * Get the list of links from a given contact info. Seperate it in main and other.
 */
function _culturefeed_agenda_get_contact_links(CultureFeed_Cdb_Data_ContactInfo $info) {

  $links = array('main' => array(), 'other' => array());
  foreach ($info->getUrls() as $url) {
    if ($url->isMain()) {
      $links['main'][] = $url->getUrl();
    }
    else {
      $links['other'][] = $url->getUrl();
    }
  }

  return $links;

}