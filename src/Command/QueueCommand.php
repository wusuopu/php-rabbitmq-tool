<?php

namespace Wusuopu\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wusuopu\Command\MQTrait;

/**
 * Console command.
 */
class QueueCommand extends Command
{
    use MQTrait;

    protected function configure()
    {
        $this->setName('mq:queue')->setDescription('rabbitmq queue manage.');
        $this->parseArguments();
        $this->addOption('queue', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ queue name')
            ->addOption('passive', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ queue passive', false)
            ->addOption('durable', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ queue durable', false)
            ->addOption('exclusive', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ queue exclusive', false)
            ->addOption('auto_delete', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ queue auto_delete', false)
            ->addOption('nowait', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ queue nowait', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cmd = $input->getArgument('cmd');
        $func = "cmd$cmd";

        if (!method_exists($this, $func)) {
            echo "'$cmd' operation is not exists.\n";
            exit(1);
        }

        $this->connectRabbit($input, $output);
        $this->$func($input, $output);
        $this->closeRabbit();
    }

    protected function cmdQueueDeclare($input, $output)
    {
        $queue = $input->getOption('queue');
        $passive = $input->getOption('passive');
        $durable = $input->getOption('durable');
        $exclusive = $input->getOption('exclusive');
        $autoDelete = $input->getOption('auto_delete');
        $nowait = $input->getOption('nowait');

        if (empty($queue)) {
            $output->writeln("<error>The queue name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->queue_declare($queue, $passive, $durable, $exclusive, $autoDelete, $nowait);
            $output->writeln("<info>declare queue '$queue'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }
}
