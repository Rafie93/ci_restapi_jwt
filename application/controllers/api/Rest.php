<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '../vendor/autoload.php';
require APPPATH . './libraries/REST_Controller.php';
require APPPATH . './libraries/BeforeValidException.php';
require APPPATH . './libraries/ExpiredException.php';
require APPPATH . './libraries/SignatureInvalidException.php';

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;

class Rest extends REST_Controller {
    private $secretkey = 'XXX'; //ubah dengan kode rahasia apapun
    public function __construct(){
        parent::__construct();
        $this->load->library('form_validation');
    }
    //method untuk not found 404
  public function notfound($pesan){

    $this->response([
      'status'=>FALSE,
      'message'=>$pesan
    ],REST_Controller::HTTP_NOT_FOUND);

  }

  //method untuk bad request 400
  public function badreq($pesan){
    $this->response([
      'status'=>FALSE,
      'message'=>$pesan
    ],REST_Controller::HTTP_BAD_REQUEST);
  }

  //method untuk melihat token pada user
  public function login_post(){
    $this->load->model('Loginmodel');

    $date = new DateTime();
    $phone = $this->post('phone',TRUE);
    $pass = $this->post('password',TRUE);

    $options = [
        'cost' => 10,
    ];
    $pass_hash =  password_hash($pass, PASSWORD_DEFAULT,$options);

    $dataadmin = $this->Loginmodel->is_valid($phone);

    // echo json_encode($pass_hash);

    if ($dataadmin) {

      if (password_verify($pass,$dataadmin->password)) {

        $payload['id'] = $dataadmin->id;
        $payload['phone'] = $dataadmin->phone;
        $payload['iat'] = $date->getTimestamp(); //waktu di buat
        $payload['exp'] = $date->getTimestamp() + 259200;//3 hari (3600/jam)

        $output['id_token'] = JWT::encode($payload,$this->secretkey);
        $this->response([
          'status'=>'Success',
          'Message'=>'Anda Berhasil Login',
          'Token'=>JWT::encode($payload,$this->secretkey),
          ],REST_Controller::HTTP_OK);
      }
      else {
        $this->viewtokenfail($phone,$pass);
      }
    }else {
      $this->viewtokenfail($phone,$pass);
    }
  }

  //method untuk jika view token diatas fail
  public function viewtokenfail($phone,$pass){
    $this->response([
      'status'=>'Hihi Gagal.!!',
      'phone'=>$phone,
      'password'=>$pass,
      'message'=>'phone dan Password yang anda masukan salah'
      ],REST_Controller::HTTP_BAD_REQUEST);
  }

//method untuk mengecek token setiap melakukan post, put, etc
  public function cektoken(){
    $this->load->model('Loginmodel');
    $jwt = $this->input->get_request_header('Authorization');

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // echo json_encode($decode);
      //melakukan pengecekan database, jika phone tersedia di database maka return true
      if ($this->Loginmodel->is_valid_num($decode->phone)>0) {
        return $decode;
      }

    } catch (Exception $e) {
      //$response = ['status' => 'false', 'message' => 'Token Expired'];
      //$this->response($response);
       exit('Token Expired');

    }
  }
}
?>