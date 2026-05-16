<?php
class user
{
	function getUser(){
		global $conn;
		$query = "select * from  user ORDER BY id";
		$result = mysqli_query($conn, $query);
		return $result;
	}
			
 function addUser($objusers){
	 global $conn;
		$query ="INSERT INTO  user set ";
		foreach ($objusers as $key=>$value){
			$query.= "$key='$value'";
			$query.= ",";
		}  
	
		$query= substr($query, 0, -1);
		//echo $query;
		$result=mysqli_query($conn, $query);
		$last_id = mysqli_insert_id($conn);
		return $last_id;
	}
	
	
	function editUser($objusers,$id){
		global $conn;
		$query =" UPDATE user set ";
		foreach ($objusers as $key=>$value){
			$query .= "$key='$value'";
				$query.= ",";
		}
		
		$query= substr($query, 0, -1);
		$query .= " WHERE  id='$id'";		
		//echo $query;
		$result=mysqli_query($conn, $query);
		return $result;
	}
	
	function getUserinfo($id)
	   {
  	    global $conn;

		$query = "SELECT * FROM user WHERE id ='$id'";
        $result = mysqli_query($conn, $query);
		return $result;
	   }	
	function deleteUser($id)
	{
  	     global $conn;

	    $query  = "DELETE FROM user WHERE id ='$id' ";
		$result=mysqli_query($conn, $query);
		return $result;
	}	
	
	
}

?>