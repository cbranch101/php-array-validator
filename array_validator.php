<?php

	class ArrayValidator {
		
		public static function validateMany($arraysToValidate, $requiredStructure) {
			$invalidArrays = __::chain($arraysToValidate) 
				->map(function($arrayToValidate) use($requiredStructure){
					$invalidArray = ArrayValidator::validateCurrentLevel($arrayToValidate, $requiredStructure);
					return $invalidArray;
				})
				->compact()
			->value();						
			return $invalidArrays;
			
		}
		
		public static function validateOne($arrayToValidate, $requiredStructure) {
			return ArrayValidator::validateCurrentLevel($arrayToValidate, $requiredStructure);
		}
		
		public static function validateCurrentLevel($currentLevel, $requiredStructure) {
				$hasError = false;
				__::map($requiredStructure, function($requiredType, $requiredKey) use(&$requiredStructure, &$hasError, $currentLevel){
					if(__::has($currentLevel, $requiredKey)) {
						if(is_array($requiredType)) {
							$nextLevel = $currentLevel[$requiredKey];
							$invalidKeysOnNextLevel = ArrayValidator::validateCurrentLevel($nextLevel, $requiredType);
							if($invalidKeysOnNextLevel != null) {
								$requiredStructure[$requiredKey] = $invalidKeysOnNextLevel;
								$hasError = true;
							}
						} else {
							$value = $currentLevel[$requiredKey];
							if(ArrayValidator::valueIsValid($value, $requiredType)) {
								$requiredStructure[$requiredKey] = 'ok';
							} else {
								
								$requiredStructure[$requiredKey] = array(
									'wrong_type' => array(
										'required' => $requiredType,
										'actual' => gettype($value),
									),
								);
								
								$hasError = true;
								
							}
						}
					} else {
						$requiredStructure[$requiredKey] = 'not_set';
						$hasError = true;
					}			
				});
				return $hasError ? $requiredStructure : null;
		} 
		
		public static function valueIsValid($value, $requiredType) {
			
			$validationMap = array(
				'number' => function($value) {
					return is_numeric($value);
				},
				'array' => function($value) {
					return is_array($value);
				},
				'string' => function($value) {
					return is_string($value);
				},
			);
			
			$validationFunction = $validationMap[$requiredType];
			
			$isValid = $validationFunction($value);
						
			return $isValid;
						
		}
		
		
	}
