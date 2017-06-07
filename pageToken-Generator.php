<?php

function get_weight($weight,$number) {
	$n = $number;
	foreach($weight as $val) {
		$res[] = (int) floor($n/$val);
		$n = $n % $val;
	}
	return $res;
}

function number2token($number) {

	$isPrevToken = False;
	$prefix = 'C';
	$nextToken = "AA";
	$prevToken = "A_";

	$b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
	$weight = array ('65536', '16384', '8192', '128', '16', '1');

	$x = get_weight($weight,$number);

	$self['weight_1'] = $x[5];
	$self['weight_16'] = $x[4];
	$self['weight_128'] = $x[3];
	$self['weight_8192'] = $x[2];
	$self['weight_16384'] = $x[1];
	$self['weight_65536'] = $x[0];

	$offset['w16'] = ($number < 128)?0:8;
	$offset['w1'] = ($self['weight_8192'] % 2) + 2;

	# converts range(0, 3) into 'BRhx'
	$suffix_pos = ($self['weight_16384'] * 16 + 1) % 64;
	$self['char_16384'] = $b64[$suffix_pos];

	if ($number < 16384) {
		$self['char_16384'] = 'E';
		$offset['w1'] = 1;
	}
	if ($number < 8192) {
		$offset['w1'] = 0;
	}
	if ($number < 128) {
		$self['char_16384'] = 'Q';
	}


	$self['char_1'] = $b64[$self['weight_1'] * 4 + $offset['w1']];		# converts range(0, 15) into  'AEIMQUYcgkosw048', 'BFJNRVZdhlptx159', 'CGKOSWaeimquy26-', 'DHLPTXbfjnrvz37_'
	$self['char_16'] = $b64[$self['weight_16'] + $offset['w16']];		# converts range(0 ,7) into 'ABCDEFGH' and 'IJKLMNOP'
	$self['char_128'] = ($number >= 128)?$b64[$self['weight_128']]:'';
	$self['char_65536'] = ($number >= 16384)?$b64[$self['weight_65536']]:'';

	$p = $prefix;
	$n1 = $self['char_16'];
	$n2 = $self['char_1'];
	$n3 = $self['char_128'];
	$n4 = $self['char_65536'];
	$n5 = $self['char_16384'];
	$s = ($isPrevToken)?$prevToken:$nextToken;

	$self['token'] = "$p$n1$n2$n3$n4$n5$s";

	return $self['token'];
}

function token2number($token) {

	$b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

	if(strlen($token) < 6) die("Not A Valid Page Token");

	if(substr($token,-1) != 'A') $isPrevToken = True;

	# Remove prefix and previous/next tags
	$t = substr($token,1,-2);

	# Get Suffix Char and Remove it
	$self['char_16384'] = substr($t,-1);
	$t = substr($t,0,-1);

	$self['char_1'] = $t[1];
	$self['char_16'] = $t[0];

	if(strlen($t)>2) $self['char_128'] = $t[2];
	if(strlen($t)>3) $self['char_65536'] = $t[3];

	$pos = strpos($b64, $self['char_1']);
	$self['weight_1'] = ($pos - ($pos % 4)) / 4; # Converts these sequences to range(0, 15). 'AEIMQUYcgkosw048', 'BFJNRVZdhlptx159', 'CGKOSWaeimquy26-', 'DHLPTXbfjnrvz37_'

	if( ($pos % 4 == 3) || ($pos % 4 == 1) ) $self['weight_8192'] = 1; # every odd 8192

	$pos = strpos($b64, $self['char_16']);
	$self['weight_16'] = $pos % 8; # Converts 'ABCDEFGH' and 'IJKLMNOP' into range(0, 7)

	if(strlen($t) > 2) {
		$pos = strpos($b64, $self['char_128']);
		$self['weight_128'] = $pos;
	}

	if(strlen($t) > 3) {
		$pos = strpos($b64, $self['char_16384']);
		$self['weight_16384'] = ($pos - 1) /16; # Converts 'BRhx' into range(0, 3)
		$self['weight_65536'] = strpos($b64, $self['char_65536']);
	}

	$retval = 	1 * $self['weight_1'] +
				16 * $self['weight_16'] +
				128 * $self['weight_128'] +
				8192 * $self['weight_8192'] +
				16384 * $self['weight_16384'] +
				65536 * $self['weight_65536'];

	return $retval;

}

		if (($_GET['number']) print_r(number2token($_GET['number']));
		if (($_GET['token']) print_r(token2number($_GET['token']));