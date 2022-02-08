<?php

namespace Stitch\Regression;

class Regression
{
    /**
     * 将输入数据与方程式拟合成一条直线 y=mx+c
     * @param array $data
     * @param int|null $precision
     * @return Result
     */
    public function linear(array $data, ?int $precision = 10)
    {
        $sum = [0, 0, 0, 0, 0];
        $len = 0;
        foreach ($data as $v) {
            if (!isset($v[1])) {
                continue;
            }

            $len++;
            $sum[0] += $v[0];
            $sum[1] += $v[1];
            $sum[2] += $v[0] * $v[0];
            $sum[3] += $v[0] * $v[1];
            $sum[4] += $v[1] * $v[1];
        }

        $run = $len * $sum[2] - $sum[0] * $sum[0];
        $rise = $len * $sum[3] - $sum[0] * $sum[1];
        $gradient = $run == 0 ? 0 : $this->round($rise / $run, $precision);
        $intercept = $this->round($sum[1] / $len - $gradient * $sum[0] / $len, $precision);

        $predict = function ($x) use ($precision, $gradient, $intercept) {
            return [$this->round($x, $precision), $this->round($gradient * $x + $intercept, $precision)];
        };

        $points = array_map(function ($point) use ($predict) {
            return call_user_func($predict, $point[0]);
        }, $data);

        $r2 = $this->round($this->determinationCoefficient($data, $points), $precision);
        $formula = $intercept === 0 ? "y = {$gradient}x" : "y = {$gradient} x + {$intercept}";
        return $this->buildResult($points, $predict, [$gradient, $intercept], $r2, $formula);
    }

    /**
     * 将输入数据拟合成对数曲线 y=a+bln(x)
     * @param array $data
     * @param int|null $precision
     * @return Result
     */
    public function logarithmic(array $data, ?int $precision = 10)
    {
        $sum = [0, 0, 0, 0];

        $len = count($data);

        foreach ($data as $v) {
            if (!isset($v[1])) {
                continue;
            }

            $sum[0] += log($v[0]);
            $sum[1] += $v[1] * log($v[0]);
            $sum[2] += $v[1];
            $sum[3] += pow(log($v[0]), 2);
        }

        $a = ($len * $sum[1] - $sum[2] * $sum[0]) / ($len * $sum[3] - $sum[0] * $sum[0]);
        $coeff_b = $this->round($a, $precision);
        $coeff_a = $this->round(($sum[2] - $coeff_b * $sum[0]) / $len, $precision);

        $predict = function ($x) use ($precision, $coeff_b, $coeff_a) {
            return [$this->round($x, $precision), $this->round($this->round($coeff_a + $coeff_b * log($x), $precision), $precision)];
        };

        $points = array_map(function ($point) use ($predict) {
            return call_user_func($predict, $point[0]);
        }, $data);

        $r2 = $this->round($this->determinationCoefficient($data, $points), $precision);
        $formula = "y = {$coeff_a} + {$coeff_b} ln(x)";
        return $this->buildResult($points, $predict, [$coeff_a, $coeff_b], $r2, $formula);
    }

    private function array_reduce(array $data, callable $func, $initial = null)
    {
        $last_v = $initial;
        foreach ($data as $idx => $v) {
            $last_v = call_user_func($func, $last_v, $v, $idx);
        }

        return $last_v;
    }

    private function round($number, int $precision)
    {
        $factor = pow(10, $precision);
        return round($number * $factor) / $factor;
    }

    private function determinationCoefficient($data, $results)
    {
        $predictions = [];
        $observations = [];

        foreach ($data as $idx => $v) {
            if (!isset($v[1])) {
                continue;
            }
            array_push($observations, $v);
            array_push($predictions, $results[$idx]);
        }

        $sum = $this->array_reduce($observations, function ($a, $observation) {
            return $a + $observation[1];
        }, 0);

        $mean = $sum / count($observations);

        $ssyy = $this->array_reduce($observations, function ($a, $observation) use ($mean) {
            $difference = $observation[1] - $mean;
            return $a + $difference * $difference;
        }, 0);


        $see = $this->array_reduce($observations, function ($accum, $observation, $index) use ($predictions) {
            $prediction = $predictions[$index];
            $residual = $observation[1] - $prediction[1];
            return $accum + $residual * $residual;
        }, 0);

        return 1 - $see / $ssyy;
    }

    private function buildResult(array $points, callable $predict, array $equation, $r2, string $formula)
    {
        $result = new Result();
        $result->setPoints($points);
        $result->setPredict($predict);
        $result->setEquation($equation);
        $result->setR2($r2);
        $result->setFormula($formula);
        return $result;
    }
}