<?php


namespace Ecotone\SymfonyBundle\DepedencyInjection;


use Ecotone\Messaging\Config\Annotation\ModuleConfiguration\ConsoleCommandModule;
use Ecotone\Messaging\Config\OneTimeCommandParameter;
use Ecotone\Messaging\Config\OneTimeCommandResultSet;
use Ecotone\Messaging\Gateway\MessagingEntrypoint;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessagingEntrypointCommand extends Command
{
    private MessagingEntrypoint $messagingEntrypoint;
    private string $requestChannel;
    private string $name;
    private array $parameters;

    /**
     * @var OneTimeCommandParameter[] $parameters
     */
    public function __construct(string $name, string $requestChannel, string $parameters, MessagingEntrypoint $messagingEntrypoint)
    {
        $this->name = $name;
        $this->messagingEntrypoint = $messagingEntrypoint;
        $this->requestChannel = $requestChannel;
        $this->parameters = unserialize($parameters);

        parent::__construct();
    }

    protected function configure()
    {
        foreach ($this->parameters as $parameter) {
            if ($parameter->hasDefaultValue()) {
                $this->addArgument($parameter->getName(), InputArgument::OPTIONAL, "", $parameter->getDefaultValue());
            }else {
                $this->addArgument($parameter->getName(), InputArgument::REQUIRED);
            }
        }

        $this
            ->setName($this->name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $arguments = [];
        foreach ($input->getArguments() as $argumentName => $value) {
            $arguments[ConsoleCommandModule::ECOTONE_COMMAND_PARAMETER_PREFIX . $argumentName] = $value;
        }

        /** @var OneTimeCommandResultSet $result */
        $result = $this->messagingEntrypoint->sendWithHeaders([], $arguments, $this->requestChannel);

        if ($result) {
            $table = new Table($output);
            $table
                ->setHeaders($result->getColumnHeaders())
                ->setRows($result->getRows());

            $table->render();
        }

        return 0;
    }
}