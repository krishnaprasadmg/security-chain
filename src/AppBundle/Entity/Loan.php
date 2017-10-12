<?php

namespace AppBundle\Entity;

class Loan implements \JsonSerializable
{
    private $isin;

    private $maturity;

    private $nominalAmount;

    private $interestRate;

    private $borrower;

    private $grade;

    /**
     * @return mixed
     */
    public function getIsin()
    {
        return $this->isin;
    }

    /**
     * @param mixed $isin
     */
    public function setIsin($isin)
    {
        $this->isin = $isin;
    }

    /**
     * @return mixed
     */
    public function getMaturity()
    {
        return $this->maturity;
    }

    /**
     * @param mixed $maturity
     */
    public function setMaturity($maturity)
    {
        $this->maturity = $maturity;
    }

    /**
     * @return mixed
     */
    public function getNominalAmount()
    {
        return $this->nominalAmount;
    }

    /**
     * @param mixed $nominalAmount
     */
    public function setNominalAmount($nominalAmount)
    {
        $this->nominalAmount = $nominalAmount;
    }

    /**
     * @return mixed
     */
    public function getInterestRate()
    {
        return $this->interestRate;
    }

    /**
     * @param mixed $interestRate
     */
    public function setInterestRate($interestRate)
    {
        $this->interestRate = $interestRate;
    }

    /**
     * @return mixed
     */
    public function getBorrower()
    {
        return $this->borrower;
    }

    /**
     * @param mixed $borrower
     */
    public function setBorrower($borrower)
    {
        $this->borrower = $borrower;
    }

    /**
     * @return mixed
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param mixed $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'loan' => [
                'isin' => $this->isin,
                'maturity' => $this->maturity,
                'nominal_amount' => ['amount' => $this->nominalAmount, 'currency' => 'EUR'],
                'interest_rate' => $this->interestRate,
                'borrower' => $this->borrower,
                'score' => ['grade' => $this->grade],
                'payments' => [],
                'amortization_table' => []
            ]
        ];
    }
}