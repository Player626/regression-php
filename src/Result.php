<?php


namespace Stitch\Regression;


class Result
{
    /** @var array $points */
    private $points;

    /** @var callable $predict */
    private $predict;

    /** @var array $equation */
    private $equation;

    /** @var float|int */
    private $r2;

    /** @var string $formula */
    private $formula;

    /**
     * @return array
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * @param array $points
     */
    public function setPoints(array $points): void
    {
        $this->points = $points;
    }

    /**
     * @return callable
     */
    public function getPredict(): callable
    {
        return $this->predict;
    }

    /**
     * @param callable $predict
     */
    public function setPredict(callable $predict): void
    {
        $this->predict = $predict;
    }

    /**
     * @return array
     */
    public function getEquation(): array
    {
        return $this->equation;
    }

    /**
     * @param array $equation
     */
    public function setEquation(array $equation): void
    {
        $this->equation = $equation;
    }


    /**
     * @return float|int
     */
    public function getR2()
    {
        return $this->r2;
    }

    /**
     * @param float|int $r2
     */
    public function setR2($r2): void
    {
        $this->r2 = $r2;
    }

    /**
     * @return string
     */
    public function getFormula(): string
    {
        return $this->formula;
    }

    /**
     * @param string $formula
     */
    public function setFormula(string $formula): void
    {
        $this->formula = $formula;
    }
}