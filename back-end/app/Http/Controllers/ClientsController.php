<?php

namespace App\Http\Controllers;

use App\Models\Clients as ClientsModel;
use Illuminate\Http\Request;
use Exception;

class ClientsController extends Controller
{

    private $model;
    private $defaultMessageError = [
        'message' => 'Oops, ocorreu um erro interno, tente novamente!'
    ];
    
    /**
     * ClientsController.
     *
     * @return void
     */
    public function __construct(ClientsModel $model)
    {
        $this->model = $model;
    }

    public function read(Request $request) {
        try{

            $get = $request->all();

            $model = $this->model;

            if( isset($get['name']) ) {
                $model = $model->where('name','like','%'.$get['name'].'%');
            }

            if( isset($get['email']) ) {
                $model = $model->where('email','like','%'.$get['email'].'%');
            }

            if( isset($get['birthdate_from']) && !isset($get['birthdate_to']) ) {
                $model = $model->where('birthdate','>=',$get['birthdate_from']);
            }

            if( !isset($get['birthdate_from']) && isset($get['birthdate_to']) ) {
                $model = $model->where('birthdate','<=',$get['birthdate_to']);
            }

            if( isset($get['birthdate_from']) && isset($get['birthdate_to']) ) {
                $model = $model->whereBetween('birthdate',[$get['birthdate_from'],$get['birthdate_to']]);
            }

            $orderBy = isset($get['order_by'])??$get['order_by'];
            $orderByType = isset($get['order_by_type'])? $get['order_by_type'] : 'desc';

            $perPage = 15;

            if( isset($get['perPage']) ) {
                if( is_numeric($get['perPage'])) {
                    $perPage = (int) $get['perPage'];
                }
            }
            
            switch($orderBy) 
            {
                default:
                case 'id':
                    $datasReads = $model->orderBy('id',$orderByType)->paginate($perPage);  
                break;              

                case 'name':
                    $datasReads = $model->orderBy('name',$orderByType)->paginate($perPage);  
                break;      
                
                case 'email':
                    $datasReads = $model->orderBy('email',$orderByType)->paginate($perPage);  
                break;  
                
                case 'birthdate':
                    $datasReads = $model->orderBy('birthdate',$orderByType)->paginate($perPage);  
                break;  

            }

            return response()->json($datasReads);

        }catch(Exception $e){                    
            return $this->defaultError();
        }
    }

    public function store(Request $request) {
        try{

            $post = $request->all();

            if( !isset($post['name']) ) {
                return $this->messageError('Oops, por favor informe o nome!');
            }

            if( !isset($post['email']) ) {
                return $this->messageError('Oops, por favor informe o e-mail!');
            }

            if( !isset($post['birthdate']) ) {
                return $this->messageError('Oops, por favor informe o a data de nascimento!');
            }

            if ( $this->model->where('email',$post['email'])->count() < 1 )  {
                
                $dataToInsert = [
                    'name' => $post['name'],
                    'email' => $post['email'],
                    'birthdate' => $post['birthdate']
                ];

                $created = $this->model->create($dataToInsert);
    
                if( !$created ) {
                    return $this->messageError('Oops, aconteceu alguma coisa ao cadastrar cliente, tente novamente!');
                }
    
                return response()->json(['error' => false, 'message' => 'Parabéns! Cliente cadastrado com sucesso!', 'data' => $created]);
            }else{
                return $this->messageError('Oops, e-mail já está cadastrado!');
            }


        }catch(Exception $e){                    
            return $this->defaultError();
        }
    }

    public function update(Request $request) {
        try{

            $post = $request->all();

            if( !isset($post['id']) ) {
                return $this->messageError('Oops, cliente não encontrado!');
            }

            if( !isset($post['name']) ) {
                return $this->messageError('Oops, por favor informe o nome!');
            }

            if( !isset($post['email']) ) {
                return $this->messageError('Oops, por favor informe o e-mail!');
            }

            if( !isset($post['birthdate']) ){
                return $this->messageError('Oops, por favor informe o a data de nascimento!');
            }

            if ( $this->model->where('id','!=',$post['id'])->where('email',$post['email'])->count() < 1 )  {
                
                $dataToUpdate = [
                    'name' => $post['name'],
                    'email' => $post['email'],
                    'birthdate' => $post['birthdate']
                ];

                $updated = $this->model->where('id',$post['id'])->update($dataToUpdate);
    
                if( !$updated ) {
                    return $this->messageError('Oops, aconteceu alguma coisa ao atualizar cliente, tente novamente!');
                }
    
                return response()->json(['error' => false, 'message' => 'Parabéns! Cliente atualizado com sucesso!']);
            }else{
                return $this->messageError('Oops, e-mail já está cadastrado!');
            }


        }catch(Exception $e){      
            return $this->defaultError();
        }
    }

    public function delete($client_id) {
        try{

            if( !isset($client_id) ) {
                return $this->messageError('Oops, cliente não encontrado!');
            }

            if ( $this->model->where('id','!=',$client_id)->count() > 0 )  {
                              
                $deleted = $this->model->where('id',$client_id)->delete();
    
                if( !$deleted ) {
                    return $this->messageError('Oops, aconteceu alguma coisa ao excluír cliente, tente novamente!');
                }
    
                return response()->json(['error' => false, 'message' => 'Cliente excluído com sucesso!']);
            }else{
                return $this->messageError('Oops, cliente não encontrado!');
            }

        }catch(Exception $e){         
            return $this->defaultError();
        }
    }

    private function defaultError(){
        return response()->json($this->defaultMessageError,500);
    }

    private function messageError($message){
        return response()->json(['error' => true, 'message' => $message]);
    }
}
