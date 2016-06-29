<?php

function ft_isempty($var)
{
    return($var !== "");
}

function    ft_isok($str)
{
	$patern = "#^ *[-+]? *([\d]+|[\d]+\.[\d]+) *(\* *([Xx]\^)?[\d]+)?( *([-+])? *(?(5) *([\d]+|[\d]+\.[\d]+) *(\* *([Xx]\^)?[\d]+)? *))+$#i";
	if (!preg_match($patern, $str))
		return (FALSE);
	return (TRUE);
}

function	ft_parse($str)
{
	$equ = array_values(array_filter(preg_split("#[ *Xx^]#i", $str), ft_isempty));
	if ($equ[0][0] !== "-" && $equ[0][0] !== "+")
		array_unshift($equ, "+");
	$i = -1;
	$j = 0;
	foreach ($equ as $val)
	{
		if (++$i > 2)
		{
			$i = 0;
			++$j;
		}
		$tab[$j][$i] = $val;
	}
	return ($tab);
}

function    ft_degree($tab)
{
	$n = $tab[0][2];
	foreach ($tab as $val)
	{
		if ($n < $val[2])
			$n = $val[2];
	}
	return ($n);
}

function    ft_reduce1($tab)
{
	$new = array();
	foreach ($tab as $key => $val)
	{
		if (!empty($new) && in_array($val[2], array_column($new, 2)))
		{
			$k = array_search($val[2], array_column($new, 2));
			if ($val[0] === $new[$k][0])
				$new[$k][1] += $val[1];
			else
				$new[$k][1] -= $val[1];
			if ($new[$k][1] < 0)
			{
				if ($new[$k][0] === "-")
					$new[$k][0] = "+";
				else
					$new[$k][0] = "-";
				$tab[$k][1] = -$tab[$k][1];
			}
		}
		else if ($val[1])
			$new[] = [ $val[0], $val[1], $val[2] ];
	}
	return ($new);
}

function    ft_reduce2($tab1, $tab2)
{
	$new = array();
	foreach ($tab2 as $key => $val)
	{
		if (!empty($tab1) && in_array($val[2], array_column($tab1, 2)))
		{
			$k = array_search($val[2], array_column($tab1, 2));
			if ($val[0] !== $tab1[$k][0])
				$tab1[$k][1] += $val[1];
			else
				$tab1[$k][1] -= $val[1];
			if ($tab1[$k][1] < 0)
			{
				if ($tab1[$k][0] === "-")
					$tab1[$k][0] = "+";
				else
					$tab1[$k][0] = "-";
				$tab1[$k][1] = -$tab1[$k][1];
			}
		}
		else if ($val[1])
			$tab1[] = [ ($val[0] === '+') ? '-' : '+', $val[1], $val[2] ];
	}
	return ($tab1);
}

function    ft_reduce3($tab)
{
	$new = array();
	foreach ($tab as $key => $val)
	{
		if ($val[1])
			$new[] = $val;
	}
	return ($new);
}

function    ft_print_reduce($tab)
{
	echo "Reduced form: \033[33m";
	if (empty($tab))
		echo "0";
	foreach ($tab as $key => $val)
	{
		if ($val[0] == "-" && !$key)
			echo "- ";
		else if ($key)
			echo " ", $val[0], " ";
		if ($val[1] == 1 && $val[2] > 1)
			echo "X^", $val[2];
		else
		{
			echo $val[1];
			if ($val[2] == 1)
				echo " * X";
			else if ($val[2] > 0)
				echo " * ", "X^", $val[2];
		}
	}
	echo " = 0\n\033[0m";
}

function 	ft_sqrt($val)
{
	$val = (float)$val;
	for ($i = 0; $i * $i <= $val; ++$i);
	--$i;
	$d = $val - $i * $i;
	$p = $d / (2 * $i);
	$a = $i + $p;
	return ($a - ($p * $p) / (2 * $a));
}

