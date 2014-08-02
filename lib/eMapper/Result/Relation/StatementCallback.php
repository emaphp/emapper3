<?php
namespace eMapper\Result\Relation;

use eMapper\Annotations\AnnotationsBag;

/**
 * The StatementCallback class implements the logic for evaluating named queries againts
 * a list of arguments.
 * @author emaphp
 */
class StatementCallback extends DynamicAttribute {
	/**
	 * Statement ID
	 * @var string
	 */
	protected $statementId;
	
	public function getStatementId() {
		return $this->statementId;
	}
	
	protected function parseMetadata(AnnotationsBag $attribute) {
		//obtain statement id
		$this->statementId = $attribute->get('StatementId')->getValue();
	}
	
	public function evaluate($row, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $mapper->getConfig()) === false) {
			return null;
		}
		
		//build argument list
		$args = $this->evaluateArgs($row);
		array_unshift($args, $this->statementId);
		
		//update configuration
		$this->updateConfig($mapper->getConfig());
		
		//invoke statement
		return call_user_func_array([$mapper->merge($this->config), 'execute'], $args);
	}
}
?>