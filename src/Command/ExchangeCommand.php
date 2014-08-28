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
class ExchangeCommand extends Command
{
    use MQTrait;

    // "exchange_bind"
    // "exchange_unbind"
    // "exchange_declare"
    // "exchange_delete"

    protected function configure()
    {
        $this->setName('mq:exchange')->setDescription('rabbitmq exchange manage.');
        $this->parseArguments();
        $this->addOption('exchange', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ exchange name')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ exchange type')
            ->addOption('route', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ route name', "")
            ->addOption('src', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ source exchange name')
            ->addOption('dst', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ destination exchange name')
            ->addOption('passive', null, InputOption::VALUE_NONE, 'RabbitMQ exchange passive')
            ->addOption('durable', null, InputOption::VALUE_NONE, 'RabbitMQ exchange durable')
            ->addOption('auto_delete', null, InputOption::VALUE_NONE, 'RabbitMQ exchange auto_delete')
            ->addOption('internal', null, InputOption::VALUE_NONE, 'RabbitMQ exchange internal')
            ->addOption('if_unused', null, InputOption::VALUE_NONE, 'RabbitMQ exchange if unused')
            ->addOption('nowait', null, InputOption::VALUE_NONE, 'RabbitMQ exchange nowait');
    }

    protected function cmdExchangeBind($input, $output)
    {
        $src = $input->getOption('src');
        $dst = $input->getOption('dst');
        $route = $input->getOption('route');
        $nowait = $input->getOption('nowait');

        if (empty($src)) {
            $output->writeln("<error>The source exchange name must not be empty!</error>");

            return;
        }
        if (empty($dst)) {
            $output->writeln("<error>The destination exchange name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->exchange_bind($dst, $src, $route, $nowait);
            $output->writeln("<info>bind exchange '$dst' to '$src' in '$route'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }

    protected function cmdExchangeUnbind($input, $output)
    {
        $src = $input->getOption('src');
        $dst = $input->getOption('dst');
        $route = $input->getOption('route');

        if (empty($src)) {
            $output->writeln("<error>The source exchange name must not be empty!</error>");

            return;
        }
        if (empty($dst)) {
            $output->writeln("<error>The destination exchange name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->exchange_unbind($dst, $src, $route, $nowait);
            $output->writeln("<info>unbind exchange '$dst' from '$src' in '$route'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }

    protected function cmdExchangeDeclare($input, $output)
    {
        $exchange = $input->getOption('exchange');
        $type = $input->getOption('type');
        $passive = $input->getOption('passive');
        $durable = $input->getOption('durable');
        $autoDelete = $input->getOption('auto_delete');
        $internal = $input->getOption('internal');
        $nowait = $input->getOption('nowait');

        if (empty($exchange)) {
            $output->writeln("<error>The exchange name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->exchange_declare($exchange, $type, $passive, $durable, $autoDelete, $internal, $nowait);
            $output->writeln("<info>declare exchange '$exchange'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }

    protected function cmdExchangeDelete($input, $output)
    {
        $exchange = $input->getOption('exchange');
        $ifUnused = $input->getOption('if_unused');
        $nowait = $input->getOption('nowait');

        if (empty($exchange)) {
            $output->writeln("<error>The exchange name must not be empty!</error>");

            return;
        }
        try {
            $this->channel->exchange_delete($exchange, $ifUnused, $nowait);
            $output->writeln("<info>delete exchange '$exchange'.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }
}
