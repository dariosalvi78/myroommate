<?php
require_once('../libs/simpletest/autorun.php');
require_once '../MNGR.php';

class MateTestst extends UnitTestCase {

	function setUp() {
        
    }
    function tearDown() {
        R::exec("DELETE FROM mate;", null);
		R::exec("DELETE FROM statuslog;", null);
		R::exec("DELETE FROM expense;", null);
		R::exec("DELETE FROM expense_mate;", null);
		R::exec("DELETE FROM payment;", null);
    }
	
    
	function test_create() {
		addNewMate("TESTMATE", "New mate created");

		$mate = getMate("TESTMATE");

		$this->assertNotNull($mate, "Mate from query is not null");
		$this->assertEqual( "TESTMATE", $mate->name, "Retrieved mate has good name" );

		$statusLogs = getStatusLogs("TESTMATE");

		$this->assertEqual( 1, count($statusLogs) , "status log is just one" );
		$statusLog = current($statusLogs);
		$this->assertEqual( "ACTIVE", $statusLog->status, "Statuslog is active" );
	}
	
	function test_getmates()
	{
		addNewMate("TESTMATE1", "New mate created");
		addNewMate("TESTMATE2", "New mate created");
		addNewMate("TESTMATE3", "New mate created");
		sleep(2);
		suspendMate("TESTMATE3", "Suspend 3");
		
		$mates = getMates("ACTIVE");
		
		$this->assertEqual(2, count($mates), "There are 2 active mates");
		$mates = getMates("SUSPENDED");
		$this->assertEqual(1, count($mates), "There are 1 suspended mate");
		$this->assertEqual("TESTMATE3", current($mates)->name, "The suspended is 3");
		
	}
	 
	function test_susped()
	{
		addNewMate("TESTMATE","new mate");
		sleep(2);
		suspendMate("TESTMATE", "Mate suspended");

		$statusLogs = getStatusLogs("TESTMATE");

		$lastStatusLog = current($statusLogs);

		$this->assertEqual( 'SUSPENDED' , $lastStatusLog->status, "Last status is suspened");
	}
	 

	function test_suspedSuspended()
	{
		addNewMate("TESTMATE","new mate");
		sleep(2);
		suspendMate("TESTMATE", "Mate suspended");
		sleep(2);
		suspendMate("TESTMATE", "Mate suspended");

		$statusLogs = getStatusLogs("TESTMATE");

		$lastStatusLog = current($statusLogs);

		$this->assertEqual( 2, count($statusLogs), "Status logs should be 2");
		$this->assertEqual( 'SUSPENDED' , $lastStatusLog->status, "Last status is suspened");
	}
	
	function  test_closed()
	{
		addNewMate("TESTMATE", "new mate");
		sleep(2);
		closeMate("TESTMATE", "Mate closed");
		
		$statusLogs = getStatusLogs("TESTMATE");
		$lastStatusLog = current($statusLogs);

		$this->assertEqual( 2, count($statusLogs), "Status logs should be 2");
		$this->assertEqual( 'CLOSED' , $lastStatusLog->status, "Last status is closed");
	}

};



?>