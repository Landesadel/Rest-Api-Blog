<?php

namespace Landesadel\easyBlog\GitHooks;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action as HookAction;
use Exception;
use phpDocumentor\Reflection\Types\Self_;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository as Repo;
use CaptainHook\App\Config\Action;


class PrepareCommitMessage implements HookAction
{
    private const ARRGITFLOWNAME = ['feature/', 'bugfix/', 'hotfix/', 'test_'];

    /**
     * Execute the action.
     *
     * @param  Config   $config
     * @param  IO       $io
     * @param  Repo     $repository
     * @param  Action   $action
     * @return void
     * @throws Exception
     */
    public function execute(Config $config, IO $io, Repo $repository, Config\Action $action): void
    {
        //откапываем сообщение коммита
        $oldMessage = $repository->getCommitMsg();// возвращает класс CommitMessage для дальнейшего изменения коммита
        $currentCommit = $oldMessage->getContent();// сам текст коммита

        //достаем текущую ветку
        $currentBranch = $repository->getInfoOperator()->getCurrentBranch();//полное название ветки

        $reg = "/^([a-z]*\/|test_)?(ABC)*-?([0-9]*)_?-?([A-Za-z_А-Яа-яёЁ 0-9\W]*)/ui";

        //подготавливаем массивы для коммита и ветки -> туда будут падать части названия
        $arrValueCommit = [];
        $arrValueBranch = [];
        $newCommitMessage = '';

        //выковыриваем части из названии ветки
        preg_match($reg, $currentBranch, $arrValueBranch);

        //выковыриваем части из названия коммита
        preg_match($reg, $currentCommit, $arrValueCommit);
        $trimCommitMessage = trim($arrValueCommit[4]);

        // пристально сравниваем
        if (empty(trim($arrValueCommit[0]))) { //если коммит пустой
            throw new \Exception('error: You SHALL NOT pass without commit!' . PHP_EOL);
        } else {
            if (!empty($arrValueBranch[1])) { //если flowname есть в ветке
                if (!in_array($arrValueBranch[1], self::ARRGITFLOWNAME)) {
                    throw new \Exception('Некорректный gitflowname! Доступные варианты - '. implode(self::ARRGITFLOWNAME) . PHP_EOL);
                }

                if (!empty($arrValueBranch[3])) { //если стоит номер задачи
                    $newCommitMessage .= !empty($arrValueBranch[2])
                        ? ($arrValueBranch[2] . '-' . $arrValueBranch[3] . '_' . $trimCommitMessage)
                        : 'MBM-' . $arrValueBranch[3] . '_' . $trimCommitMessage;
                } else { //если номера задачи нет
                    if (!empty($arrValueBranch[2])) {
                        throw new \Exception('Отсутствует номер задачи в ветке!' . PHP_EOL);
                    } else {
                        $newCommitMessage .= $trimCommitMessage;
                    }
                }
            } else { //если нет gitflowname
                if (!empty($arrValueBranch[2]) || !empty($arrValueBranch[3])) { //если MBM или номера задачи нет в ветке
                    throw new \Exception('Отсутствует gitflowname в названии ветки!' . PHP_EOL);
                } else {
                    if ($arrValueBranch[4] === 'master' || $arrValueBranch[4] === 'dev') {
                        $newCommitMessage .= $trimCommitMessage;
                    } else {
                        throw new \Exception('Некорректное название ветки! Доступные варианты - dev | master' . PHP_EOL);
                    }
                }
            }


        }

        $repository->setCommitMsg(new CommitMessage($newCommitMessage, $oldMessage->getCommentCharacter()));
    }
}