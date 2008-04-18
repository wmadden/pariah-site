<?php

class TitleCaseInflection extends Inflection
{
  /**
   * Takes an array of words, and makes each of them start with a capital.
   * If given a string, makes the first letter a capital.
   *
   * @param unknown_type $source
   * @param unknown_type $args
   * @return unknown
   */
  public static function process( $source, $args = array() )
  {
    $reverse = isset($args['reverse']) ? $args['reverse'] : false;
    
    if( is_array($source) )
    {
      foreach($source as &$string)
      {
        self::titlecase($string, $reverse);
      }
    }
    else
      self::titlecase($source, $reverse);
    
    return $source;
  }
  
  protected static function titlecase( &$string, $reverse )
  {
    $string[0] = $reverse ? strtolower($string[0]) : strtoupper($string[0]);
  }
}