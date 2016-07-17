<?php

namespace IGonics\Migrations\Factories;

use IGonics\Migrations\Laravel\5\1\SpecificFilesMigrator as L51SpecificFilesMigrator;
use IGonics\Migrations\Laravel\5\2\SpecificFilesMigrator as L52SpecificFilesMigrator;

class MigratorFactory {

	public static function SpecificMigratorClassName($version=null){
        if($version==null){
             $version = static::GetIlluminateSupportDatabaseVersion();
        }

        switch ($version) {
        	case '5.2':
        		return L52SpecificFilesMigrator::class;
        	case '5.1':
        		return L51SpecificFilesMigrator::class;
        	default:
        		return L52SpecificFilesMigrator::class;
        }

	}

	public static function GetIlluminateSupportDatabaseVersion(){
        $migratorClass = 'Illuminate\Database\Migrations\Migrator';
        $differingMethod = 'run';
        $params = static::GetMethodParamsWithKeyNames($migratorClass,$differingMethod);

        if(static::isIlluminateSupportDatabaseVersion52($params))
        	return '5.2';
        	
        if(static::isIlluminateSupportDatabaseVersion51($params))
        	return '5.1';

        return null;
	}

	protected static function isIlluminateSupportDatabaseVersion51($params){
        if( array_key_exists('path', $params) ){
        	if( array_key_exists('pretend', $params) ){
                 return $params['pretend']->isOptional();
        	}
        }
        return false;
	}

	protected static function isIlluminateSupportDatabaseVersion52($params){
		if( array_key_exists('path', $params) ){
        	return array_key_exists('options', $params) 
        	   &&  $params['options']->hasType() 
        	   &&  $params['options']->isOptional();
        }
        return false;
	}

	private static function GetMethodParamsWithKeyNames($class, $method){
		$r = new ReflectionMethod($className, $methodName);
        $params = $r->getParameters();
        $res = [];
        foreach ($params as $param) {
            //$param is an instance of ReflectionParameter
            $res[$param->getName()] = $param;
        }
        return $res;
	}
	
}