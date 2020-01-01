<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'controllers/api/Rest.php';

class Akuapi extends Rest {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
        $this->cektoken();
    }
    /* index page */
    function index_get($table = '', $id = '') {
        if ($table == '') {
             $response  = array('success' => false);
             $this->response($response, 404);
        } else {
            $datatoken = $this->cektoken();
            $id_user = $datatoken->id;
            //kondisikan supaya dapat id merhcant store
            $id_merchant_store = "";
            if ($id_merchant_store != null) {$this->db->where("id_merchant_store", $id_merchant_store);}

            // $id_merchant = $this->input->get('id_merchant');
            // if ($id_merchant != null) {$this->db->where("id_merchant", $id_merchant);}
            if ($id == '') {
            // baseurl/?table=nama_table (semua data)
                $this->db->where("deleted", "0");
                $data = $this->db->get($table)->result();
            } else {
            // baseurl/?table=nama_table&id=id (satu data)
                $this->db->where("id", $id);
                $data = $this->db->get($table)->result();
            }
            $response  = array('success' => true, 'data'=> $data);
            $this->response($response, 200);
        }
    }
    function index_post($table = '') { // baseurl/?table=nama_table
        $insert = $this->db->insert($table, $this->post());
        $id = $this->db->insert_id();
        if ($insert) {
            $response = array(
                'status' => 'success',
                'table' => $table,
                'id' => $id,
                'data' => $this->post(),
                );
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
    function index_put($table = '', $id = '') { // baseurl/nama_table/id
        $get_id = 'id_'.$table;
        $this->db->where($get_id, $id);
        $update = $this->db->update($table, $this->put());
        if ($update) {
            $response = array(
                'data' => $this->put(),
                'table' => $table,
                'id' => $id,
                'status' => 'success'
                );
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
    function index_delete($table = '', $id = '') {
        $get_id = 'id_'.$table;
        $this->db->where($get_id, $id);
        $delete = $this->db->delete($table);
        if ($delete) {
            $response = array(
                'table' => $table,
                'id' => $id,
                'status' => 'success'
                );
            $this->response($response, 201);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
}
?>