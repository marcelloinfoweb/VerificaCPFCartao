<?php
/**
 * Copyright © Marcelo Caetano All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Funarbe\VerificaCPFCartao\Block\Adminhtml;

class VerificaCpfCartao extends \Magento\Backend\Block\Template
{
    private \Magento\Sales\Api\OrderRepositoryInterface $orderRepository;

    /**
     * Constructor
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function verificaCpfCartao(): string
    {
        $orderId = $this->getRequest()->getParam('order_id');

        $order = $this->orderRepository->get($orderId);
        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        $cpfCliente = $order->getCustomerTaxvat();
        $nomeCliente = $order->getCustomerFirstname();

        if (!array_key_exists('mestremagecc_cpf', $additionalInformation)) {
            return '';
        }

        $cpfCartao = $additionalInformation['mestremagecc_cpf'];
        $nomeFullCartao = $additionalInformation['cielo_credit_name'];

        $nome = explode(' ', $nomeFullCartao);
        $nomeCartao = $nome['0'];

        if ($cpfCartao === $cpfCliente) {
            $resultadoCpf = '<span style=\'color: darkgreen\'> Confere</span>';
        } else {
            $resultadoCpf = '<span style=\'color: darkred\'> Não Confere</span>';
        }
        if ($nomeCartao === $nomeCliente) {
            $resultadoNome = '<span style=\'color: darkgreen\'> Confere</span>';
        } else {
            $resultadoNome = '<span style=\'color: darkred\'> Não Confere</span>';
        }

        return "<section class='admin__page-section'>
                <div class='admin__page-section-title'>
                    <span class='title'>Validação do cartão</span>
                </div>
                <div class='admin__page-section-content'>
                    <div class='admin__page-section-item-content'>
                        <strong>CPF:</strong> $resultadoCpf | <strong>Primeiro Nome:</strong> $resultadoNome
                    </div>
                </div>
            </section>";
    }
}

