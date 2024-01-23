<?php

declare(strict_types=1);

namespace App\Command;

use App\Contracts\CLIInputValidatorInterface;
use App\Enums\ShippingProvider;
use App\Factory\ShippingServiceFactory;
use App\Service\Shipment;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:order:shipment:register',
    description: 'Register order shipment',
)]
class OrderShipmentRegisterCommand extends Command
{
    public function __construct(
        private readonly Shipment $shipmentService,
        private readonly ShippingServiceFactory $shippingServiceFactory,
        private readonly CLIInputValidatorInterface $cliValidator,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addUsage(
                'app:order:shipment:register -o \'{"order_id": 11123234,"country":"LT","address":"addresstest","town":"Vilnius","zip_code":77777}\' -p Dhl'
            )
            ->addOption(
                'shipping-provider',
                'p',
                InputOption::VALUE_REQUIRED,
                'Insert Shipping provider. Allowed shipment providers are : ' . implode(
                    ',',
                    ShippingProvider::getAllValues()
                ),
                'none'
            )
            ->addOption(
                'order',
                'o',
                InputOption::VALUE_REQUIRED,
                'Insert Order json data in single quotes with proper fields.'
            )
            ->setHelp(
                'This command allows you to register a parcel(shipment) related to the provided Order.Allowed providers can be found under the proper field description.Example in a Usage'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $violations = $this->cliValidator->validateInput($input);
        if (!empty($violations)) {
            $io->error($violations);
            return Command::FAILURE;
        }
        $shippingProvider = strtolower($input->getOption('shipping-provider'));
        $order = $input->getOption('order');
        try {
            $shipmentServiceProvider = $this->shippingServiceFactory->create($shippingProvider);
            $this->shipmentService->process($order, $shipmentServiceProvider);
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        $io->success('Shipment has been registered successfully');
        return Command::SUCCESS;
    }
}
