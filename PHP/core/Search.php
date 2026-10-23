<?php
Class Search {
	private $id;
	private $state;

	public function __construct($searchId=false){
		require_once('./core/ext/challengeclass.php');
		if ($searchId) $this->id=$searchId;
		else {
			$this->id=ChallengeGenerator::staticGetRandomString(10);
			$this->state='started';
			$_SESSION['searchId']=$this->id;
		}

  }
	public function isState($state){
		if ($state==$this->state) return true;
		else return false;
	}
	public function setState($state){
		$this->state=$state;
	}
	public function getId(){
		return $this->id;
	}
}
?>
