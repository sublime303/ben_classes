<?php
#include ("jp/src/jpgraph.php");
#include ("jp/src/jpgraph_line.php");




#echo "loaded..";
class wx{

    public $metar;
    #public $page_from_lfv;

    public $icao;
    public $temp;
    public $dew;
    public $hdg;
    public $spd;
    public $pres;
    public $sikt;

    public $day;
    public $time;
    public $age;


    public $clouds = array(); #cloud layers
    # clouds['009']='OVC'

    # SAMPLE DATA
    # ESSA 311450Z 10009KT 040V130 9000 -SG OVC009 M04/M05 Q1020 RWY 01L      ICE 11-25 PCT 1 MM BA GOOD RWY 08 ICE 11-25 PCT 1 MM BA GOOD      RWY 01R CON            TAMI 11750 NO FURTHER INFO NOSIG=

    # metar.se :)


    # $metar is the metar to work with... 
    # build a function to regex a metar from a fat page/string to $metar
    # like function extract_metar_from_page('ESSA')
    #

    function wx(){  #constructor

    }

    function metar($metar = '-nodata-'){
	        
        # Take input and decode and populate propterties

        $this->metar = preg_replace('/\s+/', ' ', $metar); #CLEAN spaces and newlines from input
        $this->decode();
        #$this->decode_metar(); #named groups?
    }


	public static function tempfix($temp_string){
		if (strlen($temp_string) == 3) # 3chars = contains M for minus
			return '-'.substr($temp_string, 2); #do
		return $temp_string; #else
			
    }


    function decode(){
        # En skysst named group regex..         
        $regex ="/(?P<ICAO>[A-Z]{4}).+(?P<UTC>\d{6}Z).+(?P<HDG>\d{3})(?P<SPD>\d{2})KT.+(?<SIKT>\d{4}).+\s(?<TEMP>M?\d{2})\/(?P<DEW>M?\d{2}) Q(?<QNH>\d+)\s/";
        #echo $this->metar;
        if (preg_match($regex, $this->metar, $a)) {
            #print_r($a); #decode debugging
            $this->icao = $a['ICAO'];
            $this->temp = $a['TEMP'];
            $this->dew = $a['DEW'];
            $this->hdg = $a['HDG'];
            $this->spd = $a['SPD'];
            $this->sikt = $a['SIKT'];
            $this->pres = $a['QNH'];


            

            # some afterwork fixes  minus temps
            if (strlen($this->temp)==3){ # 3chars = contains M for minus
                $this->temp = '-'.substr($this->temp, 2);
            }

            if (strlen($this->dew)==3){ # 3chars = contains M for minus
                $this->dew = '-'.substr($this->dew, 2);
            }

            $this->day = substr($a['UTC'],0,2);
            $this->time = substr($a['UTC'],2,4);

        }else{
            echo "regex parse fail";
        }
    }
}

$essa = new wx;
#$essa->metar('ESSA 311450Z 10009KT 040V130 9000 -SG OVC009 M04/M05 Q1020 RWY 01L      ICE 11-25 PCT 1 MM BA GOOD RWY 08 ICE 11-25 PCT 1 MM BA GOOD      RWY 01R CON            TAMI 11750 NO FURTHER INFO NOSIG=');


#get metar and run /(test)
$fileContents = file_get_contents('http://www.lfv.se/MetInfo.asp?TextFile=metar.sweden.list.txt&SubTitle=&T=METAR%A0Sweden&Frequency=30');
#get line with a ICAO
$icao = 'ESSA';

$regex ="/(?P<ICAO>[$icao]{4}).+(?P<UTC>\d{6}Z).+(?P<HDG>\d{3})(?P<SPD>\d{2})KT.+(?<SIKT>\d{4}).+\s(?<TEMP>M?\d{2})\/(?P<DEW>M?\d{2}) Q(?<QNH>\d+)\s/";
        #echo $this->metar;
