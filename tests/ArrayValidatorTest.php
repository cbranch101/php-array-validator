<?php

	require_once('/Users/cbranch101/Sites/clay/movement_strategy/array_validator/array_validator.php');
	require_once('/Users/cbranch101/Sites/clay/movement_strategy/array_validator/tests/test_files/FunctionalTest.php');
	
	
	class ArrayValidatorTest extends FunctionalTest {
		
		public $verifyExpectedActual = true;
		
		public $verifyKey = null;
				
		public $levelsToDrillDown = 0;
		
		public $collectionsToReset = array();
				
		public function entryValidateMany() {
			$test = $this;
			return array(
				'get_output' => function($input, $extraParams) use($test){	
					$arraysToValidate = $input['arrays_to_validate'];
					$requiredStructure = $input['required_structure'];
					$invalidArrays = ArrayValidator::validateMany($arraysToValidate, $requiredStructure);
					return array(
						'invalid_arrays' => $invalidArrays,
					);
				},
			);
		}
		
		public function entryValidateOne() {
			$test = $this;
			return array(
				'get_output' => function($input, $extraParams) use($test){	
					$arrayToValidate = $input['array_to_validate'];
					$requiredStructure = $input['required_structure'];
					$invalidArray = ArrayValidator::validateOne($arrayToValidate, $requiredStructure);
					return array(
						'invalid_array' => $invalidArray,
					);
				},
			);
		}
		
		public function configValidateOneWithFailure() {
			return array(
				'input' => array(
					'array_to_validate' => array(
						'bar' => 1,
					),
					'required_structure' => array(
						'test' => array(
							'foo' => 'number',
							'bar' => 'string',
							'bazz' => 'array',
							'test' => array(
								'oop' => 'number',
							),
						),
					),
				),
				'assert_input' => array(
					'expected' => array(
						'invalid_array' => array(
								'test' => 'not_set',
						),
					),
				),
			);
		}
		
		public function configValidateOneWithSuccess() {
			return array(
				'input' => array(
					'array_to_validate' => array(
						'test' => array(
							'foo' => 1,
							'bar' => 'test',
							'bazz' => array(
								1,
							),
							'test' => array(
								'oop' => 1,
							),
						),
					),
					'required_structure' => array(
						'test' => array(
							'foo' => 'number',
							'bar' => 'string',
							'bazz' => 'array',
							'test' => array(
								'oop' => 'number',
							),
						),
					),
				),
				'assert_input' => array(
					'expected' => array(
						'invalid_array' => null,
					),
				),
			);
		}
		
		public function configValidateManyWithFailures() {
			return array(
				'input' => array(
					'arrays_to_validate' => array(
						array(
							'test' => array(
								'bar' => 1,
								'foo' => 'test',
								'bazz' => 1,
								'test' => array(
									'buzz' => 1,
								),
							),
						),
						array(
							'bar' => 1,
						),
					),
					'required_structure' => array(
						'test' => array(
							'foo' => 'number',
							'bar' => 'string',
							'bazz' => 'array',
							'test' => array(
								'oop' => 'number',
							),
						),
					),
				),
				'assert_input' => array(
					'expected' => array(
						'invalid_arrays' => array(
							array(
								'test' => array(
									'foo' => array(
										'wrong_type' => array(
											'required' => 'number',
											'actual' => 'string',
										),
									),
									'bar' => array(
										'wrong_type' => array(
											'required' => 'string',
											'actual' => 'integer',
										),
									),
									'bazz' => array(
										'wrong_type' => array(
											'required' => 'array',
											'actual' => 'integer',
										),
									),
									'test' => array(
										'oop' => 'not_set',
									),
								),
							),
							array(
								'test' => 'not_set',
							),
						),
					),
				),
			);
		}
		
		public function configValidateManyWithSuccess() {
			return array(
				'input' => array(
					'arrays_to_validate' => array(
						array(
							'test' => array(
								'foo' => 1,
								'bar' => 'test',
								'bazz' => array(
									1,
								),
								'test' => array(
									'oop' => 1,
								),
							),
						),
						array(
							'test' => array(
								'foo' => 2,
								'bar' => 'test',
								'bazz' => array(
									2,
								),
								'test' => array(
									'oop' => 2,
								),
							),
						),
					),
					'required_structure' => array(
						'test' => array(
							'foo' => 'number',
							'bar' => 'string',
							'bazz' => 'array',
							'test' => array(
								'oop' => 'number',
							),
						),
					),
				),
				'assert_input' => array(
					'expected' => array(
						'invalid_arrays' => array(),
					),
				),
			);
		}
		
		public function testValidateManyWithFailures() {
			$test = array(
				'configuration' => 'validate_many_with_failures',
				'entry_point' => 'validate_many',
			);
			self::buildTest($test);
		}
		
		public function testValidateManyWithSuccess() {
			$test = array(
				'configuration' => 'validate_many_with_success',
				'entry_point' => 'validate_many',
			);
			self::buildTest($test);
		}
		
		public function testValidateOneWithFailure() {
			$test = array(
				'configuration' => 'validate_one_with_failure',
				'entry_point' => 'validate_one',
			);
			self::buildTest($test);
		}
		
		public function testValidateOneWithSuccess() {
			$test = array(
				'configuration' => 'validate_one_with_success',
				'entry_point' => 'validate_one',
			);
			self::buildTest($test);
		}
		
	}
