<?php
/**
 * Created by PhpStorm.
 * User: thomasbuatois
 * Date: 26/11/2017
 * Time: 15:43
 */


class Users
{
    private $ID;
    private $id_Building;
    private $LastName;
    private $FirstName;
    private $Email;
    private $Phone;
    private $Role;
    private $id_flat;
    private $conn;

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->LastName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->FirstName;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->Role;
    }

    /**
     * @return mixed
     */
    public function getIdFlat()
    {
        return $this->id_flat;
    }



    public function __construct()
    {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }


    public function connect($Email,$Password){


        $stmt = $this->conn->prepare('SELECT * from user WHERE email = ? ');
        $stmt->bind_param('s', $Email);
        $stmt->execute();
        $row = $stmt->get_result();
        $stmt->free_result();
        if ($row->num_rows != 1){
            $error =   "An error as occured, too many users with the same email";
            return False;
        }
        else{

            $data = $row->fetch_assoc();

            if (password_verify($Password, $data['password'])){
                $this->Role = $data['role_user'];
                $this->FirstName = $data['name_user'];
                $this->ID = $data['id_user'];
                return True;

            }
            else
                return False;

        }

    }



    public function create_user($user_param){

        $db = Database::getInstance();
        $conn = $db->getConnection();

        $password = password_hash($user_param['Password'], PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('INSERT INTO user (surname_user,name_user,email,phone,password,role_user) VALUES (?,?,?,?,?,?)');
        $stmt->bind_param("ssssss", $user_param['LastName'],$user_param['FirstName'] , $user_param['Email'], $user_param['Phone'] , $password ,$user_param['Role']);

        $stmt->execute();
        $stmt->close();
    }
    
    public function update_user(){

        $password = password_hash(['password'], PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('UPDATE user SET surname_user = ?, name_user = ?, email = ?, phone = ? WHERE id = ?')     ;
        $stmt->bind_param("sssi", ['LastName'],['FirstName'] , ['Email'], ['Phone']);
        $stmt->execute();
        $stmt->close();

    }

    public function delete_user($userid){
        $stmt = $this->conn->prepare('DELETE FROM user WHERE user.id_user = ?');
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->close();
    }

    public function setCurrentUser($userid){

        $stmt = $this->conn->prepare('SELECT * from user WHERE id_user = ?');
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $this->FirstName = $data['name_user'];
        $this->LastName = $data['surname_user'];
        $this->Role = $data['role_user'];
        $this->Email = $data['email'];
        $this->Phone = $data['phone'];
        $this->id_flat = $data['id_flat'];
        $this->ID = $userid;
    }

    public function getIDBM(){

        $stmt = $this->conn->prepare('SELECT * FROM flat INNER JOIN building ON building.id_building = flat.id_building WHERE id_flat = ?');
        $stmt->bind_param("i", $this->id_flat);
        $stmt->execute();
        $row = $stmt->get_result();
        $data = $row->fetch_assoc();
        return $data['id_user'];

    }
    

    public function getUsersList($name){

        switch ($this->Role) {
            case 'admin':
                $stmt = $this->conn->prepare('SELECT * FROM user WHERE CONCAT(user.name_user, " " , user.surname_user) LIKE CONCAT("%",?,"%") LIMIT 30');
                $stmt->bind_param("s", $name);
                break;
            case 'FM':
                $stmt = $this->conn->prepare('SELECT * FROM user WHERE user.id_flat = ? AND CONCAT(user.name_user, " " , user.surname_user) LIKE CONCAT("%",?,"%") LIMIT 30');
                $stmt->bind_param("si", $name, $this->id_flat);
                break;
            case 'BM':
                $stmt = $this->conn->prepare('SELECT * FROM user
                                          INNER JOIN flat ON user.id_flat = flat.id_flat
                                          INNER JOIN building ON flat.id_building = building.id_building
                                        WHERE building.id_user = ? AND  CONCAT(user.name_user, " " , user.surname_user) LIKE CONCAT("%",?,"%")  LIMIT 20 ');
                $stmt->bind_param("is", $this->ID, $name);
                break;
        }
        
        $stmt->execute();
        $res= $stmt->get_result();
        $rows = array();
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

}

?>