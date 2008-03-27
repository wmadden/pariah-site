<?php

class Role extends Model implements Zend_Acl_Role_Interface
{
  public function __construct($data = null, $id = null, $dirty = true)
  {
    // Set up model fields
    $this->addField('name');
    
    // Call parent constructor
    parent::__construct($data, $id, $dirty);
  }
  
  public function getRoleId()
  {
    return $this->getName();
  }
}