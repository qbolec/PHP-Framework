<?php
interface IRedisDB
{
  /**
   * @param $key string nazwa sortlisty
   * @param $score float jaką wagę ustawić. 
   * @param $member string nazwa elementu
   * @returns bool true = dodano nowy element, false = uaktualizowano stary
   * @throws CouldNotConvertException jeśli $key nie jest sortlistą
   */
  public function z_add($key,$score,$member);
  /**
   * @param $key string nazwa sortlisty
   * @param $score_delta float o ile zmienić.
   * @param $member string nazwa elementu
   * @returns int nowa waga elementu
   * @throws CouldNotConvertException jeśli $key nie jest sortlistą, lub nowa waga elementu to nie int
   */
  public function z_incr_by($key,$score_delta,$member);
  public function z_delete($key,$member);
  public function z_rev_range($key,$start,$stop);
  public function z_rev_range_with_scores($key,$start,$stop);
  public function delete($key);
  public function z_card($key);
  public function z_rev_rank($key,$member);
  /**
   * @param $members values of this array are used to find members in $key zset
   * @returns map<member,z_rev_rank> missing members are missing in result
   */
  public function z_rev_ranks($key,array $members);
  public function z_score($key,$member);
  /**
   * @param $members values of this array are used to find members in $key zset
   * @returns map<member,score> missing members are missing in result
   */
  public function z_scores($key,array $members);
  /**
   * @param $key string name of hashmap
   * @param $field string index in hashmap
   * @param $delta int amount to increment/decrement if negative
   * @returns int nowa wartość pola (jeśli go nie było lub nie było stringiem to $delta
   */
  public function h_incr_by($key,$field,$delta);
  /**
   * @param $key string name of hashmap
   * @param $field string index in hashmap
   * @returns string zawartość komórki jako string
   * @throws IsMissingException jeśli nie ma takiego klucza bądź indeksu
   */
  public function h_get($key,$field);

  /**
   * @param $key string name of hashmap
   * @param $field string index in hashmap
   * @param $value string zawartość komórki
   * @returns bool true jeśli nie było wcześniej takiego fielda
   * @throws CouldNotConvertException jeśli to nie jest tablica hashująca
   */
  public function h_set($key,$field,$value);
  /**
   * @param $key string name of hashmap
   * @returns map zawartość całej mapy
   * @throws IsMissingException jeśli nie ma takiego klucza bądź indeksu
   */
  public function h_get_all($key);
  /**
   * @param $key string name of the key
   * @param $delta int amount of increment/decrement
   * @returns int nowa wartość 
   */
  public function incr_by($key,$delta);
  /**
   *
   */
  public function evaluate(ILUAScript $script,array $keys,array $args);
}
?>
