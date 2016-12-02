<?php

require_once "aws.phar";

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\AcpBuilder;

class ArquivosAws{
	// use \Aws;
	// // Instantiate the S3 client with your AWS credentials
	private $client = null;

	private $bucket = null;


	public function __construct($key = '', $secret = '', $bucket = ''){
		$this->bucket = $bucket;
		$this->client = new S3Client(array(
		    'credentials' => array(
		        'key'    => $key,
		        'secret' => $secret
		    ),
		    'region' => 'us-west-2',
		    'version' => '2006-03-01'
		));
	}
	
	/**
	*	Envia diretório subindo todos os arquivos
	*/
	public function uploadDir($dir, $bucket='', $keyPrefix = ''){
		if(!empty($bucket)){
			$this->bucket = $bucket;
		}
		return $this->client->uploadDirectory($dir, $this->bucket, $keyPrefix, array(
		    'params'      => array('ACL' => 'public-read'),
		    'concurrency' => 20,
		    'debug'       => true
		));
	}
	
	/**
	*	Cria regras de acesso ao arquivo
	*/
	public function acpBuilder(){
		return AcpBuilder::newInstance()
		    ->setOwner($myOwnerId)
		    ->addGrantForEmail('READ', 'test@example.com')
		    ->addGrantForUser('FULL_CONTROL', 'user-id')
		    ->addGrantForGroup('READ', Group::AUTHENTICATED_USERS)
		    ->build();
	}

	/**
	*	Envia um arquivo do sistema de arquivos
	*/
	public function uploadFile($origem, $destino, $wait = false, $metadata = []){
		//grava arquivo do sistema de arquivos
		$ret =  $this->client->putObject(array(
		    'Bucket' => $this->bucket,
		    'Key'    => $destino,
		    'SourceFile' => $origem,
		    'ACL'        => 'public-read',
		    'Metadata'   => $metadata
		));	

		if($wait==true){
			// We can poll the object until it is accessible
			$this->client->waitUntil('ObjectExists', array(
			    'Bucket' => $this->bucket,
			    'Key'    => $destino
			));
		}

		return $ret;
	}
	
	/**
	*	Grava o conteúdo passado em um arquivo remodo do bucket
	*/
	public function uploadFileContent($arquivo, $conteudo){
		// grava arquivo com conteúdo passado
		return $this->client->putObject(array(
		    'Bucket' => $this->bucket,
		    'Key'    => $arquivo,
		    'Body'   => $conteudo
		));
	}

	/**
	*
	*/
	public function listBuckets(){
		$result = $this->client->listBuckets();
		return $result['Buckets'];
	}

	/**
	*	Retorna lista com todos objetos do bucket, usar cuidadosamente
	*/
	public function listObjects(){
		$iterator = $this->client->getIterator('ListObjects', array(
		    'Bucket' => $this->bucket
		));
		$ret = [];
		foreach ($iterator as $object) {
		    $ret[] = $object['Key'];
		}
		return $ret;
	}
}