<?php

class ExpensesModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    function insert($title, $amount, $category, $date, $id_user){
        try{
            $query = $this->db->connect()->prepare('INSERT INTO expenses (title, amount, category_id, date, id_user) VALUES(:title, :amount, :category, :d, :user)');
            $query->execute(['title' => $title, 'amount' => $amount, 'category' => $category, 'user' => $id_user, 'd' => $date]);
            if($query->rowCount()) return true;
            return false;
        }catch(PDOException $e){
            return false;
        }
    }

    function delete($id_expense, $id_user){
        try{
            $query = $this->db->connect()->prepare('DELETE FROM expenses WHERE id = :id and id_user = :user');
            $query->execute(['id' => $id_expense,'user' => $id_user]);
            if($query->rowCount()) return true;
            return false;
        }catch(PDOException $e){
            //echo $e->getMessage();
            //echo "Ya existe esa matrícula";
            return false;
        }
    }

    function modify($id_expense, $title, $amount, $category, $id_user){
        // TODO: completar funcion
    }

    function get($user, $n){
        $res = [];
        $items = [];
        try{
            if($n === 0){
                $query = $this->db->connect()->prepare('SELECT categories.id as "category.id", categories.color as "category.color", expenses.id as "expense.id", categories.name, expenses.title, expenses.amount, expenses.date FROM expenses INNER JOIN categories WHERE categories.id = expenses.category_id AND id_user = :user ORDER BY expenses.date DESC ');
                $query->execute(['user' => $user]);
            }else{
                $query = $this->db->connect()->prepare('SELECT categories.id as "category.id", categories.color as "category.color", expenses.id as "expense.id", categories.name, expenses.title, expenses.amount, expenses.date FROM expenses INNER JOIN categories WHERE categories.id = expenses.category_id AND id_user = :user ORDER BY expenses.date DESC LIMIT 0, :n ');
                $query->execute(['user' => $user, 'n' => $n]);
            }
        }catch(PDOException $e){
            return NULL;
        }

        if($query->rowCount() == 0) return array();
        
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $item = Array(
                'expense_id' => $row['expense.id'],
                'category_name' => $row['name'],
                'category_id' => $row['category.id'],
                'expense_title' => $row['title'],
                'amount' =>  $row['amount'],
                'date' => $row['date'],
                'category_color' => $row['category.color'],
            );
            array_push($items, $item);
        }
        
        return $items;  
    }

    function getTotal($user){
        try{
            $year = date('Y');
            $month = date('m');
            $query = $this->db->connect()->prepare('SELECT SUM(amount) AS total FROM expenses WHERE YEAR(date) = :year AND MONTH(date) = :month AND id_user = :user');
            $query->execute(['year' => $year, 'month' => $month, 'user' => $user]);

            $total = $query->fetch(PDO::FETCH_ASSOC)['total'];
            if($total == NULL) $total = 0;
            
            return $total;

        }catch(PDOException $e){
            return NULL;
        }
    }

    function getTotalByCategory($cid, $user){
        try{
            $total = 0;
            $year = date('Y');
            $month = date('m');
            $query = $this->db->connect()->prepare('SELECT SUM(amount) AS total from expenses WHERE category_id = :val AND id_user = :user AND YEAR(date) = :year AND MONTH(date) = :month');
            $query->execute(['val' => $cid, 'user' => $user, 'year' => $year, 'month' => $month]);

            if($query->rowCount() > 0){
                $total = $query->fetch(PDO::FETCH_ASSOC)['total'];
            }else{
                return 0;
            }
            
            return $total;

        }catch(PDOException $e){
            return NULL;
        }
    }

    function getTotalByMonthAndCategory($date, $category, $userid){
        try{
            $total = 0;
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 7);
            $query = $this->db->connect()->prepare('SELECT SUM(amount) AS total from expenses WHERE category_id = :val AND id_user = :user AND YEAR(date) = :year AND MONTH(date) = :month');
            $query->execute(['val' => $category, 'user' => $userid, 'year' => $year, 'month' => $month]);

            if($query->rowCount() > 0){
                $total = $query->fetch(PDO::FETCH_ASSOC)['total'];
            }else{
                return 0;
            }
            
            return $total;

        }catch(PDOException $e){
            return NULL;
        }
    }

    function getField($user, $name ){
    }
}

?>