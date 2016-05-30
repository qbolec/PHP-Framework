<?php
class Strings
{
  public static function len($a){
    return mb_strlen($a,'UTF-8');
  }
  public static function charAt($a,$i){
    return self::substr($a,$i,1);
  }
  public static function substr($a,$x,$y=null){
    if($y===NULL){
      $y=self::len($a);
    }
    return mb_substr($a,$x,$y,'UTF-8');
  }
  public static function letters($a){
    $len = self::len($a);
    if($len==0){
      return array();
    }else if($len == 1){
      return array($a);
    }else{
      return Arrays::concat(
        self::letters(self::substr($a,0,$len>>1)),
        self::letters(self::substr($a,$len>>1))
      );
    }
  }
  private static function lcs_last_column(array $A,array $B){
    $al=count($A);
    $bl=count($B);
    $last_column = array();
    for($i=0;$i<=$al;++$i){
      $current_row = array();
      for($j=0;$j<=$bl;++$j){
        //$a[0,$i) vs $b[0,$j)
        if($i==0 || $j == 0){
          $v = 0;
        }else if($A[$i-1]===$B[$j-1]){
          $v = 1 + $last_row[$j-1];
        }else{
          $v = max($last_row[$j],$current_row[$j-1]);
        }
        $current_row[] = $v;
      }
      $last_column[] = $current_row[$bl];
      $last_row = $current_row;
    }
    return $last_column;
  }
  public static function lcs($a,$b){
    $A = self::letters($a);
    $B = self::letters($b);
    $bl=count($B);
    if($bl==0){
      return '';
    }else if($bl==1){
      return FALSE===array_search($B[0],$A,true)?'':$B[0];
    }
    $left=self::lcs_last_column($A,array_slice($B,0,$bl>>1));
    $right=array_reverse(self::lcs_last_column(array_reverse($A),array_reverse(array_slice($B,$bl>>1))));

    $best_i = 0;
    $best_lcs = 0;
    foreach($left as $i => $lcs_left){
      $option = $lcs_left + $right[$i];
      if($best_lcs < $option){
        $best_lcs = $option;
        $best_i = $i;
      }
    }
    return 
      self::lcs(self::substr($a,0,$best_i), self::substr($b,0,$bl>>1)).
      self::lcs(self::substr($a,$best_i), self::substr($b,$bl>>1));
  }
  public static function is_prefix_of($short,$long){
    return substr($long,0,strlen($short)) === $short;
  }
  public static function to_html($str){
    return htmlspecialchars($str,ENT_QUOTES);
  }
  public static function to_uri($str){
    return urlencode($str);
  }
  public static function number_of_chars($text){
    return mb_strlen($text,'UTF-8');
  }
}
?>
