<?php
    $subdomain ='cvetok255'; //Поддомен нужного аккаунта
    $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
	'client_id' => 'c17d7800-5ddb-47ec-93b5-79b4125d0cc0',
	'client_secret' => 'WA4PE79QzI0LQvOSZ5Dz6AclpRn06aj2WsirUPonb2lznOLaMr7ntWoil1NAkR3W',
	'grant_type' => 'authorization_code',
	'code' => 'def50200081c067eb1b57f3775918c10ff27b1174c20c341a1914c6c7477c2dd9aaa3342d9ba37163d339058a9a6c3817ff8893d598a2c341cb2123825163254cc4d851a39e9bdd7c5fb4ee1f462806c113dca44fd8f755ba59f9f3d48574d6f3d724077a32fdde16a43129c877c154fc01e7afdaba4881773b1609c060260991760654e2ab43b2849bf91e6040477d9ad5ed826157c68b1e0316f48bb6a271fc9f261bbbe6f79982b6c33fecb2102036bd5d6caaf81cfb4790b69aa2ddecbc992f5c11b0b085c0ea2254e2e47a0629efc08c4f7bae9825546609088ae3b2d546aa07a660f23995646495fb053e0288e33339d0aa0b8c1c8e975cf424dfd0bbd154305da0073e5926ed2c94877ab2515ebe0ff3960e0bfa21722b0f0b6940255d4bad31c651b9faf5b908ba337e9f08a6096fbf6810fd643924cac552da75b40945185727bd75c4996cbb69038a96bd4ae1af887a3559826f7d9e69b359629a3d336a6f48e8dbae71010f5e57bde4f8b80c9c895b2a73a1080369953609d7e259f07a9654e050064001953cbb3adaf6c4e9e412ac6788e7a3f48e9f3a0cac6bb653c32da295cb754b0496630e94660b95deb56ce3c7d16e9bb419ebfedfa',
	'redirect_uri' => 'https://wellspa.su/',
];

/**
 * Нам необходимо инициировать запрос к серверу.
 * Воспользуемся библиотекой cURL (поставляется в составе PHP).
 * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
 */
$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
/** Устанавливаем необходимые опции для сеанса cURL  */
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
/** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
$code = (int)$code;
$errors = [
	400 => 'Bad request',
	401 => 'Unauthorized',
	403 => 'Forbidden',
	404 => 'Not found',
	500 => 'Internal server error',
	502 => 'Bad gateway',
	503 => 'Service unavailable',
];

try
{
	/** Если код ответа не успешный - возвращаем сообщение об ошибке  */
	if ($code < 200 || $code > 204) {
		throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
	}
}
catch(\Exception $e)
{
	die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}

/**
 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
 * нам придётся перевести ответ в формат, понятный PHP
 */
$response = json_decode($out, true);

$access_token = $response['access_token']; //Access токен
$refresh_token = $response['refresh_token']; //Refresh токен
$token_type = $response['token_type']; //Тип токена
$expires_in = $response['expires_in']; //Через сколько действие токена истекает
echo "<pre>";
print_r($response);
echo "</pre>";

?>