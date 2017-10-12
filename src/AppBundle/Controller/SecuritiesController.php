<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Loan;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class SecuritiesController extends Controller
{
    /**
     * @Route("/security/create", name="security_create")
     */
    public function indexAction(Request $request)
    {
        $loan = new Loan();

        $form = $this->createFormBuilder($loan)
            ->add('isin', TextType::class)
            ->add('maturity', NumberType::class)
            ->add('nominalAmount', NumberType::class, ['label' => 'Nominal Amount (EUR cents)'])
            ->add('interestRate', NumberType::class)
            ->add('borrower', TextType::class)
            ->add('grade', ChoiceType::class, ['choices' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']])
            ->add('submit', SubmitType::class, [
                'label' => 'Create Security',
                'attr'  => ['class' => 'btn btn-info'],
            ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loan = $form->getData();
            $this->createLoanSecurity($loan);
        }

        return $this->render('AppBundle::default_form.html.twig', ['title' => 'Create loan security', 'form' => $form->createView()]);
    }

    private function createLoanSecurity(Loan $loan)
    {
        $metadata = [
            'isin' => $loan->getIsin(),
        ];

        $response = $this->createTransaction($metadata, $loan);

        dump($response->getBody()->getContents());
        exit;
    }

    private function createTransaction($metadata, $asset)
    {
        $signers = $this->container->getParameter('bigchaindb_public_key');

        $body = [
            'id' => null,
            'version' => '1.0',
            'operation' => 'CREATE',
            'metadata' => $metadata,
            'asset' => [
                'data' => $asset,
            ],
            'inputs' => [
                'fulfillment' => null,
                'fulfills' => null,
                'owners_before' => [$signers],
            ],
            'outputs' => [
                'amount' => '1',
                'public_keys' => [$signers],
                'condition' => [
                    'details' => [
                        'public_key' => $signers,
                        'type' => 'ed25519-sha-256',
                    ],
                    'uri' => '',
                ]
            ],
        ];

        $body['id'] = hash('sha256', json_encode($body));

        $client = new Client(['base_uri' => 'http://bigchaindb:9984', 'timeout' => 2.0]);

        $response = $client->post('/api/v1/transactions', ['body' => json_encode($body)]);

        return $response;
    }
}