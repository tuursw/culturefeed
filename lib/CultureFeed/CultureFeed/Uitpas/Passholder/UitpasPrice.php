<?php

class CultureFeed_Uitpas_Passholder_UitpasPrice extends CultureFeed_Uitpas_ValueObject {

  const REASON_LOSS_THEFT = 'LOSS_THEFT';
  const REASON_REMOVAL = 'REMOVAL';
  const REASON_LOSS_KANSENSTATUUT = 'LOSS_KANSENSTATUUT';
  const REASON_OBTAIN_KANSENSTATUUT = 'OBTAIN_KANSENSTATUUT';

  /**
   * The ID of the price
   *
   * @var string
   */
  public $id;

  /**
   * The reason for which the price applies
   *
   * @var string
   */
  public $reason;

  /**
   * True if the price applies for kansenstatuut
   *
   * @var boolean
   */
  public $kansenStatuut;

  /**
   * The price of the uitpas
   *
   * @var float
   */
  public $price;

  /**
   * The age range the price is valid for
   *
   * @param CultureFeed_Uitpas_Passholder_AgeRange
   */
  public $ageRange;

  public function __construct() {
    $this->ageRange = new CultureFeed_Uitpas_Passholder_AgeRange();
  }

  public static function createFromXML(CultureFeed_SimpleXMLElement $object) {
    $price = new CultureFeed_Uitpas_Passholder_UitpasPrice();
    $price->id = $object->xpath_str('id');
    $price->reason = $object->xpath_str('reason');
    $price->kansenStatuut = $object->xpath_bool('kansenstatuut');
    $price->price = $object->xpath_float('price');

    $ageRange = $object->xpath('ageRange', FALSE);

    if ($ageRange) {
      $ageFrom = $ageRange->xpath_int('ageFrom', FALSE);
      if ($ageFrom) {
        $price->ageRange->ageFrom = $ageFrom;
      }

      $ageTo = $ageRange->xpath_int('ageTo', FALSE);
      if ($ageTo) {
        $price->ageRange->ageTo = $ageTo;
      }
    }

    return $price;
  }
}