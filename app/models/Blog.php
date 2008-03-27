<?php

class Blog extends Resource
{
  public function __construct( $data = null, $id = null, $dirty = true )
  {
    // Call parent constructor
    parent::__construct( $data, $id, $dirty );
    
    // Add fields/structures
    $this->addField('title')
         ->addStructure('BlogArticle', 'OM');
  }
}

?>