function 	ft_solve($tab, $degree)
{
	if ($degree == 1)
	{
		$key = array_search(0, array_column($tab, 2));
		$x0 = ($tab[$key][0] == '+') ? -$tab[$key][1] : $tab[$key][1];
		$key = array_search(1, array_column($tab, 2));
		$x1 = $tab[$key][1];
		$x0 /= ($tab[$key][0] == "+") ? $x1 : -$x1;
		echo "The solution is:\n\033[33m", $x0, "\n\033[0m";
	}
	else
	{
		$a = 0;
		$b = 0;
		$c = 0;
		if (($key = array_search(0, array_column($tab, 2))) !== false)
			$c = ($tab[$key][0] == '-') ? -$tab[$key][1] : $tab[$key][1];
		if (($key = array_search(1, array_column($tab, 2))) !== false)
			$b = ($tab[$key][0] == '-') ? -$tab[$key][1] : $tab[$key][1];
		if (($key = array_search(2, array_column($tab, 2))) !== false)
			$a = ($tab[$key][0] == '-') ? -$tab[$key][1] : $tab[$key][1];
		$delta = $b * $b - 4 * $a * $c;
		if ($delta > 0)
		{
			$s1 = (-$b - ft_sqrt($delta)) / (2 * $a);
			$s2 = (-$b + ft_sqrt($delta)) / (2 * $a);
			echo "Discriminant is strictly positive, the two solutions are:\n\033[33m$s1\n$s2\n\033[0m";
		}
		else if (!$delta)
		{
			$s = -$b / (2 * $a);
			echo "Discriminant is null, the solution is:\n\033[33m$s\033[0m\n";
		}
		else
		{
			echo "Discriminant is strictly negative, the two solutions are:\n";
			$sqrt = ft_sqrt(-$delta) / (2 * $a);
			echo "\033[33m", ($b) ? -$b / (2 * $a)." + " : "", ($sqrt !== (float)1) ? "i * ".$sqrt : "i", "\n\033[0m";
			echo "\033[33m", ($b) ? -$b / (2 * $a)." " : "", ($sqrt !== (float)1) ? "- i * ".$sqrt : "- i",  "\n\033[0m";
		}
	}
}

function    ft_computer($str)
{
	if (substr_count($str, "=") == 1)
	{
		$equ = explode("=", $str);
		if (ft_isok($equ[0]) && ft_isok($equ[1]))
		{
			$tab1 = ft_parse($equ[0]);
			$tab2 = ft_parse($equ[1]);
			$equ1_reduce = ft_reduce1($tab1);
			$equ2_reduce = ft_reduce1($tab2);
			$equ_reduce = ft_reduce2($equ1_reduce, $equ2_reduce);
			$equ_reduce = ft_reduce3($equ_reduce);
			ft_print_reduce($equ_reduce);
			$degree = (int)ft_degree($equ_reduce);
			echo "Polynomial degree: \033[33m", $degree, "\n\033[0m";
			if (empty($equ_reduce) && !$degree)
				echo "\033[32mAll real numbers are solution.\n\033[0m";
			else if (!$degree)
				echo "\033[32mThe polynomial degree is null, I can't solve.\n\033[0m";
			else if ($degree > 2)
				echo "\033[32mThe polynomial degree is stricly greater than 2, I can't solve.\n\033[0m";
			else if ($degree < 0)
				echo "\033[32mThe polynomial degree is negative, I can't solve.\n\033[0m";
			else
				ft_solve($equ_reduce, $degree);
		}
		else
			echo "\033[31mBad format.\n\033[0m";
	}
	else
		echo "\033[31mBad format.\n\033[0m";
}

function	main($argc, $argv)
{
	if ($argc == 2)
	{
		if ($argv[1] === "-b")
		{
			echo "\033[44mEquation:\033[0m \033[33m";
			$fd = fopen("php://stdin", "r");
			while (($line = fgets($fd)) !== false)
				echo "\033[0m", ft_computer(trim($line)), "\033[44mEquation:\033[0m \033[33m";
		}
		else
			ft_computer($argv[1]);
	}
	else
		echo "\033[31mBad numbers of arguments.\n\033[0m";
}

main($argc, $argv);

?>