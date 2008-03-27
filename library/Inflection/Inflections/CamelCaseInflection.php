<?php

class CamelCaseInflection extends Inflection
{
  /**
   * Makes a word camelcased.
   * 
   * If given an array of strings, capitalizes the first letter and lowercases
   * the rest for each string and joins them.
   * If given a string, capitalizes the first letter and lowercases the rest.
   * Also trims whitespace.
   *
   * @param unknown_type $source
   * @param unknown_type $args
   */
  public static function process( $source, $args = array() )
  {
    if( is_array($source) )
    {
      foreach( $source as &$word )
      {
        self::capitalize($word);
      }
      
      $source = implode('', $source);
    }
    else
    {
      self::capitalize($source);
    }
    
    return $source;
  }
  
  protected static function capitalize( &$word )
  {
    trim($source);
    $word = strtolower($word);
    $word[0] = strtoupper($word[0]);
  }
}