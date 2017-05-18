<?php
//класс для стреминга (обрезания слов)

class Lingua_Stem_Ru_HTmp// взят из плагина для wp_stem_ru название класса экранировано
{
    var $Stem_Caching = true;
    var $Stem_Cache = array();
    var $VOWEL = '/аеиоуыэюя/u';
    var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/u';
    var $REFLEXIVE = '/(с[яь])$/u';
	
	//Прилагательное
    var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/u';
    
	var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/u';
    var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/u';
    
	//Существительное
	var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/u';
    
	var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/u';
    var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/u';

	function Lingua_Stem_Ru_HTmp() 
	{
		mb_internal_encoding("UTF-8");
	}

    function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    function m($s, $re)
    {
        return preg_match($re, $s);
    }
	function is_pril($word)//прилагательное
	{	
		//return $this->stem_word($word,"!NOUN");
		$word = mb_strtolower($word,'utf-8');
        $word = preg_replace("/ё/u","е",$word);//ё=>е
		$v=$this->s($word, $this->ADJECTIVE, '');
		return $v.' '.$word;
	}
	function is_sysh($word)//существительное
	{
		//return $this->stem_word($word,"!ADJECTIVE");

		$word = mb_strtolower($word,'utf-8');
        $word = preg_replace("/ё/u","е",$word);//ё=>е
		$v=$this->s($word, $this->NOUN, '');
		return $v.' '.$word;
	}
    function stem_word($word,$Short=false)
    {
		if(!$Short)
		{
			$word = mb_strtolower($word,'utf-8');
			$word = preg_replace("/ё/u","е",$word);//ё=>е
        }
		if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) 
            return $this->Stem_Cache[$word];
        $stem = $word;
			 
        do 
		{
			if(!preg_match($this->RVRE, $word, $p))
				break;
			$start = $p[1];
			$RV = $p[2];
			if (!$RV) 
				break;
				
			# Step 1
			if(!$this->s($RV, $this->PERFECTIVEGROUND, '')) 
			{
				$this->s($RV, $this->REFLEXIVE, '');
				if ($this->s($RV, $this->ADJECTIVE, '')) 
					$this->s($RV, $this->PARTICIPLE, '');
				elseif (!$this->s($RV, $this->VERB, ''))
                      $this->s($RV, $this->NOUN, '');
			}
			$this->s($RV, '/и$/u', '');

			# Step 3
			if ($this->m($RV, $this->DERIVATIONAL))
				$this->s($RV, '/ость?$/u', '');

			# Step 4
			if (!$this->s($RV, '/ь$/u', '')) 
			{
				$this->s($RV, '/ейше?/u', '');
				$this->s($RV, '/нн$/u', 'н');
			}
			$stem = $start.$RV;
        } while(false);
        

		if (!$mode && $this->Stem_Caching) 
			$this->Stem_Cache[$word] = $stem;
        return $stem;
    }

    function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/')) {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }

    function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }
}
global $HStrem1;
$HStrem1 = new Lingua_Stem_Ru_HTmp();
function HkStrem($In, $Short=false)
{
	global $HStrem1;
	if(!$HStrem1)
		$HStrem1= new Lingua_Stem_Ru_HTmp();
	return $HStrem1->stem_word($In,$Short);
} 

	function HkStremIsPril($word)//прилагательное
	{	
		global $HStrem1;
		if(!$HStrem1)
			$HStrem1= new Lingua_Stem_Ru_HTmp();
		return $HStrem1->is_pril($word);
	}
	function HkStremIsSysh($word)//прилагательное
	{	
		global $HStrem1;
		if(!$HStrem1)
			$HStrem1= new Lingua_Stem_Ru_HTmp();
		return $HStrem1->is_sysh($word);
	}
function HkStremIsSameWords($X,$Y, $YaFilter=true)
{
	static $Cash=Array(); 
	if($X==$Y)
		return true;
	if($X{0}!=$Y{0}||$X{1}!=$Y{1})
		return false;
	$xlen=strlen($X);
	$ylen=strlen($Y);
	if($xlen==$ylen)
		return false;
	if($xlen<$ylen)
	{//CS более универсальный 
		$T=$Y;
		$Y=$X;
		$X=$T;
	}
	$CS=$X.' '.$Y;
	if(isset($Cash[$CS]))
		return $Cash[$CS];
	
	if($YaFilter)
	{//Прилагательное!=Существительному
		if($X.'ная'==$Y||$X==$Y.'ная'
		||$X.'ный'==$Y||$X==$Y.'ные'
		||$X.'ное'==$Y||$X==$Y.'ное'
		||$X.'ные'==$Y||$X==$Y.'ные'
		||$X.'ая'==$Y||$X==$Y.'ая'
		||$X.'ый'==$Y||$X==$Y.'ые'
		||$X.'ое'==$Y||$X==$Y.'ое'
		||$X.'ые'==$Y||$X==$Y.'ые'
		||$X.'ой'==$Y||$X==$Y.'ой'
		||$X.'им'==$Y||$X==$Y.'им'
		||$X.'ими'==$Y||$X==$Y.'ими'
		||$X.'ого'==$Y||$X==$Y.'ого'
		||$X.'ской'==$Y||$X==$Y.'ской'
		||$X.'ский'==$Y||$X==$Y.'ския'
		||$X.'ская'==$Y||$X==$Y.'ская'
		||$X.'ские'==$Y||$X==$Y.'ские'
		||$X.'ских'==$Y||$X==$Y.'ских'
		||$X.'скими'==$Y||$X==$Y.'скими'
		||$X.'альный'==$Y||$X==$Y.'альный'
		||$X.'альная'==$Y||$X==$Y.'альная'
		||$X.'альные'==$Y||$X==$Y.'альные'
		||$X.'альное'==$Y||$X==$Y.'альное')
		{
			$Cash[$CS]=false;
			return false;
		}
	}
	$X1=HkStrem($X,true);//урезаная версия когда слова в нижнем регистре
	if(mb_strlen($X1,'utf-8')<2)
	{
		$Cash[$CS]=false;
		return false;
	}
	if($X1==$Y)
	{
		$Cash[$CS]=true;
		return true;
	}
	$xlen=strlen($X1);
	if($xlen<=$ylen)
	{
		$Cash[$CS]=false;
		return false;
	}
	$Y1=HkStrem($Y,true);
	//if($X1==$Y1)
	//	echo "$X==$Y ($X1) <br />";
	$Cash[$CS]=($X1==$Y1);
	return $X1==$Y1;
}
?>