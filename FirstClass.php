<?php

class SQLiteWork {

private $pdo;
private $id, $name, $surname, $bday, $sex, $city;

function __construct($newName = 0, $newSurname = 0, $newBday = 0, $newSex = 0, $newCity=0)
{
$pdo = new SQLite3("bd/peoples3.bd");
echo "Connected to bd successfully.<br>";
$this->pdo = $pdo;

//проверка есть ли уже такой же человек в БД
if ($newName == 0 or $newSurname == 0 or $newBday == 0) {
//значит человека нет в базе(нет смсысла проверять по отсутсвующим ключевым параметрам, а также нет смысла его записывать в базу без этих параметров)
}
else {
$this->name = $newName;
$this->surname = $newSurname;
$this->bday = $newBday;
$this->sex = $newSex;
$this->city = $newCity;

$result = $this->isThatPersonInTable($newName, $newSurname, $newBday, $newSex, $newCity);

if ($result == true) {
//значит человек найден в базе
}
else {
SQLiteWork::insertToPeoples();
}
}
}


function isThatPersonInTable() {
$pdo = new SQLite3("bd/peoples3.bd");

$stmt = $pdo->prepare('SELECT *
FROM peoples
WHERE name = :name AND surname = :surname ;');    //возвращает объект pdo statement без данных еще
$stmt->bindParam(':name', $this->name);   //Возвращает true в случае успешного выполнения или false в случае возникновения ошибки.
$stmt->bindParam(':surname', $this->surname);
$result = $stmt->execute();    //Возвращает объект SQLite3Result
$nrows = 0;
while ($row = $result->fetchArray())
{
$nrows++;
};
if ($nrows >= 1) $ret = true; else $ret = false;
if ($ret == false ) echo "Человек не найден в базе<br>";
if ($ret == true ) echo "Человек найден в базе<br>";
return $ret;
}

function insertToPeoples() {

$sql = 'INSERT INTO peoples (name, surname, bday, sex, city) '
. 'VALUES(:name,:surname,:bday,:sex,:city)';

$stmt = $this->pdo->prepare($sql);

$stmt->bindParam(':name', $this->name);   //Возвращает true в случае успешного выполнения или false в случае возникновения ошибки.
$stmt->bindParam(':surname', $this->surname);
$stmt->bindParam(':bday', $this->bday);
$stmt->bindParam(':sex', $this->sex);
$stmt->bindParam(':city', $this->city);

$stmt->execute();

}

static function deletePersonFromTable ($id) {
$pdo = new SQLite3("bd/peoples3.bd");
$sql = 'DELETE FROM peoples '
. 'WHERE id = :id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id);

$stmt->execute();
echo "Человек удален из базы.<br>";


}
static function sexFromNumToStringDescription ($name, $surname) {
$pdo = new SQLite3("bd/peoples3.bd");
$stmt = $pdo->prepare('SELECT sex
FROM peoples
WHERE name = :name AND surname = :surname ;');
$stmt->bindParam(':name', $name);
$stmt->bindParam(':surname', $surname);

$result = $stmt->execute();

$row = $result->fetchArray();

if ($row['sex']==0) $result = 'муж.'; else if ($row['sex']==1) $result = 'жен.';

return $result;
}

    static function calculateAge($name, $surname){
        $pdo = new SQLite3("bd/peoples3.bd");
        $stmt = $pdo->prepare('SELECT bday 
                             FROM peoples
                             WHERE name = :name AND surname = :surname ;');
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);

        $result = $stmt->execute();

        $row = $result->fetchArray();


//            echo $personBday = date($row['bday']);
//            echo $today = date("j.m.Y");
        $personBday = date($row['bday']);
        $birthday_timestamp = strtotime($personBday);
        $age = date('Y') - date('Y', $birthday_timestamp);
        if (date('md', $birthday_timestamp) > date('md')) {
            $age--;
        }

        echo $age;
        return $age;

    }

}
?>