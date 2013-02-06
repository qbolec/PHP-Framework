<?php
interface IFramework{
  public function get_logger();
  public function get_assertions();
  public function get_output();
  public function get_request_factory();
  public function get_response_factory();
  public function get_pdo_factory();
  public function get_rng();
  public function get_time();
  public function get_sharding_factory();
  public function get_persistence_manager_factory();
  public function get_cache_factory();
  public function get_redis_factory();
  public function get_validation_exception_explainer_factory();
  public function get_server_info_factory();
  public function get_relation_manager_factory();
  public function get_tickets();
  public function get_signatures();
  public function get_cache_versioning_factory();
  public function get_lock_factory();
  public function get_templates();
}
?>
