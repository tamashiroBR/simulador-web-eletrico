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

use NDSE\Math\Sparse;

/**
 * Defines LoadFlow Class
 *
 * @author Márcio A. Tamashiro
 */
final class LoadFlowT extends LoadFlow
{
    /**
     * Constructs
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Defines
     */
    public function makeJ($input)
    {
        $nt = $input[0];
        $JACOB = new Sparse($nt,$nt);

        //make threads
        $H = new makeH($input);
        $L = new makeL($input);
        $M = new makeM($input);
        $N = new makeN($input);

        //start threads
        $H->start();
        $L->start();
        $M->start();
        $N->start();

        //wait for both threads
        $H->join();
        $L->join();
        $M->join();
        $N->join();

        $JACOB = $H->get()->add($L->get()->add($M->get()->add($N->get())));

        return $JACOB;
    }
}