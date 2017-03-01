<?php


use IGonics\Migrations\Laravel\L5\V1\SpecificFilesMigrator as L51SpecificFilesMigrator;
use IGonics\Migrations\Laravel\L5\V2\SpecificFilesMigrator as L52SpecificFilesMigrator;
use IGonics\Migrations\Laravel\L5\V3\SpecificFilesMigrator as L53SpecificFilesMigrator;
use IGonics\Migrations\Factories\MigratorFactory;

class MigratorFactoryTestCase extends PHPUnit_Framework_TestCase {

	public function testSpecificMigratorClassName(){

		$this->assertEquals( 
			MigratorFactory::SpecificMigratorClassName('5.1'), 
			L51SpecificFilesMigrator::class
		);
		$this->assertEquals( 
			MigratorFactory::SpecificMigratorClassName('5.2'), 
			L52SpecificFilesMigrator::class
		);

		$this->assertEquals( 
			MigratorFactory::SpecificMigratorClassName('5.3'), 
			L53SpecificFilesMigrator::class
		);

		$this->assertEquals( 
			MigratorFactory::SpecificMigratorClassName(), 
			L53SpecificFilesMigrator::class
		);
	}


	public function testGetIlluminateSupportDatabaseVersion51(){
        $this->assertEquals(
        	MigratorFactory::GetIlluminateSupportDatabaseVersion('Laravel51MigratorStub','run'),
        	'5.1'
        );
	}

	public function testGetIlluminateSupportDatabaseVersion52(){
        $this->assertEquals(
        	MigratorFactory::GetIlluminateSupportDatabaseVersion('Laravel52MigratorStub','run'),
        	'5.2'
        );
	}

	public function testIsIlluminateSupportDatabaseVersion51(){
		$this->assertTrue(MigratorFactory::isIlluminateSupportDatabaseVersion51(
            $this->getDifferingMethodParams('Laravel51MigratorStub','run')
		));
	}

	public function testIsIlluminateSupportDatabaseVersion52(){
		$this->assertTrue(MigratorFactory::isIlluminateSupportDatabaseVersion52(
            $this->getDifferingMethodParams('Laravel52MigratorStub','run')
		));
	}

	protected function getDifferingMethodParams($class=null, $method = null){
		return $this->getMethodParamsWithKeyNames(
        	$class?:$this->getMigratorClass(),
        	$method?:$this->getMigratorTestMethod()
        );
	}

	protected function getMigratorClass(){
        return 'Illuminate\Database\Migrations\Migrator';
	}

	protected function getMigratorTestMethod(){
		return 'run';
	}

	protected function getMethodParamsWithKeyNames($className, $methodName){
		$r = new \ReflectionMethod($className, $methodName);
        $params = $r->getParameters();
        $res = [];
        foreach ($params as $param) {
            $res[$param->getName()] = $param;
        }
        return $res;
	}

}

class Laravel51MigratorStub{
	public function run($path, $pretend = false){} 
}

class Laravel52MigratorStub{
	public function run($path, array $options = []){} 
}