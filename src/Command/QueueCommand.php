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

    // "queue_bind"
    // "queue_unbind"
    // "queue_declare"
    // "queue_delete"
    // "queue_purge"

    protected function configure()
    {
        $this->setName('mq:queue')->setDescription('rabbitmq queue manage.');
        $this->parseArguments();
        $this->addOption('queue', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ queue name')
            ->addOption('exchange', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ exchange name')
            ->addOption('route', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ route name', "")
            ->addOption('passive', null, InputOption::VALUE_NONE, 'RabbitMQ queue passive')
            ->addOption('durable', null, InputOption::VALUE_NONE, 'RabbitMQ queue durable')
            ->addOption('exclusive', null, InputOption::VALUE_NONE, 'RabbitMQ queue exclusive')
            ->addOption('auto_delete', null, InputOption::VALUE_NONE, 'RabbitMQ queue auto_delete')
            ->addOption('if_unused', null, InputOption::VALUE_NONE, 'RabbitMQ queue if unused')
            ->addOption('if_empty', null, InputOption::VALUE_NONE, 'RabbitMQ queue if empty')
            ->addOption('nowait', null, InputOption::VALUE_NONE, 'RabbitMQ queue nowait');
    }

    protected function cmdQueueBind($input, $output)
    {
        $queue = $input->getOption('queue');
        $exchange = $input->getOption('exchange');
        $route = $input->getOption('route');
        $nowait = $input->getOption('nowait');

        if (empty($queue)) {
            $output->writeln("<error>The queue name must not be empty!</error>");

            return;
        }
        if (empty($exchange)) {
            $output->writeln("<error>The exchange name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->queue_bind($queue, $exchange, $route, $nowait);
            $output->writeln("<info>bind queue '$queue' with '$exchange' in '$route'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }

    protected function cmdQueueUnbind($input, $output)
    {
        $queue = $input->getOption('queue');
        $exchange = $input->getOption('exchange');
        $route = $input->getOption('route');

        if (empty($queue)) {
            $output->writeln("<error>The queue name must not be empty!</error>");

            return;
        }
        if (empty($exchange)) {
            $output->writeln("<error>The exchange name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->queue_unbind($queue, $exchange, $route);
            $output->writeln("<info>unbind queue '$queue' with '$exchange' in '$route'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
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

    protected function cmdQueueDelete($input, $output)
    {
        $queue = $input->getOption('queue');
        $ifUnused = $input->getOption('if_unused');
        $ifEmpty = $input->getOption('if_empty');
        $nowait = $input->getOption('nowait');

        if (empty($queue)) {
            $output->writeln("<error>The queue name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->queue_delete($queue, $ifUnused, $ifEmpty, $nowait);
            $output->writeln("<info>delete queue '$queue'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }

    protected function cmdQueuePurge($input, $output)
    {
        $queue = $input->getOption('queue');
        $nowait = $input->getOption('nowait');

        if (empty($queue)) {
            $output->writeln("<error>The queue name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->queue_purge($queue, $nowait);
            $output->writeln("<info>purge queue '$queue'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }
}
