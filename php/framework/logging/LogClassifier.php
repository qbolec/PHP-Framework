<?php
class LogClassifier extends Singleton implements ILogClassifier
{
  private $rules;
  private $path_log_writer;
  private $classification_validator;
  public function __construct(){
    $this->rules = Config::get_instance()->get('logging/rules');
    $this->path_log_writer = Framework::get_instance()->get_logger()->get_log_writer_factory()->get_path_log_writer();
    $this->classification_validator = new RecordValidator(array(
      'verbosity' => new IntValidator(),
      'priority' => new NullableValidator(new IntValidator()),
    ));
  }
  public function classify(array $backtrace,$info){
    $default =array(
      'verbosity' => 2,
      'priority' => LOG_ERR,
    );
    try{
      $text = $this->path_log_writer->describe($backtrace,$info);
      foreach($this->rules as $pattern=>$result){
        //w razie bledow we wzorcu i tak niewiele mozna zrobic, dlatego malpka
        $match_result = @preg_match($pattern,$text);
        if(false===$match_result){
          return $default;
        }else if($match_result){
          if($this->classification_validator->is_valid($result)){
            return $result;
          }else{
            return $default;
          }
        }
      }
    }catch(Exception $e){
      //ostatnie czego chcemy, to sie wysypac na logowaniu
    }
    //przy dobrej konfiguracji, nie powinniśmy tu nigdy dochodzić:
    return $default;
  }
}
?>
