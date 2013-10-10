<?php
	
	require_once('/Users/cbranch101/Sites/clay/movement_strategy/functional_test_builder/functional_test_builder.php');
	require_once('/Users/cbranch101/Sites/clay/movement_strategy/underscore_kit/underscore_kit.php');
	require_once('/Users/cbranch101/Sites/clay/movement_strategy/php_mongorm/php_mongorm.php');
		
	abstract class FunctionalTest extends PHPUnit_Framework_TestCase {
		
		static $functionalBuilderConfig;
				
		public $verifyExpectedActual = true;
		public $verifyKey = null;
		
		protected $mongoDatabase = 'test';
		protected $mongoHost = 'localhost';
		
		public $levelsToDrillDown = 0;
		public $collectionsToReset = array();
		
								
		function __construct() {
			__kit::initialize();
			self::$functionalBuilderConfig = self::getFunctionalBuilderConfig();
			if(count($this->collectionsToReset) > 0) {
				MongORM::connect($this->mongoHost, $this->mongoDatabase);
				$this->resetCollections();
			}
		}
		
		public function tearDown() {
			if(count($this->collectionsToReset) > 0) {
				$this->resetCollections();
			}
		}
				
		public function getFunctionalBuilderConfig() {
			return array(
				'configuration_map' => self::getConfigurationMap(),
				'entry_point_map' => self::getEntryPointMap(),
			);
		}
		
		public function getExpectedActualFunction() {
			$test = $this;
			$expAct = function($expectedActual)  use($test){
				return $test->buildExpectedActualArgs($expectedActual['expected'], $expectedActual['actual']);
			};
			
			return $expAct;
		}
		
		public function buildExpectedActualArgs($expected, $actual) {
			if($expected != $actual && $this->verifyExpectedActual) {
				if($this->levelsToDrillDown == 0 && $this->verifyKey == null) {
					$output = Test_Builder::confirmExpected($expected, $actual);
				} else {
					$output = Test_Builder::confirmExpectedWithDrillDown(array(), $expected, $actual, $this->levelsToDrillDown, false, $this->verifyKey);
				}
				print_r($output);
			}
			return array(
				 'expected' => $expected,
				 'actual' => $actual,
			);
		}
		
		public function populateCollections($inputData) {
			self::resetCollections();
			foreach(self::$collectionsToPopulate as $collectionToPopulate) {
				if(isset($inputData[$collectionToPopulate])) {
					$recordsToCreate = $inputData[$collectionToPopulate];
					MongORM::for_collection($collectionToPopulate)
						->create_many($recordsToCreate);
				} 
			}
		}
		
		public function getAllFromCollections() {
			return __::chain(self::$collectionsToPopulate)
				->map(function($collectionToPopulate){
					$data = MongORM::for_collection($collectionToPopulate)
						->find_many()
						->as_array();
						
					return array(
						$collectionToPopulate => $data,
					);
				})
				->flatten(true)
			->value();
		}
		
		public function resetCollections() {
			foreach(self::$collectionsToPopulate as $collectionToPopulate) {
				MongORM::for_collection($collectionToPopulate)
					->delete_many();
			} 
		}
		
		static function getColumns() {
			return array(
				'facebook_posts' => array(
					array(
						'id' => 'created_time',
						'name' => 'Posted On',
					),
					array(
						'id' => 'message',
						'name' => 'Post Content',
					),
					array(
						'id' => 'impressions',
						'name' => 'Total Impressions',
					),
					array(
						'id' => 'unique_impressions',
						'name' => 'Unique Impressions',
					),
					array(
						'id' => 'organic_unique_impressions',
						'name' => 'Organic Unique Impressions',
					),
					array(
						'id' => 'likes',
						'name' => 'Likes',
					),
					array(
						'id' => 'comments',
						'name' => 'Comments',
					),
					array(
						'id' => 'shares',
						'name' => 'Shares',
					),
					array(
						'id' => 'bitly_clicks',
						'name' => 'Bitly Clicks',
					),
					array(
						'id' => 'visits',
						'name' => 'Visits',
					),
					array(
						'id' => 'visitors',
						'name' => 'Visitors',
					),
					array(
						'id' => 'page_views',
						'name' => 'Page Views',
					),
					array(
						'id' => 'revenue',
						'name' => 'Revenue',
					),
					array(
						'id' => 'revenue_per_visit',
						'name' => 'Revenue Per Visit',
					),
					array(
						'id' => 'time_on_site',
						'name' => 'Time On Site',
					),
					array(
						'id' => 'percent_new_visits',
						'name' => 'Percent New Visits',
					),
				),
				'instagram_posts' => array(
					array(
						'id' => 'created_time',
						'name' => 'Posted On',
					),
					array(
						'id' => 'image',
						'name' => 'Image',
					),
					array(
						'id' => 'caption',
						'name' => 'Caption',
					),
					array(
						'id' => 'likes',
						'name' => 'Likes',
					),
					array(
						'id' => 'comments',
						'name' => 'Comments',
					),
				),
			);
		}
		
		public function buildTest($test) {
			Test_Builder::buildTest($test, self::$functionalBuilderConfig);
		}
				
		public function getEntryPointMap() {
			return self::buildIndexArrayBasedOnPrefix('entry');
		}
		
		public function getConfigurationMap() {
			return self::buildIndexArrayBasedOnPrefix('config');
		}
		
		public function buildIndexArrayBasedOnPrefix($prefix) {
			$indexedArray = array();
			$classMethods = get_class_methods($this);
			$test = $this;
			__::map($classMethods, function($classMethod) use($prefix, &$indexedArray, $test){
				$pieces = preg_split('/(?=[A-Z])/',$classMethod);
				if($pieces[0] == $prefix) {
					
					// remove the first piece
					$firstPiece = array_shift($pieces);
					
					// convert the function name to lower case
					$pieces = __::map($pieces, function($piece){
						return strtolower($piece);
					});
					
					// add underscores between the pieces to get the key
					// that will be used to hold the contents
					$key = implode('_', $pieces);
					
					// get the contents that are going to be added to the indexed array
					$contents = call_user_func(array($test, $classMethod));
					// update the indexed array
					$indexedArray[$key] = $contents;
				}
			});
			
			return $indexedArray;
		}
		
		public function entryAll() {
			$expAct = self::getExpectedActualFunction();
			return array(
				'test' => $this,
				'build_input' => function($input) {
					return $input;
				},
				'get_assert_args' => function($output, $assertInput) use($expAct){
					return $expAct(
						array(
							'expected' => $assertInput['expected'],					
							'actual' => $output,
						)
					);
				},
				'input' => array(),
				'extra_params' => array(),
				'assert_input' => array(),
				'asserts' => array (
					'assertEquals' => array(
						'expected', 
						'actual',
					),
				),
			);
		}
				
	}
		
