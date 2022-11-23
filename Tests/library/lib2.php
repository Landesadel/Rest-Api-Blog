<?php

$reg = "/kjrnfg([a-z]*\/_)?(MBM)*-?([0-9]*)_?([A-Za-zА-Яа-яёЁ0-9 \W_]*)/ui";
$arrGitFlowName = ['feature/', 'bugfix/', 'hotfix/', 'test_'];

$currentBranch = "feature/MBM-123_testing_library_captainhook";
$currentCommit = ".add-св, _-><?!@3#ssdfsdfsfome";

//подготавливаем массивы для коммита и ветки -> туда будут падать части названия
$arrValueCommit = [];
$arrValueBranch = [];
$newCommitMessage = '';

//выковыриваем части из названии ветки
$searchBranchParts = preg_match($reg, $currentBranch, $arrValueBranch);

//выковыриваем части из названия коммита
$searchCommitParts = preg_match($reg, $currentCommit, $arrValueCommit);
$trimCommit = trim($arrValueCommit[4]);

//пристально сравниваем
if (empty(trim($currentCommit))) {//если коммит пустой
    echo 'error: You shall not pass without commit!' . PHP_EOL;
    die();
} else {
    if (!empty($arrValueBranch[1])) { //если flowname есть в ветке
        foreach ($arrGitFlowName as $flowName) {//сверяем корректность flowname
            if ($flowName === $arrValueBranch[1]) {
                $flowName = true;
                break;
            }
            $flowName = false;
        }

        if (!$flowName) {
            echo('Некорректный gitflowname!' . PHP_EOL);
            die();
        }

        if (!empty($arrValueBranch[2])) { //если стоит MBM
            if (!empty($arrValueBranch[3])) { //если стоит номер задачи
                $newCommitMessage .= $arrValueBranch[2] . '-' . $arrValueBranch[3] . '_' . $trimCommit;
            } else { //если номера задачи нет
                echo('Отсутствует номер задачи в ветке!' . PHP_EOL);
                die();
            }
        } else { //если не стоит MBM
            if (!empty($arrValueBranch[3])) { //если стоит номер задачи
                $newCommitMessage .= 'MBM-' . $arrValueBranch[3] . '_' . $trimCommit;
            } else {  //если не стоит номер задачи
                $newCommitMessage .= $trimCommit;
            }
        }
    } else { //если нет gitflowname
        if (empty($arrValueBranch[2])) { //если MBM нет в ветке
            if (empty($arrValueBranch[3])) { //если номера нет в ветке
                $newCommitMessage .= $trimCommit;
            } else { //если номер задачи есть в ветке
                echo('Отсутствует gitflowname в названии ветки!' . PHP_EOL);
                die();
            }
        } else { //если MBM нет в ветке
            echo('Отсутствует gitflowname в названии ветки!' . PHP_EOL);
            die();
        }
    }
}

echo($newCommitMessage . PHP_EOL);

//echo $searchTicket . PHP_EOL;
//print_r($arrValueCommit);
print_r($arrValueBranch);
//echo $arrValueBranch[0] . PHP_EOL;
//echo $numberTicketCommit . PHP_EOL;