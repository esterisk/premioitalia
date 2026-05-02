<?php
namespace App\Services;

class AjaxParcel {

	protected $parcel;

	function __construct() {
		$this->parcel = array(
			'_status' => 'error',
			'_message' => 'Unknown error'
		);
	}
	
	public function set($label, $var) {
		$this->parcel[$label] = $var;
	}
	
	public function get($label) {
		return $this->parcel[$label];
	}

	public function add($param1, $param2 = null) {
		if ($param2 !== null) {
			$this->set($param1,$param2);

		} elseif (is_array($param1) || is_object($param1)) {
			if (is_object($param1)) $param1 = (array) $param1;
			unset($param1['_status']);
			unset($param1['_message']);
			$this->parcel = array_merge($this->parcel, $param1);

		}
	}
	
	public function message($message) {
		$this->set('_message', $message);
	}
	
	public function status($status) {
		if (!in_array($status, array('ok','error'))) $this->fail('Status errato');
		$this->set('_status', $status);
	}
	
	public function html($html) {
		$this->set('html', $html);
	}
	
	public function addUser() {
		$this->set('user', Auth::User()->getKey());
	}

	public function fail($message = null) {
		if ($message !== null) $this->set('_message',$message);
		$this->set('_status','error');
		return $this->out();
	}

	public function success($message = '') {
		$this->set('_message',$message);
		$this->set('_status','ok');
		return $this->out();
	}
	
	public function ok($message = '') {
		$this->success($message);
	}

	private function out() {
		//return $this->parcel;
		return \Response::json($this->parcel);
	}
	
}
