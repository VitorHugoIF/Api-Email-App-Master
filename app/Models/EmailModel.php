<?php
/**
 * Created by PhpStorm.
 * User: vitor
 * Date: 26/11/2018
 * Time: 13:36
 */

namespace App\Models;
use Faker\Factory as FakerFactory;
class EmailModel
{
    private $enderecoEmail;
    private $enviado;

    public function __construct($enderecoEmail){
        $this->enderecoEmail = $enderecoEmail;
        $this->enviado =false;
    }

    public static function filter($string){
        $arr = array();
        $arr2 = array();
        $arquivo = fopen ("../emails.txt", 'a+');
        while(!feof($arquivo)){
            $linha = fgets($arquivo);
            array_push($arr,$linha);
        }
        fclose($arquivo);

        foreach ($arr as $end){
            if(strpos($end, $string)!=0 ){
                array_push($arr2, $end);
            }
        }
        return $arr2;
    }
    public static function sort($array){
        $arr = $array;
        sort($arr);
        return $arr;
    }

    public function send(){
        $faker = FakerFactory::create('pt_BR');
        $this->enviado = $faker->boolean;
    }
    /**
     * @return array
     */
    public function getEmailList(): array
    {
        return $this->emailList;
    }

    public function getEnviado()
    {
        return $this->enviado;
    }
}