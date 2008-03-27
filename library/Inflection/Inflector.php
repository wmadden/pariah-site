<?php

/**
 * Provides inflection functionality.
 */
class Inflector
{
  /**
   * Handles calls to undefined methods.
   * 
   * In this case, the __call magic method is used to handle calls to 
   * inflectors.
   * Inflector calls should be in the form
   *    <inflector name>(<source>, <inflection(s)>, <arguments>)
   *
   * @param unknown_type $name The name of the inflector.
   * @param unknown_type $args Arguments to the inflector.
   * @return The inflected string.
   */
  public function __call($name, $args)
  {
    $inflector = $name . 'Inflector';
    
    // Call the specific inflector
    if( class_exists($inflector) )
    {
      // Check that the given source is either a string or an array
      if( !is_array($args[0]) && !is_string($args[0]) )
        throw new InflectorException("The given source is not a string or array.", 1);
      /* VERY DANGEROUS HACKAROUND!
       * This hackaround is to allow use of a variable class type with the
       * scope resolution operator, which is not valid syntax until php 5.3.0.
       * (At the time of writing, the latest version of php is 5.2.5).
       * 
       * TODO: fix this when php 5.3.0 is released.
       */
      // Desired function call
      //return $inflector::inflect($args[0], $args[1]);
      
      // Dangerous hackaround
      return eval("return $inflector::inflect(\$args[0], \$args[1], \$args[2], $inflector);");
    }
    else
    {
      throw new InflectorException("No inflector '$inflector' can be found.", 0);
    }
  }
  
  /**
   * Custom processing function.
   * 
   * This function takes the source string and returns an array of substrings.
   * Inflection of these substrings forms the final string.
   *
   * @param string $source
   * 
   * @return array
   */
  protected static function process( $source )
  {
    throw new InflectorException('Inflector::process() should be overridden!', 1);
  }
  
/**
   * Returns an inflected string.
   *
   * @param string $source
   * @param array|string $inflections Either a map of inflection names to
   *    construction parameters, or the name of an inflection.
   */
  public static function inflect( $source, $inflections = null, $args = array(), $inflector = null )
  {
    // TODO: fix this with late static binding in PHP 5.3.0
    if( $inflector != null )
      // Process the source string
      $result = eval("return $inflector::process(\$source);");
    
    // If inflections is an array
    if( is_array($inflections) )
    {
      $static_inflection = new Inflection();
      foreach( $inflections as $key => $value )
      {
        $inflection = $value;
        if( is_array($value) )
        {
          $inflection = $key;
          $args = $value;
        }
        $result = $static_inflection->$inflection( $result, $args );
      }
    }
    // If inflections is a string (i.e. one inflection)
    else if( $inflections != null )
    {
      $result = Inflection::$inflections($result);
    }
    
    return $result;
  }
}