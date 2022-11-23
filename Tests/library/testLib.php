<?php

namespace Landesadel\easyBlog\UnitTests\library;


$reg = "/^([a-z]*\/|test_)?(MBM)*-?([0-9]*)_?-?([A-Za-z_А-Яа-яёЁ 0-9\W]*)/ui"; //сгрупировать тикет и сам коммит исп preg_math сравнивать и смотреть что по сравнению частям


const ARRGITFLOWNAME = ['feature/', 'bugfix/', 'hotfix/', 'test_'];

// переписать ещё с проверкой feature
$currentBranch = "feature/MBM-643_library_captainhook";
$currentCommit = "test commit";

//подготавливаем массивы для коммита и ветки -> туда будут падать части названия
$arrValueCommit = $arrValueBranch = [];

$newCommitMessage = '';

//выковыриваем части из названия ветки
preg_match($reg, $currentBranch, $arrValueBranch);

//выковыриваем части из названия коммита
preg_match($reg, $currentCommit, $arrValueCommit);
$trimCommitMessage = trim($arrValueCommit[4]);



//предполагаемый массив разбиваемый регуляркой
//[0] => feature/ABC-1254_testing_library_captainhook
//[1] => feature
//[2] => ABC
//[3] => 1254
//[4] => _testing_library_captainhook

// пристально сравниваем
if (empty(trim($arrValueCommit[0]))) {//если коммит пустой
    echo 'error: You shall not pass without commit!' . PHP_EOL;
    die ();
} else {
    if (!empty($arrValueBranch[1])) { //если flowname есть в ветке
        $flowName = in_array($arrValueBranch[1], ARRGITFLOWNAME);

        if (!$flowName) {
            echo('Некорректный gitflowname!' . PHP_EOL);
            die();
        }

        if (!empty($arrValueBranch[3])) { //если стоит номер задачи
            $newCommitMessage .= !empty($arrValueBranch[2])
                ? ($arrValueBranch[2] . '-' . $arrValueBranch[3] . '_' . $trimCommitMessage)
                : 'MBM-' . $arrValueBranch[3] . '_' . $trimCommitMessage;
        } else { //если номера задачи нет
            if (!empty($arrValueBranch[2])) {
                echo('Отсутствует номер задачи в ветке!' . PHP_EOL);
                die();
            } else {
                $newCommitMessage .= $trimCommitMessage;
            }
        }
    } else { //если нет gitflowname
        if (!empty($arrValueBranch[2]) || !empty($arrValueBranch[3])) { //если MBM и номера задачи нет в ветке
            echo('Отсутствует gitflowname в названии ветки!' . PHP_EOL);
            die();
        } else {
            $newCommitMessage .= $trimCommitMessage;
        }
    }
}
    echo($newCommitMessage . PHP_EOL);


//echo $searchTicket . PHP_EOL;
print_r($arrValueCommit);
print_r($arrValueBranch);
//echo $arrValueBranch[0] . PHP_EOL;
//echo $numberTicketCommit . PHP_EOL;