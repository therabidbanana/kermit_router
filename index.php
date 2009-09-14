<?php

include "lib/loader.php";

function foo($usr_id, $out_lang='en') {
    global $xmlrpcerruser;
	
    if ($someErrorCondition)
      return new xmlrpcresp(0, $xmlrpcerruser+1, 'DOH!');
    else
      return array(
        'name' => 'Joe',
        'age' => 27,
        'picture' => new xmlrpcval(file_get_contents($picOfTheGuy), 'base64')
      );
}

  $s = new xmlrpc_server(
    array(
      "examples.myFunc" => array(
        "function" => "bar::foobar",
        "signature" => array(
          array($xmlrpcString, $xmlrpcInt),
          array($xmlrpcString, $xmlrpcInt, $xmlrpcString)
        )
      )
    ), false);
  $s->functions_parameters_type = 'phpvals';
  $s->service();