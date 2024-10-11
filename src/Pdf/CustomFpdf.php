<?php

namespace Bmax\LaravelPdfGenerator\Pdf;

use Fpdf\Fpdf;

class CustomFpdf extends Fpdf
{
    public function Ellipse($x, $y, $rx, $ry, $style = 'D')
    {
        if ($style == 'F') {
            $op = 'f';  
        } elseif ($style == 'FD' || $style == 'DF') {
            $op = 'B';  
        } else {
            $op = 'S';  
        }
    
        $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
        $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;
    
        $this->_out(sprintf('%.2F %.2F m', ($x + $rx) * $this->k, ($this->h - $y) * $this->k));
    
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', ($x + $rx) * $this->k, ($this->h - ($y - $ly)) * $this->k, ($x + $lx) * $this->k, ($this->h - ($y - $ry)) * $this->k, $x * $this->k, ($this->h - ($y - $ry)) * $this->k));
    
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', ($x - $lx) * $this->k, ($this->h - ($y - $ry)) * $this->k, ($x - $rx) * $this->k, ($this->h - ($y - $ly)) * $this->k, ($x - $rx) * $this->k, ($this->h - $y) * $this->k));
    
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', ($x - $rx) * $this->k, ($this->h - ($y + $ly)) * $this->k, ($x - $lx) * $this->k, ($this->h - ($y + $ry)) * $this->k, $x * $this->k, ($this->h - ($y + $ry)) * $this->k));
    
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s', ($x + $lx) * $this->k, ($this->h - ($y + $ry)) * $this->k, ($x + $rx) * $this->k, ($this->h - ($y + $ly)) * $this->k, ($x + $rx) * $this->k, ($this->h - $y) * $this->k, $op));
    }

    public function SetDash($black = null, $white = null)
    {
        if ($black !== null) {
            $s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
        } else {
            $s = '[] 0 d';
        }
        $this->_out($s);
    }
}
