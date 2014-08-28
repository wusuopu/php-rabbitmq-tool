<?php

namespace Wusuopu\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPConnection;

Trait MQTrait
{
    private $connection;
    private $channel;

    private function parseArguments()
    {
        $methods = array();
        $cmdDesc = "The operation will be executed. The valid values are following:";
        foreach (get_class_methods($this) as $name) {
            if (preg_match("/^cmd/", $name)) {
                array_push($methods, $name);
                $cmdDesc .= "\n" . substr($name, 3);
            }
        }
        $this->addArgument('cmd', InputArgument::REQUIRED, $cmdDesc)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ host', "localhost")
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ port', 5672)
            ->addOption('user', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ username', 'guest')
            ->addOption('pswd', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ password', 'guest')
            ->addOption('vhost', null, InputOption::VALUE_OPTIONAL, 'RabbitMQ vhost', '/');
    }

    private function connectRabbit(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $user = $input->getOption('user');
        $pswd = $input->getOption('pswd');
        $vhost = $input->getOption('vhost');
        echo "Connect $host $port $user $pswd $vhost \n";

        try {
            $this->connection = new AMQPConnection($host, $port, $user, $pswd, $vhost);
            $this->channel = $this->connection->channel();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

    private function closeRabbit()
    {
        try {
            $this->channel->close();
            $this->connection->close();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
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
}
