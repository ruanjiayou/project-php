<?php
use \Firebase\JWT\JWT;
use think\Request;

class SmsMessageBLL extends BLL {
  
  public $table = 'sms_message';

  function create($input) {
    $validation = new Validater([
      'title' => 'required|string',
      'content' => 'required|string',
      'type' => 'required|string|default:"system"',
      'json' => 'required|string|default:"[]"',
      'status' => 'required|string|default:"success"',
      'createdAt' => 'required|date|default:datetime'
    ]);
    $data = $validation->validate($input);
    return model($this->table)->add($data);
  }
}
?>