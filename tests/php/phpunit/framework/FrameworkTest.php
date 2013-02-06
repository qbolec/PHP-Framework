<?php

class FrameworkTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $framework = Framework::get_instance();
    $this->assertInstanceOf('IFramework',$framework);
    $this->assertInstanceOf('ILogger',$framework->get_logger());
    $this->assertInstanceOf('IAssertions',$framework->get_assertions());
    $this->assertInstanceOf('IOutput',$framework->get_output());
    $this->assertInstanceOf('IRequestFactory',$framework->get_request_factory());
    $this->assertInstanceOf('IResponseFactory',$framework->get_response_factory());
    $this->assertInstanceOf('IPDOFactory',$framework->get_pdo_factory());
    $this->assertInstanceOf('IRNG',$framework->get_rng());
    $this->assertInternalType('int',$framework->get_time());
    $this->assertInstanceOf('IShardingFactory',$framework->get_sharding_factory());
    $this->assertInstanceOf('IPersistenceManagerFactory',$framework->get_persistence_manager_factory());
    $this->assertInstanceOf('ICacheFactory',$framework->get_cache_factory());
    $this->assertInstanceOf('IPrefetchingCacheFactory',$framework->get_cache_factory());
    $this->assertInstanceOf('IValidationExceptionExplainerFactory',$framework->get_validation_exception_explainer_factory());
    $this->assertInstanceOf('IServerInfoFactory',$framework->get_server_info_factory());
    $this->assertInstanceOf('IRelationManagerFactory',$framework->get_relation_manager_factory());
    $this->assertInstanceOf('ITickets',$framework->get_tickets());
    $this->assertInstanceOf('ISignatures',$framework->get_signatures());
    $this->assertInstanceOf('ICacheVersioningFactory',$framework->get_cache_versioning_factory());
    $this->assertInstanceOf('IRedisFactory',$framework->get_redis_factory());
  }
}
?>
