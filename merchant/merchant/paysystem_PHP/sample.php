<?php

	/* --------------------------------------------
	    KKBSign class
		-------------
		by Kirsanov Anton (webcompass@list.ru)
		01.06.2006	
	   ------------------------------------------*/

	// -----------------------------------------------------------------------------------------------------------------
	// ���������

	define("MERCHANT_CERT_ID",	"12345678");	// �������� ����� �����������
	define("MERCHANT_NAME",		"Test shop");	// �������� �������� (��������)
	define("ORDER_ID",			"000001");		// ���������� ����� ������
	define("CURRENCY",			"840");			// ID ������. 840 - USD
	define("MERCHANT_ID",		"1");			// ID �������� � �������
	define("AMOUNT", 			"1");			// ����� ������


	// -----------------------------------------------------------------------------------------------------------------
	// Include ������

	require_once("kkbsign.class.php");


	// -----------------------------------------------------------------------------------------------------------------
	// �������� �������

	$kkb = new KKBSign();

	// -----------------------------------------------------------------------------------------------------------------
	// �������������� ����
	
	$kkb->invert();

	// -----------------------------------------------------------------------------------------------------------------
	// ��������� ��������� ����.

	$kkb->load_private_key("test.pem", "test");


	// -----------------------------------------------------------------------------------------------------------------
	// ������ ������

	$merchant = '<merchant cert_id="%certificate%" name="%merchant_name%"><order order_id="%order_id%" amount="%amount%" currency="%currency%"><department merchant_id="%merchant_id%" amount="%amount%"/></order></merchant>';

	// -----------------------------------------------------------------------------------------------------------------
	// ���������� ������ ������, ��������� �������

	$merchant = preg_replace('/\%certificate\%/', 		MERCHANT_CERT_ID , 	$merchant);
	$merchant = preg_replace('/\%merchant_name\%/', 	MERCHANT_NAME , 	$merchant);
	$merchant = preg_replace('/\%order_id\%/', 			ORDER_ID, 			$merchant);
	$merchant = preg_replace('/\%currency\%/', 			CURRENCY, 			$merchant);
	$merchant = preg_replace('/\%merchant_id\%/', 		MERCHANT_ID, 		$merchant);
	$merchant = preg_replace('/\%amount\%/', 			AMOUNT,				$merchant);

	// -----------------------------------------------------------------------------------------------------------------
	// �������������� �������

	$merchant_sign = '<merchant_sign type="RSA">'.$kkb->sign64($merchant).'</merchant_sign>';


	// -----------------------------------------------------------------------------------------------------------------
	// ���������� �����

	$xml = "<document>".$merchant.$merchant_sign."</document>";


	// -----------------------------------------------------------------------------------------------------------------
	// ������� �����

	echo $xml;

	// -----------------------------------------------------------------------------------------------------------------
	// echo base64_encode($xml); // ������� ����� ��� ���������� Signed_Order_B64

?>