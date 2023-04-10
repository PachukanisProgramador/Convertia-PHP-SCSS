<?php
    namespace PHP\DAO;//
    use Exception; //Evitar do código não usar o Exception.
    
    class Conexao{

        //Iniciando o banco de dados
        public function conectar(){
            try{
                $con = mysqli_connect('localhost','root','','mp3');
                if($con){
                    return $con;
                }
            }catch(Exception $erro){
                echo $erro;
            }
        }//fim do método conectar

        public function inserirMidia(string $nome, string $midia){
            try{
                $dataBase = new Conexao();
                $con = $dataBase->conectar();
                $arquivoCodificado = base64_encode($midia);

                //Inserindo o arquivo no banco de dados
                $sql = "insert into midia (idMidia,nome,arquivo) values ('','$nome','$arquivoCodificado') ";
                
                $resultado = mysqli_query($con, $sql);
                if($resultado){
                    return "Inserido com sucesso!";
                }
                return "Erro na inserção do banco de dados";
            }catch(Exception $erro){
                echo $erro;
            }
        }
    }//fim da classe
?>