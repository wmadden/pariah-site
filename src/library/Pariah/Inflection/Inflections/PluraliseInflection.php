<?php

class PluraliseInflection extends Inflection
{
  public static function process( $source, $args = array() )
  {
    if( is_array($source) )
    {
      foreach( $source as &$word )
      {
        $word = self::pluralise($word);
      }
    }
    else
    {
      $source = self::pluralise($source);
    }
    
    return $source;
  }
  
  protected static function pluralise( $word )
  {
    /* Algorithm to pluralise a word:
     *    1. If the last letter is an 'f', replace the 'f' with 'ves'
     *    2. If the last letter is an 'x', append 'es'
     *    3. If the last letter is an 's',
     *        1. If the second last letter is a consonant, leave it alone.
     *        2. Otherwise, add 'es'
     *    4. Otherwise, append 's'
     */
    $last_letter = substr($word, -1, 1);
    $second_last_letter = substr($word, -2, 1);
    
    switch($last_letter)
    {
      case 'f':
        $word = substr($word, 0, -1) . 'ves';
        break;
      case 'x':
        $word .= 'es';
        break;
      case 's':
        if( !self::consonant($second_last_letter) )
          $word .= 'es';
        break;
      default:
        $word .= 's';
        break;
    }
    
    return $word;
  }
  
  protected static function consonant( $letter )
  {
    return preg_match('[^aeiouAEIOU]', $letter);
  }
}