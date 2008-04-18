<?php

class LowerCaseInflection extends Inflection
{
  public static function process( $source )
  {
    if( is_array($source) )
    {
      foreach( $source as &$word )
      {
        $word = strtolower($word);
      }
    }
    else
    {
      $source = strtolower($source);
    }
    
    return $source;
  }
}