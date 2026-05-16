<?php
class users
{
    function getUsers(){
        global $conn;
        $query = "select * from  admin ORDER BY id DESC";
        $result = mysqli_query($conn, $query);
        return $result;
    }
    
    function getUsersById($user_id){
        global $conn;
        $query = "select * from  admin where id='$user_id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }
    
    function getMemberlist(){
        global $conn;
        $query = "select * from  admin WHERE usertype='6' ORDER BY id DESC";
        $result = mysqli_query($conn, $query);
        return $result;
    }
    
    function userLogin($phone,$password){
        global $conn;
        $query = "select * from  admin  WHERE phone='$phone' AND password='$password' AND status='1'";
        $result = mysqli_query($conn, $query);
        return $result;
    }
    
    function addUsers($objusers){
        global $conn;
        $query ="INSERT INTO  admin set ";
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
    
    function deleteUsers($id){
        global $conn;
        $query  = "DELETE FROM admin WHERE id ='$id' ";
        $result=mysqli_query($conn, $query);
        return $result;
    }
    
    function editUsers($objusers,$id){
        global $conn;
        $query =" UPDATE admin set ";
        foreach ($objusers as $key=>$value){
            $query .= "$key='$value'";
            $query.= ",";
        }
        $query= substr($query, 0, -1);
        $query .= " WHERE id='$id'";		
        //echo $query;
        $result=mysqli_query($conn, $query);
        return $result;
    }
    
    function editMember($objmember,$member_id){
        global $conn;
        $query =" UPDATE admin set ";
        foreach ($objmember as $key=>$value){
            $query .= "$key='$value'";
            $query.= ",";
        }
        $query= substr($query, 0, -1);
        $query .= " WHERE id='$member_id'";		
        //echo $query;
        $result=mysqli_query($conn, $query);
        return $result;
    }
    
    function getuserinfo($user_id){
        global $conn;
        $query = "SELECT * FROM admin WHERE id ='$user_id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }	
    
    function checkUsers($username){
        global $conn;
        $query = "SELECT * FROM admin WHERE username ='$username' OR phone='$username'";
        $result = mysqli_query($conn, $query);
        return $result;
    }	
}

?>