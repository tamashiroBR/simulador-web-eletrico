<?php
/*
 * Copyright (C) 2016 Márcio A. Tamashiro
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace NDSE\Tools;

use NDSE\Math\Angle;
use NDSE\Math\Complex;
use NDSE\Math\Matrix;
use NDSE\Math\LinAlg;
use NDSE\Models\Gen\Gen1;
use NDSE\Models\Gen\Gen2;
use NDSE\Models\Exc\Exc1;

/**
 * Defines TransientAnalysisAbstract
 *
 * @author Márcio A. Tamashiro
 */
class TransientAnalysis extends AbstractTools
{
    /**
     * 
     */
    protected $option = ['freq0' => 60, 
                         'starttime' => 0,        
                         'stoptime' => 1,
                         'stepsize' => 1e-3
                        ];

    /**
     * 
     */
    //protected $lf = [];
    
    /**
     * Constructs
     */
    public function __construct($data)
    {
        $this->data = $data;

        $idx = array_keys($this->option);
        foreach ($this->data['optTA'] as $k => $v) {
            $this->option[$idx[$k]] = $v;
        }
        
        //$this->lf = new LoadFlow($data);
    }

    /**
     * Defines
     */
    public function getNmodel($name,$model)
    {
        $name = strtolower($name);
        
        if (is_numeric($model) && $model >= 0) {
            $name = strtoupper($name);

            $gen = $this->getData('gen');
            $gen = new Matrix($gen);

            if ($name == 'gen') {
                switch ($model) {
                    case 1:	$gen_model = $gen->subMatrix([0,'==',1],[])->get();
                                break;
                    case 2:	$gen_model = $gen->subMatrix([0,'==',2],[])->get();
                                break;
                   default:	break;
                }
                return count($gen_model);
            }

            if ($name == 'exc') {
                switch ($model) {
                    case 1:	$exc_model = $gen->subMatrix([1,'==',1],[])->get();
                                break;
                    case 2:	$exc_model = $gen->subMatrix([1,'==',2],[])->get();
                                break;
                   default:	break;
                }
                return count($exc_model);
            }

            if ($name == 'gov') {
                switch ($model) {
                    case 1:	$gov_model = $gen->subMatrix([2,'==',1],[])->get();
                                break;
                    case 2:	$gov_model = $gen->subMatrix([2,'==',2],[])->get();
                                break;
                   default:	break;
                }
                return count($gov_model);
            }
        }
    }

    /**
     * Defines
     */
    public function makeYaug($U,$lf,$xd_tr)
    {
        $Y = $lf->getYbus();

        $Sbase = $lf->getOption('sbase');

        $ngen = $lf->getNtype('pv') + 1;
        $nbus = $lf->getN('bus');

        $bus = new Matrix($lf->getData('bus'));

        $type = $bus->transpose()->get(1,[]);

        $P = new Matrix($bus->get([],4));
        $P = $P->multiply(1/$Sbase);
      
        foreach ($P->get() as $i => $col) {
            $Pl[$i] = $col[0];
        }

        $Q = new Matrix($bus->get([],5));
        $Q = $Q->multiply(1/$Sbase);

        foreach ($Q->get() as $i => $col) {
            $Ql[$i] = $col[0];
        }

    // Start augmented bus admittance matrix
        // Calculate equivalent load admittance
        for ($i = 0; $i < $nbus; $i++) {
            $Sl = new Complex($Pl[$i],-$Ql[$i]);
            $yload = $Sl->div(pow($U[$i]->abs(),2));
            if (!is_null($Y->get($i,$i))) {
                $value = $Y->get($i,$i);
                $value = $value->add($yload);
            } else {
                $value = $yload;
            }
            $Y->set($value,$i,$i);
        }

        // Calculate equivalent generator admittance
        for ($i = 0; $i < $ngen; $i++) {
            $xdtr =  new Complex(0,$xd_tr[$i]);
            $ygen  = $xdtr->inv();
            if (!is_null($Y->get($i,$i))) {
                $value = $Y->get($i,$i);
                $value = $value->add($ygen);
            } else {
                $value = $ygen;
            }
            $Y->set($value,$i,$i);	
        }
    // End augmented bus admittance matrix     
      
        return $Y;
    }

