<?php

namespace App\Http\Controllers;

use App\repository\EmailDAO;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    private $dao;
    public function __construct( EmailDAO $emaildao )
    {
        $this->dao  =  $emaildao;
    }

    public function insertList(Request $request){

        return $this->dao->insertEmails($request->toArray());
    }
    public function sendEmail(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'subject' => 'required',
                'body' => 'required'
            ]

        );
        if($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }else{
            $this->dao->log($request);
        }
    }
    public function getEmail(){
        return $this->dao->getEmail();
    }
}
