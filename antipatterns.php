<?php

//Магические числа (Magic numbers)
public function login()
{
    if ($this->validate()) {
        return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    return false;
}
//Решение: вместо использования непосредственно чисел в коде хорошей практикой считается объявление их в виде константы
// с говорящим названием и, при необходимости, сопровождение их комментарием.

//1 вариант
//Количество секунд, в течение которых пользователь может оставаться залогиненным в системе
define('DURATION', 2592000);

//2 вариант
//Константы времени для вычисления параметра $duration в методе login()
define('SECONDS', 3600);
define('HOURS', 24);
define('DAYS', 30);

//Шифрованный код (Cryptic Code)
function delNews($idx) {
    $sql = "DELETE FROM `news` WHERE `news`.`id_news` = {$idx}";
    executeQuery($sql);
}
//Решение: заменить аббревиатуры в именах сущностей на мнемонические имена deleteNews

//Сплошное одиночество (Singletonitis)
trait TSingleton
{
    private static $instance = null;

    private function __construct() {}

    private function __clone() {}

    private function __wakeup() {}

    /**
     * @return static
     */
    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
//Решение: свести к крайне необходимому минимуму использование классов-одиночек. В идеальной ситуации — избавиться от них вовсе.

//Приватизация (Privatization) или Инверсия абстракции (Abstract Inversion)
class Request
{
    ...
    private function parseRequest() {
        $pattern = '#(?P<controller>\w+)[/]?(?P<action>\w+)?[/]?[?]?(?P<params>.*)?#ui';
        if (preg_match_all($pattern, $this->requestString, $matches)) {
            $this->controllerName = $matches['controller'][0];
            $this->actionName = $matches['action'][0];
            $this->params = $_REQUEST;
        }
    }
    ...
}

//Решение: функционально важные методы в классе лучше объявлять как защищённые (protected), чтобы иметь возможность их переопределения в потомках.
//При условии, конечно, что рассматриваемый класс имеет потенциальную возможность для расширения.

//Слепая вера (Blind Faith)
//Недостаточная проверка корректности исправления ошибки или результата работы подпрограммы.
public function actionAdd() {
    if(App::call()->request->isPost()) {
        $good_id = (int) App::call()->request->getParam('id_good');
        $quantity = (int) App::call()->request->getParam('quantity') ?: 1;
        $result['id'] = $this->repository->addGood($good_id, $quantity);

        echo json_encode($result);
    }
}

//Решение: всегда проверять возвращаемые извне результаты на ошибки. Потенциально опасные блоки необходимо оборачивать в конструкцию try … catch, 
//позволяющую отловить исключительные события и как-то на них среагировать.
if($good_id)
	$result['id'] = $this->repository->addGood($good_id, $quantity);
else
	 ...