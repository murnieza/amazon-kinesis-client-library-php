<?php 

require '../vendor/autoload.php';
require '../src/Rf/Aws/AutoLoader.php';

use Aws\Kinesis\KinesisClient;
use Aws\Common\Enum\Region;
use Rf\Aws\AutoLoader;
use Rf\Aws\Kinesis\ClientLibrary\KinesisProxy;
use Rf\Aws\Kinesis\ClientLibrary\KinesisShardFileDataStore;
use Rf\Aws\Kinesis\ClientLibrary\KinesisShardMemcacheDataStore;
use Rf\Aws\Kinesis\ClientLibrary\KinesisStorageManager;

define('STREAM_NAME', 'kinesis-trial');

AutoLoader::register();

$kinesis = KinesisClient::factory(array(
  'key' => 'XXXXX',
  'secret' => 'XXXXX',
  'region' => Region::VIRGINIA
));

$kinesis_proxy = KinesisProxy::factory($kinesis, 'kinesis-trial');
$kinesis_storage_manager = new KinesisStorageManager($kinesis_proxy, new KinesisShardFileDataStore('/tmp/amazon-kinesis'));

// $memcache = new Memcache;
// $memcache->addServer("localhost", 11211);
// $kinesis_proxy = KinesisProxy::factory($kinesis, new KinesisShardMemcacheDataStore($memcache), STREAM_NAME);
$data_records = $kinesis_storage_manager->findWithMergeStoreDataRecords(null, 10, 5);

foreach ($data_records as $data_record) {
  echo $data_record->getData(), PHP_EOL;
}

$kinesis_storage_manager->saveAll();