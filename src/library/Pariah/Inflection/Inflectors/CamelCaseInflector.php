<?php

class CamelCaseInflector extends Inflector
{
  protected static function process( $source, $args = array() )
  {
    $matches = array();
    preg_match_all('(^[a-zA-Z][a-z]*|[A-Z][a-z]*)', $source, $matches );
    
    if( !empty($matches[0]) )
    {
      return $matches[0];
    }
    
    return array($source);
  }
}//