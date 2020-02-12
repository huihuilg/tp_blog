<?php

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;

class test extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('test tp5 cli mode');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("hello test!");
    }




}
