<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\service\GameService;

class Draw extends Command
{
    protected function configure()
    {
        $this->setName('draw')
            ->setDescription('draw Script');
    }

    protected function execute(Input $input, Output $output)
    {
        GameService::game_draw();
    }
}