if (preg_match($regex, $fileContents, $outArray)) {
	foreach ($outArray as $array) {
		#hopefully, only 1 metar is found on page
		$metar = $outArray[1];
	}
}


$essa->metar('ESSA 311450Z 10009KT 040V130 9000 -SG OVC009 M04/M05 Q1020 RWY 01L      ICE 11-25 PCT 1 MM BA GOOD RWY 08 ICE 11-25 PCT 1 MM BA GOOD      RWY 01R CON            TAMI 11750 NO FURTHER INFO NOSIG=');



#testing


$essa->metar('ESSA 312220Z 10005KT 9999 -SN BKN010 M04/M04 Q1013 RWY 01L WET SNOW 
     11-25 PCT 1 MM FC 0.73 RWY 08 ICE 11-25 PCT 1 MM FC 0.68 RWY 
     01
            R 31 TAMINATED, NO FURTHER INFO TEMPO 4000 BKN010=');
#echo $essa->metar;
echo "\n\ntempen på $essa->icao @ $essa->day kl $essa->time.   $essa->temp grader och sikten är $essa->sikt km";

#echo $essa->get_data();



 ########

$mysqlhst = "";
$mysqlusr = "";
$mysqlpw = "";
$mysqldb = "";

function dbq($query){
    global $mysqlhst,$mysqlusr,$mysqlpw,$mysqldb,$res,$con;
	$con        = mysql_connect($mysqlhst,$mysqlusr,$mysqlpw)or die("<br>Database error:User/Password or mySQL on $mysqlhst not found\n").mysql_error($con);
	mysql_select_db($mysqldb)                          or die("<br>Database error:Wrong or nonexisting database");
	return $res = mysql_query($query);#                  or die("<br>Database error:Query syntax");
	mysql_free_result($con);
	mysql_close($con);
}


#$tid = 24*3*1*1;
$tid = 24*7*1*1;
$skip=2; #skip every
$skipping=0; #skip every
$i=1;
dbq("select  * from wx order by id desc limit $tid");#limit 200,400
while ($row = mysql_fetch_array($res)){
    $skipping++;
    if ($skipping == $skip){
        $skipping=0;continue;
    }



    # a sample METAR
    # ESSA 311550Z 11010KT 9999 -SG OVC007 M04/M05 Q1020 RWY 01L ICE 11-25     PCT 1 MM FC 0.61 RWY 08 ICE 11-25 PCT 1 MM FC 0.63 RWY 01R CONTAMINATED,     NO FURTHER INFO NO            SIG=

    $id= $row['id'];
    $metar= $row['metar'];
    $dat= $row[dat];
    $datax[] = substr($dat,8,5);
    if (preg_match('/(M?\d{2}\/M?\d{2})/', $metar, $regs)) {
        
        #regex temps and dews

        $result = $regs[0];
        $exploded=explode("/",$result);
        $t =$exploded[0];
        $d =$exploded[1];
        #$f = $fukt[]= ($exploded[1]/$exploded[0])*100;
        if(substr($t,0,1)=="M"){ $t=intval("-".substr($t,1)); }
        if(substr($d,0,1)=="M"){ $d=intval("-".substr($d,1)); }
        #$t=5;$d=4;
        settype($t, "integer");
        settype($d, "integer");
        $temps[]    =$t;
        $dews[]     =$d;
        #echo "<br>t:$t  d:$d";

    }

    if (preg_match('/Q(\d){4}/', $metar, $regs)) {
        $pres[] = substr($regs[0],1);
    }

    if (preg_match('/(\d{5}KT)/', $metar, $regs)) { #normal winds
        $air = $regs[0];
        $h = $hdg[]=substr($air,0,3)/10;
        $s = $spd[]=substr($air,3,2);
        #echo "<br>HDG:$hdg SPD:$spd";
    }

    if (preg_match('/(\\d{5}G\\d{2}KT)/', $metar, $regs)) { #Gusty winds
        $air = $regs[0];
        $h =    $hdg[]=substr($air,0,3)/10;
        $s =    $spd[]=substr($air,6,2); #gusts
        #echo "<br>HDG:$hdg SPD:$spd";
    }

    if (preg_match('/(VRB\\d{2}KT)/', $metar, $regs)) { #Gusty winds
        $air = $regs[0];
        #$h =   $hdg[]=substr($air,0,3);
        $h =    $hdg[]=0;
        $s =    $spd[]=substr($air,3,2); #gusts
        #echo "<br>HDG:$hdg SPD:$spd VRB!";
    }

    #echo "<br>$metar => $t/$d $h $s"; #debugging


}

$temps  = array_reverse($temps);
$dews   = array_reverse($dews);
#$fukt  = array_reverse($fukt);
$pres   = array_reverse($pres);
$hdg    = array_reverse($hdg);
$spd    = array_reverse($spd);
$datax  = array_reverse($datax);

#settype($temps,"integer");
#settype($dews,"integer");
#settype($dews,"integer");
#settype($dews,"integer");
#settype($dews,"integer");
#settype($datax,"integer");


/*

$graph = new Graph(1200,400,"auto");
$graph->img->SetMargin(40,50,20,100);
$graph->SetScale("textlin");
$graph->SetY2Scale("lin");

$graph->xaxis->SetTextLabelInterval(1);

$graph->SetShadow();

// Create the linear plot
$lineplot_temp  =   new LinePlot($temps);
$lineplot_dew   =   new LinePlot($dews);
$lineplot_pres  =   new LinePlot($pres);
$lineplot_spd   =   new LinePlot($spd);
$lineplot_hdg   =   new LinePlot($hdg);

// Add the plot to the graph
$graph->Add($lineplot_temp);
$graph->Add($lineplot_dew);
$graph->Add($lineplot_spd);
$graph->Add($lineplot_hdg);
$graph->AddY2($lineplot_pres);


$lineplot_spd->SetColor("red");
$lineplot_temp->SetColor("blue");
$lineplot_dew->SetColor("aqua");
$lineplot_hdg->SetColor("yellow");

$lineplot_spd->SetWeight(3);
$lineplot_temp->SetWeight(1);
$lineplot_dew->SetWeight(1);
$lineplot_hdg->SetWeight(1);


#$graph->y2axis->SetColor("orange");
$graph->title->Set("temp/dew/pressure/fukt/windspeed");
$graph->xaxis->title->Set("Tid");
$graph->yaxis->title->Set("mixed values");
$graph->y2axis->title->Set("mb");

$lineplot_temp->SetLegend ("TMP c");
$lineplot_dew->SetLegend("DEW c");
$lineplot_pres->SetLegend("PReS mb");
$lineplot_spd->SetLegend("SPD KT");
$lineplot_hdg->SetLegend("HDG /10");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);



#Färg på skalan Y
$graph->yaxis->SetColor("blue");
$graph->y2axis->SetColor("black");


#$lineplot_fukt->SetLegend("Fukt");
#$graph ->legend->Pos( 0.05,0.5,"left" ,"up");
$graph->legend->SetPos(0.05,0.01,'left','top');

$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetLabelAlign("bottom");
$graph->xaxis->SetLabelMargin(10);


#$graph->xaxis->SetPos(0.05,0.01,'left','bottom');

#$graph->img-> SetMargin(50,50,10,50);

# extra rader
#$graph->ygrid->Show (true);

$graph->img->SetAntiAliasing();

$graph ->xgrid->Show(true);

#$lineplot_temp->value-> Show();


#limits
#$graph-> SetScale( "textlin",-50,50);

// Display the graph
$graph->Stroke();
#####################################

echo "<pre>";
#print_r($temps);
echo round(array_sum($temps)/count($temps),1)." Medeltemp";
*/
?>


