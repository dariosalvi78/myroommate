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

	
	function test_add_payment()
	{
		addNewMate("TESTMATE1","new mate");
		addNewMate("TESTMATE2","new mate");
		addNewMate("TESTMATE3","new mate");
		
		addPayment("TESTMATE1", "TESTMATE2", 100, "test pay");
		
		$payments = getPayments("TESTMATE1");
		$payment = current($payments);
		
		$this->assertEqual(1, count($payments), "Only one payment");
		$this->assertEqual("TESTMATE1",$payment->fromWho , "FromWho check");
		$this->assertEqual("TESTMATE2",$payment->toWho , "ToWho check");
		$this->assertEqual(100 ,$payment->amount , "Amount check");
		$this->assertEqual("test pay",$payment->comment , "Comment check");
		
	}
	
	
	function test_ownsExpended()
	{
		addNewMate("TESTMATE1","new mate");
		addNewMate("TESTMATE2","new mate");
		addNewMate("TESTMATE3","new mate");
		
		addExpense("TESTMATE1" ,100, "1", "1", array("TESTMATE2")); 
		addExpense("TESTMATE2" ,100, "1", "1", array("TESTMATE1")); 
		addExpense("TESTMATE1" ,30, "1", "1", array("TESTMATE2","TESTMATE3"));
		addExpense("TESTMATE2" ,30, "1", "1", array("TESTMATE1","TESTMATE3"));
		addExpense("TESTMATE3" ,30, "1", "1", array("TESTMATE1","TESTMATE2"));
		
		//situation:
		$this->assertEqual(60, getOwnsExpended("TESTMATE1", "TESTMATE2"), "1 owns 60 to 2");
		$this->assertEqual(10, getOwnsExpended("TESTMATE1", "TESTMATE3"), "1 owns 10 to 3");
		$this->assertEqual(60, getOwnsExpended("TESTMATE2", "TESTMATE1"), "2 owns 60 to 1");
		$this->assertEqual(10, getOwnsExpended("TESTMATE2", "TESTMATE3"), "2 owns 10 to 3");
		$this->assertEqual(10, getOwnsExpended("TESTMATE3", "TESTMATE1"), "3 owns 10 to 1");
		$this->assertEqual(10, getOwnsExpended("TESTMATE3", "TESTMATE2"), "3 owns 10 to 2");
	}
	
	function test_null_debts()
	{
		addNewMate("Dario","new mate");
		addNewMate("Marta","new mate");
		
		addExpense("Dario" ,100, "1", "1", array("Marta"));
		addExpense("Marta" ,100, "1", "1", array("Dario")); 
		
		//Dario shares 100 with Marta
		//Marta shares 100 with Dario
		$this->assertEqual(0, getDebt("Dario", "Marta"), "Dario owns nothing to Marta");
		$this->assertEqual(0, getDebt("Marta", "Dario"), "Marta owns nothing Dario");
	}
	
	function test_overpaid()
	{
		addNewMate("Dario","new mate");
		addNewMate("Marta","new mate");
		
		//Dario shares 100 with Marta
		//Marta shares 40 with Dario
		
		addExpense("Dario" ,100, "1", "1", array("Marta"));
		addExpense("Marta" ,40, "1", "1", array("Dario")); 
		//Marta owns 30 to Dario
		
		//Marta gives 50 to Dario
		addPayment("Marta", "Dario", 50, "wrong payment");
		
		
		$this->assertEqual(20, getDebt("Dario", "Marta"), "Dario owns 20 to Marta");
		$this->assertEqual(-20, getDebt("Marta", "Dario"), "Marta owns -20 to Dario");
	}
	
	function test_complex_case()
	{
		addNewMate("Dario","new mate");
		addNewMate("Marta","new mate");
		addNewMate("Ciro","new mate");
		
		addExpense("Dario" ,180, "1", "1", array("Marta"));
		addExpense("Dario" ,120, "1", "1", array("Marta", "Ciro"));
		addExpense("Marta" ,30, "1", "1", array("Dario", "Ciro"));
		addExpense("Ciro" ,60, "1", "1", array("Marta"));
		addExpense("Ciro" ,60, "1", "1", array("Marta", "Dario"));
		
		addPayment("Marta", "Dario", 100, "100");
		
		//Dario owns to marta 10 + 100 -90 - 40 = -20
		$this->assertEqual(-20, getDebt("Dario", "Marta"), "Dario owns -20 to Marta");
		//Dario owns to Ciro 20 - 40 = -20
		$this->assertEqual(-20, getDebt("Dario", "Ciro"), "Dario owns -20 to Ciro");
		//Ciro owns to Marta 10 -30 - 20 = -40
		$this->assertEqual(-40, getDebt("Ciro", "Marta"), "Dario owns -40 to Ciro");
	}
}
?>