<?php

	require_once('/Users/cbranch101/Sites/clay/movement_strategy/array_validator/tests/test_files/FunctionalTest.php');
	
	class {Class Name} extends FunctionalTest {
		
		public $verifyExpectedActual = true;
		
		public $verifyKey = 'output';
				
		public $levelsToDrillDown = 0;
				
		public function entryBasic() {
			$test = $this;
			return array(
				'get_output' => function($input, $extraParams) use($test){	
					$test = array(
						'foo' => array(
							'test' => array(
								'number' => $input['number'],
								'other' => 2,
							),
						),
						'bar' => array(
							1,
						),
					);
					return array(
						'output' => $test,
					);
				},
			);
		}
		
		public function configBasic() {
			return array(
				'input' => array(
					'number' => 1
				),
				'assert_input' => array(
					'expected' => array(
						'output' => array(
							'foo' => array(
								'test' => array(
									'number' => 2,
									'other' => 2,
								),
							),
							'bar' => array(
								1,
							),
						),
					),
				),
			);
		}
		
		public function testBasic() {
			$test = array(
				'configuration' => 'basic',
				'entry_point' => 'basic',
			);
			self::buildTest($test);
		}
		
		
	}