    /**
     * Defines
     */
    public function run()
    {
        //$lf = $this->lf;
        $lf = new LoadFlow($this->data);
        $lf->makeYbus();

        $resLF = json_decode($lf->run()); 
//$resLF = $lf->run();
        $resLF = new Matrix($resLF->bus);
        $resLF = $resLF->transpose();
        $resLF_Umag = $resLF->get(1,[]);
        $resLF_Uang = $resLF->get(2,[]);
        $resLF_P = $resLF->get(3,[]);
        $resLF_Q = $resLF->get(4,[]);

        $nbus = $lf->getN('bus');

        $event = new Matrix($this->getData('event'));

        $starttime = $this->getOption('startTime');
        $stoptime = $this->getOption('stopTime');
        $stepsize = $this->getOption('stepSize');

        $gen = new Matrix($this->getData('gen'));
        $ngen = $gen->getN('rows');
        $genbus = $gen->transpose()->get(0,[]);
        $genmodel = $gen->transpose()->get(1,[]);

        $exc_on = 0;
        $gov_on = 0;

        if ($this->getData('exc') != null) {
            $exc = new Matrix($this->getData('exc'));
            $nexc = $exc->getN('rows');
            $excbus = $exc->transpose()->get(0,[]);
            $excmodel = $exc->transpose()->get(1,[]);
            $exc_on = 1;
        }

       if ($this->getData('gov') != null) {
            $gov = new Matrix($this->getData('gov'));
            $ngov = $gov->getN('rows');
            $govbus = $gov->transpose()->get(0,[]);
            $govmodel = $gov->get([],1);
            $gov_on = 1;
        }

        $bus = $lf->getData('bus');
        $branch = $lf->getData('branch');

        $nbranch = $lf->getN('branch');

        $U =[];
        for ($i = 0; $i < $nbus; $i++) {
            $U[] = new Complex($resLF_Umag[$i],new Angle($resLF_Uang[$i],'deg'));
        }

        $S = [];
        for ($i = 0; $i < $nbus; $i++) {
            $S[] = new Complex($resLF_P[$i],$resLF_Q[$i]);
        }

        $U00 = $U;
        $U0 = $U;

        // Construct augmented Ybus
        $xd_tr = Matrix::zeros(1,$ngen);
        for ($i = 0; $i < $ngen; $i++) {
            if (($gen->get($i,1)) == 2) {
               $xd_tr->set($gen->get($i,6),0,$i);
            } elseif (($gen->get($i,1)) == 1) {
               $xd_tr->set($gen->get($i,5),0,$i);
            }
        }
        $xd_tr = $xd_tr->get();

        $Yaug = $this->makeYaug($U0,$lf,$xd_tr);
        $LU = LinAlg::LUdecomp($Yaug);
        
    // Calculate Initial machine state
        foreach ($genmodel as $i => $m) {
            $id = $genbus[$i]-1;
            $Pg0 = $S[$id]->re/$lf->getOption('sbase');
            $Qg0 = $S[$id]->img/$lf->getOption('sbase');
            $data_gen = [[$this->getOption('freq0'),$U0[$id],$Pg0,$Qg0],$gen->get($i,[])];

            switch ($m) {
                // Generator type 1: classical model
                 case 1: $gg[] = new Gen1($data_gen);
                         break;
                // Generator type 2: 4th order model
                 case 2: $gg[] = new Gen2($data_gen);
                         break;
                default: break;
            }
            // Generator Init
            $Xgen0[] = $gg[$i]->x0();
            $Ggen0[] = $gg[$i]->Gy($U0[$id]);

            $Pm[] = $Ggen0[$i][0];
            $Pe[] = $Ggen0[$i][0];
        }

        $Xgen0 = new Matrix($Xgen0);
        $omega = $Xgen0->transpose()->get(1,[]);
    // End calculate Initial machine state //

    // Exciter initial conditions
        if ($exc_on)
        {
            foreach ($excmodel as $i => $m) {
                $id = $excbus[$i]-1;
                $data_exc = [[$U0[$id]->abs(),$gg[$id]->get('Efd')],$exc->get($i,[])];

                switch ($m) {
                    // Exciter type 1
                     case 1: $ex[] = new Exc1($data_exc);
                             break;
                    default: break;
                }
                // Exciter Init
                $Xexc0[] = $ex[$i]->x0();
//                $Efd[] = $Xexc0[$i][0];
            }
        }
        $Xexc0 = new Matrix($Xexc0);

        // Initialization of main stability loop
        $t = $starttime;
        $ev = 1;
        $eventhappened = 0;

//        $ii = 0;
        // Main stability loop
        while ($t < ($stoptime + $stepsize)) {
            // Output
//            $ii=$ii+1;

            // Numerical Method //
            // First Euler step
            $dXexc0 = [];
            foreach ($excmodel as $i => $m) {
                $id = $excbus[$i]-1;
                $in = [$U0[$id]->abs(),$Xexc0->transpose()->get(0,$i),$Xexc0->transpose()->get(1,$i),$Xexc0->transpose()->get(2,$i)];
                $dXexc0[] = $ex[$i]->dFx($in);
            }

            $dXexc0 = new Matrix($dXexc0);
            $Xexc1 = $Xexc0->add($dXexc0->multiply($stepsize));

            $dXgen0 = [];
            $idex = 0;
            foreach ($genmodel as $i => $m) {
                $id = $genbus[$i]-1;
                $in = [$Pm[$id],$omega[$id],$Pe[$id]];
                if ($m == 2) {
                    $gg[$i]->set('Efd',$Xexc1->transpose()->get(0,$idex));
                    $idex++;
                }
                $dXgen0[] = $gg[$i]->dFx($in);
            }

            $dXgen0 = new Matrix($dXgen0);
            $Xgen1 = $Xgen0->add($dXgen0->multiply($stepsize));

            $omega = $Xgen1->transpose()->get(1,[]);

            // Calculate network voltages: U = Y/Ig
            $Ggen0 = [];
            for ($i = 0; $i < $nbranch; $i++) {
                $Ig[$i] = 0;
            }
            foreach ($genmodel as $i => $m) {
                $id = $genbus[$i]-1;

                $gg[$i]->set('delta',$Xgen1->transpose()->get(0,$i));
                $gg[$i]->set('Eq_tr',$Xgen1->transpose()->get(2,$i));
                $gg[$i]->set('Ed_tr',$Xgen1->transpose()->get(3,$i));
                $Ggen0[] = $gg[$i]->Gy($U0[$id]);
                $Ig[$id] = $Ggen0[$i][1];
            }
            $U1 = LinAlg::LUsolver($LU,$Ig);

            $Ggen1 = [];
            $Pe = [];
            foreach ($genmodel as $i => $m) {
                $id = $genbus[$i]-1;
                $Ggen1[] = $gg[$i]->Gy($U1[$id]);
                $Pe[] = $Ggen1[$i][0];
            }

            // Second Euler step//
            $dXexc1 = [];
            foreach ($excmodel as $i => $m) {
                $id = $excbus[$i]-1;
                $in = [$U1[$id]->abs(),$Xexc1->transpose()->get(0,$i),$Xexc1->transpose()->get(1,$i),$Xexc1->transpose()->get(2,$i)];
                $dXexc1[] = $ex[$i]->dFx($in);
            }

            $dXexc1 = new Matrix($dXexc1);
            $dXexc = $dXexc0->add($dXexc1);
            $Xexc2 = $Xexc0->add($dXexc->multiply($stepsize/2));

            $dXgen1 = [];
            $idex = 0;
            foreach ($genmodel as $i => $m) {
                $id = $genbus[$i]-1;
                $in = [$Pm[$id],$omega[$id],$Pe[$id]];
                if ($m == 2) {
                    $gg[$i]->set('Efd',$Xexc2->transpose()->get(0,$idex));
                    $idex++;
                }
                $dXgen1[] = $gg[$i]->dFx($in);
            }

            $dXgen1 = new Matrix($dXgen1);
            $dXgen = $dXgen0->add($dXgen1);
            $Xgen2 = $Xgen0->add($dXgen->multiply($stepsize/2));

            $omega = $Xgen2->transpose()->get(1,[]);

            // Calculate network voltages: U = Y/Ig
            $Ggen1 = [];
            for ($i = 0; $i < $nbranch; $i++) {
                $Ig[$i] = 0;
            }
            foreach ($genmodel as $i => $m) {
                $id = $genbus[$i]-1;

                $gg[$i]->set('delta',$Xgen2->transpose()->get(0,$i));
                $gg[$i]->set('Eq_tr',$Xgen2->transpose()->get(2,$i));
                $gg[$i]->set('Ed_tr',$Xgen2->transpose()->get(3,$i));
                $Ggen1[] = $gg[$i]->Gy($U1[$id]);
                $Ig[$id] = $Ggen1[$i][1];
            }
            $U2 = LinAlg::LUsolver($LU,$Ig);

            $Ggen2 = [];
            $Pe = [];
            foreach ($genmodel as $i => $m) {
                $id = $genbus[$i]-1;
                $Ggen2[] = $gg[$i]->Gy($U2[$id]);
                $Pe[] = $Ggen2[$i][0];
            }

            $U0 = $U2;
            $Ggen0 = $Ggen2;
            $Xgen0 = $Xgen2;
            $Xexc0 = $Xexc2;

            // Save values
            $Time[] = $t;
            $Ubus[] = $U0;
            // gen
            $Angles[] = $Xgen0->transpose()->get(0,[]);
            $Speeds[] = $Xgen0->transpose()->get(1,[]);
            $Pmec[] = $Pm;
            $Efd[] = [0,0,$Xexc0->transpose()->get(0,0)];

            // Check for events
            if (!is_null($event->get()) && $ev <= ($event->getN('rows'))) {
                $eventhappened = FALSE;
                if (abs($t-($event->get($ev-1,3))) < (10*pow(2,-26)))
                {
                    $eventhappened = TRUE;

                    switch ($event->get($ev-1,0)) {
                         case 1: $bus[$event->get($ev-1,1)-1][$event->get($ev-1,2)] = $event->get($ev-1,4);
                                 break;
                         case 2: $branch[$event->get($ev-1,1)-1][$event->get($ev-1,2)] = $event->get($ev-1,4);
                                 break;
                        default: break;
                    }
                    $ev = $ev + 1;
                }

                if ($eventhappened) {
//echo "evento ocorreu\n";
                    // Refactorise
                    $lf->setData('bus',$bus);
                    $lf->setData('branch',$branch);
                    $lf->makeYbus();
                    $Yaug = $this->makeYaug($U00,$lf,$xd_tr);

                    // Calculate network voltages: U = Y/Ig
                    $Ggen0 = [];
                    for ($i = 0; $i < $nbranch; $i++) {
                        $Ig[$i] = 0;
                    }
                    foreach ($genmodel as $i => $m) {
                        $id = $genbus[$i]-1;

                        $gg[$i]->set('delta',$Xgen0->transpose()->get(0,$i));
                        $gg[$i]->set('Eq_tr',$Xgen0->transpose()->get(2,$i));
                        $gg[$i]->set('Ed_tr',$Xgen0->transpose()->get(3,$i));
                        $Ggen0[] = $gg[$i]->Gy($U0[$id]);
                        $Ig[$id] = $Ggen0[$i][1];
                    }
                    $LU = LinAlg::LUdecomp($Yaug);
                    $U0 = LinAlg::LUsolver($LU,$Ig);

                    $Ggen0 = [];
                    $Pe = [];
                    foreach ($genmodel as $i => $m) {
                        $id = $genbus[$i]-1;
                        $Ggen0[] = $gg[$i]->Gy($U0[$id]);
                        $Pe[] = $Ggen0[$i][0];
                    }

//$ii=$ii+1; // if event occurs, save values at t- and t+

                    // Save values
                    // time
                    $Time[] =  $t;
                    $Ubus[] = $U0;
                    // gen
                    $Angles[] =  $Xgen0->transpose()->get(0,[]);
                    $Speeds[] = $Xgen0->transpose()->get(1,[]);
                    $Pmec[] = $Pm;
                    $Efd[] = [0,0,$Xexc0->transpose()->get(0,0)];
                }
            }

            // Advance time
            $t = $t + $stepsize;
        } // end of main stability loop

        for ($i = 0; $i < count($Angles); $i++) {
            for ($j = 0; $j < $ngen; $j++) {
                $y1[$i][$j] = $Angles[$i][$j]*180/M_PI;
            }
        }

        for ($i = 0; $i < count($Ubus); $i++) {
            for ($j = 0; $j < $ngen; $j++) {
                $y2[$i][$j] = $Ubus[$i][$j]->abs();
            }
        }

        for ($i = 0; $i < count($Speeds); $i++) {
            for ($j = 0; $j < $ngen; $j++) {
                $y3[$i][$j] = $Speeds[$i][$j]/(2*M_PI*60);
            }
        }

        for ($i = 0; $i < count($Speeds); $i++) {
            for ($j = 0; $j < $ngen; $j++) {
                $y4[$i][$j] = $Pmec[0][$j];
            }
        }

        for ($i = 0; $i < count($Speeds); $i++) {
            for ($j = 0; $j < $ngen; $j++) {
                $y5[$i][$j] = $Efd[$i][$j];
            }
        }

        $res = ['t'=>$Time,'delta'=>$y1,'volt'=>$y2,'omega'=>$y3,'pmec'=>$y4,'efd'=>$y5];

        $json = json_encode($res);
        return $json;
        //return $res;
    }
}