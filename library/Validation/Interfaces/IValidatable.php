<?php
/**
 * IValidatableSemantically.php
 *
 * This file provides the Validatable interface.
 */
 
/**
 * The Validatable interface.
 */
interface IValidatable
{
  /**
   * Used to check if an object is valid at the current time.
   *
   * @return bool True if valid, otherwise false.
   */
  public function isValid();
  
  /**
   * Used to get the errors which prevent an object from being valid.
   *
   * @return ErrorSet An ErrorSet containing the object's errors.
   */
  public function getErrors();
}

?>