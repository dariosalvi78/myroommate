<?php
require_once('../libs/simpletest/autorun.php');

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('All tests');
        $this->addFile('ExpenseTests.php');
        $this->addFile('MateTests.php');
        $this->addFile('PaymentTests.php');
    }
}

?>