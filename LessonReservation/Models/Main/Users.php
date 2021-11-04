<?php
require_once(ROOT_PATH .'/Models/Db.php');

class Users extends Db{
    private $table='users';

    public function __construct($dbh=null){
        parent::__construct($dbh);
    }

    /** 
     * usersテーブルからデータを取得
     * @return $result usersデータ取得
    */


    public function findAll($page = 0):Array{
        $sql = "SELECT u.*,i.name AS 'inst' 
        FROM users u
        JOIN instrument i
        ON u.inst_id=i.id";
        $sql .= ' LIMIT 4 OFFSET '.(4 * $page);
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    /** 
    * usersテーブルから全データ数を取得
    * @param Int  $count 全選手の件数
    */
    public function countAll():Int{
        $sql = "SELECT count(*) as count FROM users";
        $sth = $this->dbh->prepare($sql);
        
        $sth->execute();
        $count = $sth->fetchColumn();
        return $count;
    }


    /** 
     * usersテーブルからデータを取得
     * @return $result usersデータ取得
    */

    public function findUsers():Array{
        $id = $_SESSION['id'];

        $sql = "SELECT u.*,i.name AS 'inst'
        FROM users u
        LEFT JOIN instrument i
        ON u.inst_id = i.id
        WHERE u.id = '".$id."'";

        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }




     /** 
     * usersテーブルからデータを取得
     * @return $result usersデータ取得
     */

    public function findById($id=0):Array{
        $sql = "SELECT $this->table.*,i.name AS 'inst'
        FROM $this->table 
        JOIN instrument i ON $this->table.inst_id = i.id";
        $sql .= " WHERE $this->table.id = :id";
        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }




    /** 
    * @return $result 登録するusersデータ
    */
    public function UpdateUsers($id=0){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            require_once(ROOT_PATH .'/Controllers/Main/UsersController.php');
            $users = new UsersController;
            // ユーザーデータ取得
            $user = $users->views();
            // 画像
            if(empty($_FILES['image']['name'])){
                $image= htmlspecialchars($user['image'], ENT_QUOTES, "UTF-8");
              }elseif(!empty($_FILES['image']['name'])){
                $image= htmlspecialchars($_FILES['image']['name'], ENT_QUOTES, "UTF-8");
                //画像を保存
                move_uploaded_file($_FILES['image']['tmp_name'], './img/user/' . $image);
              }
            

            //氏名
            $name=htmlspecialchars($_POST['name'], ENT_QUOTES, "UTF-8");

            // フリガナ
            $kana = htmlspecialchars($_POST['kana'], ENT_QUOTES, "UTF-8");

            //メールアドレス
            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, "UTF-8");

            //電話番号
            $tel = htmlspecialchars($_POST['tel'], ENT_QUOTES, "UTF-8");

            //郵便番号
            $code = htmlspecialchars($_POST['code'], ENT_QUOTES, "UTF-8");

            // 住所
            $address = htmlspecialchars($_POST['address'], ENT_QUOTES, "UTF-8");


            // 楽器
            $inst = htmlspecialchars($_POST['inst'], ENT_QUOTES, "UTF-8");

            // 備考欄
            $body =  htmlspecialchars($_POST['body'], ENT_QUOTES, "UTF-8");;

            //更新日
            $update_at = date('Y-m-d H:i:s');


            $sql = "UPDATE users 
            SET image = :image, name = :name, kana = :kana , email = :email, tel = :tel, code = :code, address = :address, inst_id = :inst_id, body = :body, update_at = :update_at
            WHERE id = :id" ;

            $sth = $this->dbh->prepare($sql);

            $sth->bindParam( ':image', $image, PDO::PARAM_STR);
            $sth->bindParam( ':name', $name, PDO::PARAM_STR);
            $sth->bindParam( ':kana', $kana, PDO::PARAM_STR);
            $sth->bindParam( ':email', $email, PDO::PARAM_STR);
            $sth->bindParam( ':tel', $tel, PDO::PARAM_STR);
            $sth->bindParam( ':code', $code, PDO::PARAM_STR);
            $sth->bindParam( ':address', $address, PDO::PARAM_STR);
            $sth->bindParam( ':inst_id', $inst, PDO::PARAM_INT);
            $sth->bindParam( ':body', $body, PDO::PARAM_STR);
            $sth->bindParam(':update_at',$update_at,PDO::PARAM_STR);
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            
            $sth->execute();

            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        }
    }
     /** 
     * usersテーブルからデータを取得
     * @return $result 削除
     */
    public function DeleteUsers($id=0){
        $sql = "DELETE FROM $this->table 
        WHERE id = :id";
        $sth = $this->dbh->prepare($sql);
        $this->dbh->beginTransaction();

        try{
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();

            
            $this->dbh->commit();

            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        }catch(PDOException $e){
            echo '削除失敗'.$e->getMessage();
            $this->dbh->rollBack();
            exit();
        }
    }
     

}


?>