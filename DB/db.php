<?php
	class DB{
		//private $host;
		private $tns='(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 135.64.132.37)(PORT = 1521))  ) (CONNECT_DATA = (SID = xzedw)  (SERVER = DEDICATED)   )  )';
		private $username='edp';
		private $password='august';
		private $conn;
		
		//数据库连接
		public function getConn(){
			try{
				$this->conn = new PDO("oci:dbname=".$this->tns,$this->username,$this->password);
				//new PDO("oci:dbname=//135.64.132.37:1521/xzedw,$this->username,$this->password");
			}catch(PDOException $e){
				echo $e->getMessage();	
			}
			return $this->conn;
		}
		//释放数据库连接
		public function releaseConn(){
			if($this->conn){
				$this->conn = null;	
			}
		}	
	}
	/*$db = new DB();
	$conn = $db->getConn();
	if($conn){
		echo "连接成功！";	
	}else{
		echo "连接失败！";	
	}*/
	/*$result = array();
	for($i = 0;$i<111000;$i++){
		$result[] = 'i am the '.$i;	
	}
	echo json_encode($result);*/
?>