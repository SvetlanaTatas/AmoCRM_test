<?php

function curl_zapros($link_n,$headers_n, $request_n, $data_n){

      $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
      curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
	  curl_setopt($curl,CURLOPT_URL, $link_n);
	  curl_setopt($curl,CURLOPT_HTTPHEADER, $headers_n);
	  curl_setopt($curl,CURLOPT_HEADER, false);
    if($request_n!='GET'){
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $request_n);
      curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data_n)); 
    }
  	  curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
	  curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);

	$out=curl_exec($curl); 
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	curl_close($curl);
	$response=json_decode($out,true);
	$result['code']=$code;
	$result['response']=$response;
	return $result;
}

if($_SERVER['REQUEST_METHOD'] == 'GET') {
  $subdomain = 'cvetok255'; //Поддомен нужного аккаунта
  

  /** Получаем access_token из вашего хранилища */
  $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQwZmVhOTVlY2Y3NGZmMjU3YmJlYzE3YjllNjg5NDg5Mjk4ZmMwOTY2NzY4OGI1YWI4M2U1OTY1MzFmNjkwNGJmYjFiYmFmYTkzNGVhYWUwIn0.eyJhdWQiOiJjMTdkNzgwMC01ZGRiLTQ3ZWMtOTNiNS03OWI0MTI1ZDBjYzAiLCJqdGkiOiJkMGZlYTk1ZWNmNzRmZjI1N2JiZWMxN2I5ZTY4OTQ4OTI5OGZjMDk2Njc2ODhiNWFiODNlNTk2NTMxZjY5MDRiZmIxYmJhZmE5MzRlYWFlMCIsImlhdCI6MTYyODM0NTk1MSwibmJmIjoxNjI4MzQ1OTUxLCJleHAiOjE2Mjg0MzIzNTEsInN1YiI6IjczMTE2MjgiLCJhY2NvdW50X2lkIjoyOTYzMDg5Niwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXX0.XQmIy0mHvp50T7ZAaeHsBF1Y2JfQMfWp9hKqX-xY11x9fhqLCYWVNJb6pb0M8tE71dNfZsbMme8yydmUSQy8JTwgFBR1UdWkQNCXtzQ29Orq1IyFgDfjfhlj7WRtQpthRotNmO11IqI1hPmBPixWMYAnfzOtxYOqti8pTbJMLwriGvTnNkKgte-3n_GlCcZ5u4x2ro7RRE_pAPhw82PHguH0S7qudUzFzWwqJfI1FkRlj3WfYkNFylD9pExouoYHlYVjE9d_segQOwEMXZbw0QUkrgPg5cGbm3R2EcbhvdPwHb4fGTtWv6gwRCatbsECreJAtvRH8u3pMEYoK72Fbw';
  
  /** Формируем заголовки */
  $headers = [
              'Accept: application/json',
	          'Authorization: Bearer ' . $access_token
             ];
  /**формируем ссылку для получения данных*/
  $link_get_contact = 'https://' . $subdomain . '.amocrm.ru/api/v4/contacts?query='.$_GET['telephon'].''; //Формируем URL для запроса
  
  /** вызываем функцию с запросом**/
  $result_get_contact=curl_zapros($link_get_contact,$headers,'GET',array());


  /**Проверяем успешно выполнен запрос или нет*/
  if($result_get_contact['code']=='200'){
      
      echo "Контакт с таким телефоном есть. ";
      $id_contact=$result_get_contact['response']['_embedded']['contacts'][0]['id'];
      echo 'ID контакта : '.$id_contact.'. ';

      $data_update=[array('id'=>$result_get_contact['response']['_embedded']['contacts'][0]['id'],
             'name'=>''.$_GET["first_name"].' '.$_GET["otchectvo"].'',
             'first_name'=>''.$_GET["first_name"].'',
             'last_name'=>''.$_GET["last_name"].'',
             'custom_fields_values'=>[array('field_id'=>334409,
                                            'field_name'=>'Email',
                                            'values'=>[array('value'=>''.$_GET["pochta"].'',
                                                             'enum_code'=>'WORK')
                                                      ]
                                            )
                                      ]
            )
        ];
        
      /** Формируем заголовки */
      $headers_update = [
              'Content-Type: application/json',
	          'Authorization: Bearer ' . $access_token
             ];
        
      /**формируем ссылку для получения данных*/
      $link_update_contact = 'https://' . $subdomain . '.amocrm.ru/api/v4/contacts'; //Формируем URL для запроса
      
      $result_update_contact=curl_zapros($link_update_contact,$headers_update,'PATCH',$data_update);
      if($result_update_contact['code']=='200'){echo "Новый контакт обновлен. ";}
      
      /**Добавляем сделку*/
        $headers_add_leads = [
              'Content-Type: application/json',
	          'Authorization: Bearer ' . $access_token
             ];
      $link_add_leads = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads'; //Формируем URL для запроса
      
      $data_add_leads=[array('name'=>'Сделка для примера 1',
                             'created_by'=> 0,
                             'price'=> 20000,
                             '_embedded'=> array('contacts'=>[array('id'=>$id_contact)])
                            )
                      ];
      $result_add_leads=curl_zapros($link_add_leads,$headers_add_leads,'POST',$data_add_leads); 
      if($result_add_leads['code']=='200'){
        echo "Сделка создана";  
      }
      
  } else if($result_get_contact['code']=='204'){
      echo "Поиск не дал результатов.";
      
      $data_add=[array('name'=>''.$_GET["first_name"].'',
                       'first_name'=>''.$_GET["first_name"].'',
                       'custom_fields_values'=>[array('field_id'=>334407,
                                                     'values'=>[array('value'=>$_GET["telephon"])]
                                                    ),
                                               array('field_id'=>334409,
                                                     'values'=>[array('value'=>''.$_GET["pochta"].'')]
                                                    )]
                     )
                 ];
      /**формируем ссылку для получения данных*/
      $link_add_contact = 'https://' . $subdomain . '.amocrm.ru/api/v4/contacts'; //Формируем URL для запроса
      
      $result_add_contact=curl_zapros($link_add_contact,$headers,'POST',$data_add);
      if($result_add_contact['code']=='200'){echo "Новый контакт добавлен.";}
      
  } else {
      echo "Код ошибки: ".$result_get_contact['code'].". Что-то пошло не так!";
      
  }
  


}
?>