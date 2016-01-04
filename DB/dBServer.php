<?php
	require_once(__DIR__.'/db.php');
	class DBServer{
		private $conn;
		public function __construct(){
			$db = new DB();
			$this->conn = $db->getConn();
			if($this->conn){
				//echo "数据库连接成功！";	
			}else{
				echo "数据库连接失败！";	
			}
		}	
		//获取领导视窗数据
		public function squery(){
			$sql = 'select month_no,prd_inst_id,src_serv_nbr,SRC_REGION_ID,DIM_AREA_ID,DIM_CUST_GROUP
 from edp.tb_dm_leader_win_serv_201511 where rownum<1000';
			//echo "select";
			$g_pdo = $this->conn;
			$resultSet = $g_pdo->query($sql);
			$result = array();
			while($row = $resultSet->fetch(PDO::FETCH_ASSOC)){
				//var_dump($row);
				$item = array();
				$item['日起'] = $row['MONTH_NO'];
				$item['用户实例ID'] = $row['PRD_INST_ID'];
				$item['用户号码'] = $row['SRC_SERV_NBR'];
				$item['社区区ID'] = $row['DIM_AREA_ID'];
				$item['营业区ID'] = $row['SRC_REGION_ID'];
				$item['客户群'] = $row['DIM_CUST_GROUP'];				
				$result[] = $item;
			}
			return json_encode($result);
		}
	}
	
?>