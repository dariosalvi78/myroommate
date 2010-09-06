<?php

require_once 'rb.php';

class RBObject extends  RedBean_OODBBean
{
	
	public function __construct($objectname)
	{
		parent::setMeta("type", $objectname );
        parent::setMeta("sys.oodb", $this);
        $idfield = R::$writer->getIDField(parent::getMeta("type"));
		$this->$idfield = 0;
		R::$redbean->signal( "dispense", $this );
		R::$redbean->check( $this );
		parent::setMeta("tainted",false);
	}
}

?>