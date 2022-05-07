<?php namespace Source\Connection;

/**
 * Conexao com o banco de dados
 */
trait DBConnect{

    public static function PDOSetConnection(){
            
        $connection = new \PDO(PDO_CONFIG, DB_CONFIG['username'], DB_CONFIG['password']);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //retornará uma exception caso algo de errado
        
        return new \ClanCats\Hydrahon\Builder("mysql", function ($query, $queryString, $queryParameters) use ($connection){

            $statement = $connection->prepare($queryString);
            $statement->execute($queryParameters);

            if ($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface)
            {
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
  
            elseif($query instanceof \ClanCats\Hydrahon\Query\Sql\Insert)
            {
                return $connection->lastInsertId();
            }

            else 
            {
                return $statement->rowCount();
            }   

        });

    }

    public static function table( string $table )
    {
        return DBConnect::PDOSetConnection()->table($table);
    }
}