<?php
interface INormalizer
{
  /**
   * throws CouldNotConvertException
   */
  public function normalize($data);
}
?>
