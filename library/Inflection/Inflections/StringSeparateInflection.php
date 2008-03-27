<?php

class StringSeparateInflection extends Inflection
{
  public static function process( $source, $args = array() )
  {
    if( is_array($source) )
      return implode(isset($args['separator']) ? $args['separator'] : '_', $source);
    else
      return $source;
  }
}