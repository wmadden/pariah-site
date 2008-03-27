<?php

class BlogArticle extends Resource
{
  public function __construct( $data = null, $id = null, $dirty = true )
  {
    // Call parent constructor
    parent::__construct( $data, $id, $dirty );

    // Add fields
    $this->addFields( array(
              'title',
              'author',
              'created',
              'body',
              'teaser',
              'date' 
    ));
  }
}

?>