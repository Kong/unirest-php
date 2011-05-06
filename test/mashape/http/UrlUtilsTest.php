<?php 

require_once(MASHAPE_CLIENT_LIBRAY_PATH . "/http/UrlUtils.php");

class UrlUtilsTest extends PHPUnit_Framework_TestCase {
	
	function testPrepareRequest() {
		$url = "http://www.ciao.com";
		$parameters = null;
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com", $url);
		$this->assertEquals(array(), $parameters);
		
		$url = "http://www.ciao.com";
		$parameters = array();
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com", $url);
		$this->assertEquals(array(), $parameters);
		
		$url = "http://www.ciao.com/{id}";
		$parameters = null;
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/", $url);
		$this->assertEquals(array(), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}";
		$parameters = null;
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/", $url);
		$this->assertEquals(array(), $parameters);

		$url = "http://www.ciao.com/{id}?name={name}";
		$parameters = array("id"=>12);
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12", $url);
		$this->assertEquals(array("id"=>"12"), $parameters);

		$url = "http://www.ciao.com/{id}?name={name}";
		$parameters = array("id"=>12, "name"=>"tom");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?name=tom", $url);
		$this->assertEquals(array("id"=>12, "name"=>"tom"), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}&opt=1";
		$parameters = array("id"=>12, "name"=>"tom");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?name=tom&opt=1", $url);
		$this->assertEquals(array("id"=>12, "name"=>"tom"), $parameters);

		$url = "http://www.ciao.com/{id}?name={name}&opt=1";
		$parameters = array("id"=>12, "name"=>"tom jerry");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?name=tom+jerry&opt=1", $url);
		$this->assertEquals(array("id"=>12, "name"=>"tom jerry"), $parameters);

		$url = "http://www.ciao.com/{id}?name={name}&opt=1&nick={nick}";
		$parameters = array("id"=>12, "name"=>"tom jerry");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?name=tom+jerry&opt=1", $url);
		$this->assertEquals(array("id"=>12, "name"=>"tom jerry"), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick={nick}";
		$parameters = array("id"=>12, "name"=>"tom jerry", "nick"=>"sinz");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?name=tom+jerry&nick=sinz", $url);
		$this->assertEquals(array("id"=>12, "name"=>"tom jerry", "nick"=>"sinz"), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick={nick}";
		$parameters = array("id"=>12, "name"=>"tom jerry", "opt"=>"yes", "nick"=>"sinz");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?name=tom+jerry&opt=yes&nick=sinz", $url);
		$this->assertEquals(array("id"=>12, "name"=>"tom jerry", "opt"=>"yes", "nick"=>"sinz"), $parameters);

		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick={nick}";
		$parameters = array("id"=>12, "opt"=>"yes", "nick"=>"sinz");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?opt=yes&nick=sinz", $url);
		$this->assertEquals(array("id"=>12, "opt"=>"yes", "nick"=>"sinz"), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick={nick}";
		$parameters = array("id"=>12, "opt"=>"yes");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?opt=yes", $url);
		$this->assertEquals(array("id"=>12, "opt"=>"yes"), $parameters);

		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick={nick}";
		$parameters = array("id"=>12, "nick"=>"sinz");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12?nick=sinz", $url);
		$this->assertEquals(array("id"=>12, "nick"=>"sinz"), $parameters);

		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick={nick}";
		$parameters = array("id"=>12);
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12", $url);
		$this->assertEquals(array("id"=>12), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick={nick}";
		$parameters = array("id"=>12, "pippo"=>null);
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/12", $url);
		$this->assertEquals(array("id"=>12), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick=some+nick";
		$parameters = array("id"=>"ciao marco", "name"=>"ciao pippo", "opt"=>"2");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/ciao%20marco?name=ciao+pippo&opt=2&nick=some+nick", $url);
		$this->assertEquals(array("id"=>"ciao marco", "name"=>"ciao pippo", "opt"=>"2"), $parameters);
		
		$url = "http://www.ciao.com/{id}?name={name}&opt={opt}&nick=some+nick";
		$parameters = array("id"=>"ciao marco", "name"=>"ciao pippo", "opt"=>"{this is opt}");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com/ciao%20marco?name=ciao+pippo&opt=%7Bthis+is+opt%7D&nick=some+nick", $url);
		$this->assertEquals(array("id"=>"ciao marco", "name"=>"ciao pippo", "opt"=>"{this is opt}"), $parameters);
	}
	
	function testAddClientParameters() {
		$url = "http://www.ciao.com";
		$parameters = array();
		UrlUtils::addClientParameters($url, $parameters, null);
		$this->assertEquals("http://www.ciao.com?_token={_token}&_language={_language}&_version={_version}", $url);
		$this->assertEquals(array("_token"=>null, "_language"=>"PHP", "_version"=>"V03"), $parameters);
		
		$url = "http://www.ciao.com?name={name}";
		$parameters = array("name"=>"Marco");
		UrlUtils::addClientParameters($url, $parameters, null);
		$this->assertEquals("http://www.ciao.com?name={name}&_token={_token}&_language={_language}&_version={_version}", $url);
		$this->assertEquals(array("name"=>"Marco", "_token"=>null, "_language"=>"PHP", "_version"=>"V03"), $parameters);
		
		$url = "http://www.ciao.com?name={name}";
		$parameters = array("name"=>"Marco");
		UrlUtils::addClientParameters($url, $parameters, "a-random-token");
		$this->assertEquals("http://www.ciao.com?name={name}&_token={_token}&_language={_language}&_version={_version}", $url);
		$this->assertEquals(array("name"=>"Marco", "_token"=>"a-random-token", "_language"=>"PHP", "_version"=>"V03"), $parameters);

		$url = "http://www.ciao.com?name={name}";
		$parameters = array("name"=>"Marco");
		UrlUtils::addClientParameters($url, $parameters, "a-random-token");
		UrlUtils::prepareRequest($url, $parameters);
		$this->assertEquals("http://www.ciao.com?name=Marco&_token=a-random-token&_language=PHP&_version=V03", $url);
		$this->assertEquals(array("name"=>"Marco", "_token"=>"a-random-token", "_language"=>"PHP", "_version"=>"V03"), $parameters);
	}
	
}

?>