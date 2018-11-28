<?php
/**
 * Created by PhpStorm.
 * User: vitor
 * Date: 26/11/2018
 * Time: 15:41
 */

namespace App\repository;
use App\Models\EmailModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class EmailDAO
{
    private $model = array();
    private $model2 = array();

    private $emailModel;

    public function insertEmails($listEmails){
        $this->converteArray($listEmails);

        sort($this->model);
        foreach ($this->model as $endereco){
            if(filter_var($endereco, FILTER_VALIDATE_EMAIL)){

                $this->gravarAdcional($endereco . "\r\n");

                if($this->validar($endereco)) {
                    $this->gravar($endereco . "\r\n");
                }
            }
        }
    }
    public function log($email){
        $this->carregaArray();

        $logSend = new Logger('send');
        $logFail = new Logger('fail');
        $logSend->pushHandler(new StreamHandler('../logs/send.log', Logger::INFO));
        $logFail->pushHandler(new StreamHandler('../logs/fail.log', Logger::WARNING));

        $hora = date("H:i:s");
        $assunto = $email["subject"];

        $total = 0;
        $enviados = 0;
        $naoEnviados = 0;


        foreach ($this->model2 as $email){
            if(!empty($email)){
                $this->emailModel = new EmailModel($email);
                $this->emailModel->send();
                if($this->emailModel->getEnviado()){
                    $enviados++;
                    $total++;
                    $logSend->addInfo("hora: $hora, assunto: $assunto, email: $email");
                }else {
                    $naoEnviados++;
                    $total++;
                    $logFail->addWarning("hora: $hora, assunto: $assunto, email: $email");
                }
            }
        }
        $this->resultadoJson($total, $enviados,$naoEnviados);
    }

    private function resultadoJson($total, $enviados,$naoEnviados){
        $resultado = array(
            'emails'   => $total,
            'emails_sent'     => $enviados,
            'emails_fail' => $naoEnviados
        );
        $json = json_encode($resultado);
        return $json;
    }
    private function carregaArray(){
        $arquivo = fopen ("../emails.txt", 'a+');
        while(!feof($arquivo)){
            $linha = fgets($arquivo);
            array_push($this->model2,$linha);
        }
        fclose($arquivo);
    }
    private function converteArray($listEmails){
        foreach ( $listEmails as $emails ) {
            foreach ( $emails as $email ) {
                foreach ( $email as $endereco ) {
                    array_push($this->model, $endereco);
                }
            }
        }
    }
    private function gravar($texto){

        $arquivo = "../emails.txt";
        $fp = fopen($arquivo, "a+");
        fwrite($fp, (string)$texto);
        fclose($fp);
    }
    private function gravarAdcional($texto){
        $d1 =(string) date("d-m-Y");
        $d2 =(string) date("H:i:s");
        $d2 = explode(":", $d2);
        $d2 = implode("_",$d2);

        $arquivo = "../listaEnviados/emails_$d1-$d2.txt";

        $fp = fopen($arquivo, "a+");
        fwrite($fp, (string)$texto);
        fclose($fp);
    }
    private function validar($email){
        $arquivo = "../emails.txt";
        fopen($arquivo, "a+");
        if( strpos(file_get_contents("../emails.txt"),$email) !== false) {
            return false;
        }else{
            return true;
        }
    }

}