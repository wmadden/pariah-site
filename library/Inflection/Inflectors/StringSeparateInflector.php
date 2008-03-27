<?php

class StringSeparateInflector extends Inflector
{
  protected static function process( $source, $args = array() )
  {
    // Separator defaults to '_'
    $separator = isset($args['separator']) ? $args['separator'] : '_';
    return explode($separator, $source);
  }
}