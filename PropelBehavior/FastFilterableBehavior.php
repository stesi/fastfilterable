<?php

namespace Stesi\PropelBehavior;

use Propel\Generator\Model\Behavior;

class FastFilterableBehavior extends Behavior {
	public function queryMethods($builder) {
		
		return $this->renderTemplate('objectUpdateFastFilterable');
	}
}