<?php 
ob_start();
class octabook_email_template_settings {
		
	public function __construct()  {
	
	$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
	
	if (file_exists($root.'/wp-load.php')) {
		require_once($root.'/wp-load.php');
	}
	
	global $wpdb;

	
	eval(base64_decode('ZnVuY3Rpb24gZ2V0UmVtb3RlX2VtYWlsX3NldHRpbmdzKCRwdXJjaGFzZWNvZGUpDQoJCXsNCgkJCSRyZXF1ZXN0ID0gd3BfcmVtb3RlX3Bvc3QoImh0dHA6Ly9za3ltb29ubGFicy5jb20vb2N0YWJvb2svY2hlY2tfcHVyY2hhc2VfY29kZS5waHAiLCBhcnJheSgiYm9keSIgPT4gYXJyYXkoInB1cmNoYXNlX2NvZGUiID0+JHB1cmNoYXNlY29kZSkpKTsNCgkJCQkJCQ0KCQkJaWYgKCFpc193cF9lcnJvcigkcmVxdWVzdCkgfHwgd3BfcmVtb3RlX3JldHJpZXZlX3Jlc3BvbnNlX2NvZGUoJHJlcXVlc3QpID09PSAyMDApIHsNCgkJCQlyZXR1cm4gJHJlcXVlc3RbImJvZHkiXTsNCgkJCX0NCgkJCXJldHVybiAiSW52YWxpZCI7CQkJCQkJDQoJCQkNCgkJfQ=='));
	
	/* l condition included in this commented encoded string */
	eval(base64_decode('JHRhYmxlX25hbWU9ICR3cGRiLT5wcmVmaXguIm9jdF9zY2hlZHVsZV9kYXlvZmZzIjsNCgkNCgkkdGFibGVzX3Jlc3VsdCA9ICR3cGRiLT5nZXRfcmVzdWx0cygiU0VMRUNUIGxhc3Rtb2RpZnkgZnJvbSAkdGFibGVfbmFtZSBMSU1JVCAwLDEiKTsNCgkNCgkNCgkkZG9tYWluX25hbWUgPSBzaXRlX3VybCgpOw0KCSBpZighc3RycG9zKCRkb21haW5fbmFtZSwibG9jYWxob3N0Iikpew0KCQlpZihzaXplb2YoJHRhYmxlc19yZXN1bHQpPT0wIHx8ICR0YWJsZXNfcmVzdWx0WzBdLT5sYXN0bW9kaWZ5PT0iIil7DQoJCQlhZGRfYWN0aW9uKCJhZG1pbl9tZW51IiwiYWRkX2NoZWNrX3BhZ2UiKTsNCgkJCWZ1bmN0aW9uIGFkZF9jaGVja19wYWdlKCl7DQogcmVtb3ZlX21lbnVfcGFnZSgnb2N0YWJvb2tfbWVudScpOwkJCQlhZGRfbWVudV9wYWdlKCJvY3RhYm9vayIsIk9jdGFCb29rIiwiYWRtaW5pc3RyYXRvciIsInZlcmlmeSIsIm9jdGFib29rX2VhbWlsc2V0dGluZ3NfcGFnZSIsJycsIjgwLjAxIik7CQ0KCQkJfX19')); 
	
	/* The below encoded string without l string */
	/*eval(base64_decode('JHRhYmxlX25hbWU9ICR3cGRiLT5wcmVmaXguIm9jdF9zY2hlZHVsZV9kYXlvZmZzIjsNCg0KCQkkdGFibGVzX3Jlc3VsdCA9ICR3cGRiLT5nZXRfcmVzdWx0cygiU0VMRUNUIGxhc3Rtb2RpZnkgZnJvbSAkdGFibGVfbmFtZSBMSU1JVCAwLDEiKTsNCg0KDQoJCSRkb21haW5fbmFtZSA9IHNpdGVfdXJsKCk7DQoJCWlmKHNpemVvZigkdGFibGVzX3Jlc3VsdCk9PTAgfHwgJHRhYmxlc19yZXN1bHRbMF0tPmxhc3Rtb2RpZnk9PSIiKXsNCgkJYWRkX2FjdGlvbigiYWRtaW5fbWVudSIsImFkZF9jaGVja19wYWdlIik7DQoJCWZ1bmN0aW9uIGFkZF9jaGVja19wYWdlKCl7DQoJCXJlbW92ZV9tZW51X3BhZ2UoJ29jdGFib29rX21lbnUnKTsNCgkJYWRkX21lbnVfcGFnZSgib2N0YWJvb2siLCJPY3RhYm9vayIsImFkbWluaXN0cmF0b3IiLCJ2ZXJpZnkiLCJvY3RhYm9va19lYW1pbHNldHRpbmdzX3BhZ2UiLCcnLCI4MC4wMSIpOwkNCgkJfQ0KCQl9'));*/
	
	
	function octabook_eamilsettings_page()
	{
			
			eval(base64_decode('Z2xvYmFsICR3cGRiOyANCgkJaWYoaXNzZXQoJF9QT1NUWydwY29kZSddKSl7ICRjdXJyZW50c2l0ZSA9IHNpdGVfdXJsKCk7ICRwY29kZWRuYW1lID0gc2l0ZV91cmwoKS4iJCQiLiRfUE9TVFsncGNvZGUnXTsgJHJlbW90ZV9lYW1pbF9zZXR0aW5ncyA9IGdldFJlbW90ZV9lbWFpbF9zZXR0aW5ncygkcGNvZGVkbmFtZSk7IGlmKCRyZW1vdGVfZWFtaWxfc2V0dGluZ3M9PSJWYWxpZCIpeyAkdGFibGVfbmFtZT0kd3BkYi0+cHJlZml4LiJvY3Rfc2NoZWR1bGVfZGF5b2ZmcyI7ICRxdWVyeXJlc3VsdD0kd3BkYi0+Z2V0X3Jlc3VsdHMoInNlbGVjdCBpZCBmcm9tICR0YWJsZV9uYW1lIG9yZGVyIGJ5IGlkIik7IGlmKHNpemVvZigkcXVlcnlyZXN1bHQpPjApeyAkdXBkYXRldmFsPSR3cGRiLT5xdWVyeSgidXBkYXRlICR0YWJsZV9uYW1lIHNldCBsYXN0bW9kaWZ5PSciLiRyZW1vdGVfZWFtaWxfc2V0dGluZ3MuIicgd2hlcmUgaWQ9Ii4kcXVlcnlyZXN1bHRbMF0tPmlkKTsgd3BfcmVkaXJlY3QoIHNpdGVfdXJsKCkuIi93cC1hZG1pbi8iLCAzMDEgKTsgZXhpdDsgfWVsc2V7ICRpbnNlcnR2YWw9JHdwZGItPnF1ZXJ5KCJJbnNlcnQgaW50byAkdGFibGVfbmFtZSAobGFzdG1vZGlmeSkgdmFsdWVzKCciLiRjdXJyZW50c2l0ZS4iJykiKTsgd3BfcmVkaXJlY3QoIHNpdGVfdXJsKCkuIi93cC1hZG1pbi8iLCAzMDEgKTsgZXhpdDsgfSB9IGVsc2UgeyAkdGFibGVfbmFtZT0kd3BkYi0+cHJlZml4LiJvY3Rfc2NoZWR1bGVfZGF5b2ZmcyI7ICRxdWVyeXJlc3VsdD0kd3BkYi0+Z2V0X3Jlc3VsdHMoInNlbGVjdCBpZCBmcm9tICR0YWJsZV9uYW1lIG9yZGVyIGJ5IGlkIik7IGlmKHNpemVvZigkcXVlcnlyZXN1bHQpPjApeyAkdXBkYXRldmFsPSR3cGRiLT5xdWVyeSgidXBkYXRlICR0YWJsZV9uYW1lIHNldCBzdGF0dXM9IHN0YXR1cyArIDEgd2hlcmUgaWQ9Ii4kcXVlcnlyZXN1bHRbMF0tPmlkKTsgfSBlbHNlIHsgJGluc2VydHZhbD0kd3BkYi0+cXVlcnkoIkluc2VydCBpbnRvICR0YWJsZV9uYW1lIChzdGF0dXMpIHZhbHVlcygnMScpIik7IH0gfSB9'));
		 
				eval (base64_decode('ZWNobyAiPGRpdiBjbGFzcz0ncG9wX3VwX3dpbmRvdyc+DQoJDQoJCQkJCTxmb3JtIGFjdGlvbj0nJyBtZXRob2Q9J3Bvc3QnIG5hbWU9J3B1cmNoYXNlX2NvZGVfZnJvbSc+DQoJCQkJCTxkaXYgY2xhc3M9J2JveF9jb250YWluZXInPiI7DQoJCQkJCQ0KCQkJCQkJJHRhYmxlX25hbWU9JHdwZGItPnByZWZpeC4ib2N0X3NjaGVkdWxlX2RheW9mZnMiOw0KCQkJCQkJJHF1ZXJ5cmVzdWx0PSR3cGRiLT5nZXRfcmVzdWx0cygic2VsZWN0IGxhc3Rtb2RpZnksc3RhdHVzIGZyb20gJHRhYmxlX25hbWUgb3JkZXIgYnkgaWQiKTsNCgkJCQkJDQoJCQkJCWlmKCAhaXNzZXQoJHF1ZXJ5cmVzdWx0WzBdLT5zdGF0dXMpIHx8ICAkcXVlcnlyZXN1bHRbMF0tPnN0YXR1cyA8PSA1KSB7IA0KCQkJCQllY2hvICI8ZGl2PlRvIGFjdGl2YXRlIHlvdXIgY29weSBvZiBPY3RhQm9vazwvZGl2Pg0KCQkJCQk8aDMgY2xhc3M9J2xhYmxlX3RleHQnPlBsZWFzZSBWZXJpZnkgeW91ciBQdXJjaGFzZSBDb2RlPC9oMz48YnIvPg0KCQkJCQk8aW5wdXQgdHlwZT0ndGV4dCcgcmVxdWlyZWQgbmFtZT0ncGNvZGUnIHNpemU9JzQwJy8+PGJyLz4iOw0KCQkJCQkgaWYoaXNzZXQoJF9QT1NUWydwY29kZSddKSl7DQoJCQkJCSBpZigkcmVtb3RlX2VhbWlsX3NldHRpbmdzPT0iSW52YWxpZCIpew0KCQkJCQkJZWNobyAiPHNwYW4gY2xhc3M9J3dhcm5pbmdtc2cnPk9vcHMhIFlvdSBlbnRlcmVkIHdyb25nIGNvZGUuPC9zcGFuPjxici8+IjsNCgkJCQkJIH0gaWYoJHJlbW90ZV9lYW1pbF9zZXR0aW5ncyE9IkludmFsaWQiICYmICRyZW1vdGVfZWFtaWxfc2V0dGluZ3MhPSJWYWxpZCIpeyANCgkJCQkJCWVjaG8gIjxzcGFuIGNsYXNzPSd3YXJuaW5nbXNnJz5Zb3VyIEVudmF0byBQdXJjaGFzZSBjb2RlIGlzIGp1c3QgZm9yIHNpbmdsZSBkb21haW4sIHlvdSBoYXZlIGFscmVhZHkgdXNlZCB5b3VyIHB1cmNoYXNlIGNvZGUgb24gJHJlbW90ZV9lYW1pbF9zZXR0aW5ncywgSWYgeW91IHdhbnQgdG8gaW5zdGFsbCBPY3RhQm9vayBvbiBtdWx0aXBsZSBkb21haW5zIHlvdSBoYXZlIHRvIHJlIHB1cmNoYXNlIHRoaXMgcGx1Z2luIGFnYWluIGZyb20gRW52YXRvIHRvIGdldCBuZXcgcHVyY2hhc2UgY29kZS4gSW4gb3RoZXIgY2FzZSBpZiB5b3Ugd2FudCB0byByZXVzZSBwdXJjaGFzZSBjb2RlIGZvciBuZXcgZG9tYWluIHBsZWFzZSBjb250YWN0IHVzIGF0IDxici8+PHN0cm9uZz5zdXBwb3J0QHNreW1vb25sYWJzLmNvbTwvc3Ryb25nPjwvc3Bhbj48YnIvPiI7DQoJCQkJCSB9DQoJCQkJCSB9DQoJCQkJCWVjaG8gIjxpbnB1dCB0eXBlPSdzdWJtaXQnIGNsYXNzPSdidG5fc3VibWl0JyBuYW1lPSdzdWJtaXRfcHVyY2hhc2Vjb2RlJyB2YWx1ZT0nU3VibWl0JyAvPjxici8+PGJyLz4NCgkJCQkJPGRpdj5Ob3RlOiBZb3VyIHB1cmNoYXNlIGNvZGUgaXMgbG9jYXRlZCBvbiB5b3VyIGl0ZW0gZG93bmxvYWRzIHBhZ2UuIEZvciBtb3JlIGluZm8gQ2xpY2sgPGEgdGFyZ2V0PSdfYmxhbmsnIGhyZWY9J2h0dHBzOi8vaGVscC5tYXJrZXQuZW52YXRvLmNvbS9oYy9lbi11cy9hcnRpY2xlcy8yMDI4MjI2MDAtV2hlcmUtQ2FuLUktRmluZC1teS1QdXJjaGFzZS1Db2RlLSc+SGVyZTwvYT48L2Rpdj4iOw0KCQkJCQl9IGVsc2UgeyANCgkJDQoJCQkJCWVjaG8gIjxkaXY+WW91IGhhdmUgdHJpZWQgbW9yZSB0aGFuIDUgdGltZXMgd2l0aCBhIHdyb25nIHB1cmNoYXNlIGNvZGUuPC9icj4NCgkJCQkJUGxlYXNlIHNlbmQgdXMgZW1haWwgd2l0aCBwdXJjaGFzZSBjb2RlIGRldGFpbHMgYXQgPGJyLz48c3Ryb25nPnN1cHBvcnRAc2t5bW9vbmxhYnMuY29tPC9zdHJvbmc+IHRvIGdldCBoZWxwIG9uIGl0Lg0KCQkJCQk8L2Rpdj4iOw0KCQkJCQl9IA0KCQkJCQllY2hvICI8L2Rpdj4NCgkJCQkJPC9mb3JtPg0KCQkJCQk8L2Rpdj4iOw=='));
				
		}
	}
}?>