<?php

namespace App\Command;

use App\Entity\ShippingProvider;
use App\Service\Order as OrderService;
use App\Factory\ShippingProviderFactory;
use App\Service\Shipment;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

#[AsCommand(
    name: 'app:order:shipment:register',
    description: 'Register order shipment',
)]
class ShippingParcelRegisterCommand extends Command
{
    public function __construct(private readonly Shipment $shipmentService, private readonly OrderService $orderService, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('shipping-provider', 'p', InputOption::VALUE_REQUIRED, 'Shipping provider')
            ->addOption('order', 'o', InputOption::VALUE_REQUIRED, 'Insert proper json data in single quotes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $violations = $this->validateInput($input);
        if (!empty($violations)) {
            $io->error($violations);
            return Command::FAILURE;
        }
        $shippingProvider = strtolower($input->getOption('shipping-provider'));
        $order = $input->getOption('order');
        try {
            $shipmentServiceProvider = ShippingProviderFactory::create($shippingProvider);
            $orderShipment = $this->orderService->createOrderShipment($order, $shipmentServiceProvider);
            $register = $this->shipmentService->register($orderShipment, $shipmentServiceProvider);
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        $io->success('Shipment has been registered successfully');
        return Command::SUCCESS;
    }

    /**
     * Validation of both params :
     * 1. shippimg-provider - checks supported providers
     * 2. order - checks if is json, if order_id is not empty and integer
     * @param InputInterface $input
     * @return array
     */
    public function validateInput(InputInterface $input): array
    {
        $result = [];
        $validator = Validation::createValidator();
        $violations = $validator->validate(strtolower($input->getOption('shipping-provider')), [
            new Choice(['callback' => [ShippingProvider::class, 'getAllEnabled']])
        ]);
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $result[] = $violation->getInvalidValue() . ': ' . $violation->getMessage();
            }
        }
        $orderData = $input->getOption('order');
        $orderDataViolations = $validator->validate($orderData, [
            new Json()
        ]);

        if (0 !== count($orderDataViolations)) {
            foreach ($orderDataViolations as $violation) {
                $result[] = $violation->getInvalidValue() . ': ' . $violation->getMessage();
            }
        } else {
            $orderData = json_decode($orderData, true);
            $constraint = new Assert\Collection([
                'order_id' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'integer'])
                ]
            ]);
            $orderJsonViolations = $validator->validate($orderData, $constraint);
            if (0 !== count($orderJsonViolations)) {
                foreach ($orderJsonViolations as $violation) {
                    $result[] = 'order_id: ' . $violation->getMessage();
                }
            }
        }

        return $result;
    }
}
