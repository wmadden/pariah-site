<?php

class Inflection
{
  public function __call($name, $args)
  {
    $inflection = $name . 'Inflection';
    
    // Call the specific inflection
    if( class_exists($inflection) )
    {
      /* VERY DANGEROUS HACKAROUND!
       * This hackaround is to allow use of a variable class type with the
       * scope resolution operator, which is not valid syntax until php 5.3.0.
       * (At the time of writing, the latest version of php is 5.2.5).
       * 
       * Todo: fix this when php 5.3.0 is released.
       */
      // Desired function call
      //return $inflection::process($args[0], $args[1]);
            
      // Dangerous hackaround
      return eval("return $inflection::process(\$args[0], \$args[1]);");
    }
    else
    {
      throw new InflectionException("No inflection '$inflection' can be found.", 0);
    }
  }
  
  protected static function process( $source, $args = array() )
  {
    return $source;
  }
}