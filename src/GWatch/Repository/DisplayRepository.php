<?php
namespace GWatch\Repository;

use Doctrine\DBAL\Connection;
/**
 * Display repository
 */
class DisplayRepository
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    protected $dbprefics;
    protected $dbmanagemant;
    
    public function __construct(Connection $db, $prefics, $managemantdb )
    {
        $this->db = $db;
        $this->dbprefics = $prefics;
        $this->dbmanagemant = $managemantdb;
       
    }
	
	protected function module($module){
		
		return $this->dbprefics . trim($module);
	
	}
    
	public function ReadColumnInfo($module)
	{
	
 		$sql = "
 		SELECT 
 			max(col) as num 
 		FROM 
 			".$this->module( $module ).".col;";
 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 		$result = $stmt->fetch(\PDO::FETCH_ASSOC); 
 		
 		$colInfo["NumDataColumns"] = $result['num'];
	
 		$sql = "
 		SELECT 
 			* 
 		FROM 
 			".$this->module( $module ).".`col` 
 		ORDER BY 
 			col;";

 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 		 

 		$i = 0;
		$gr = 0;
		$test = ''; 
		$subs = 3;
		
		$ms = 0;
		while ($rowch  = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
		
	 		//$colInfo["NumDataColumns"] ++;
			$stat = '';
			if ($rowch['statname']) $stat = $rowch['statname'];
			$prvTest = $test;
			$test = substr($rowch["test"], 0, $subs);
			$rowch["test"] = trim($rowch["test"]);
			$col = $rowch["col"];
			$bg =  $rowch["bg"];
			if ($prvTest != $test)
			{
				if ($prvTest != '')
				{
					$colInfo[$i] = array(
						"Name" => "$prvTest",
						"DataColumn" => - 1,
						"StatName" => "$stat",
						"GroupIndex" => $gr,
						"IdColumn" => $ms,
						"bg" => $bg
					);
					$gr++;
					$i++;
					$ms++;
				}

				$colInfo[$i] = array(
					"Name" => "$test",
					"DataColumn" => - 1,
					"StatName" => "$stat",
					"GroupIndex" => $gr,
					"IdColumn" => $ms,
						"bg" => $bg
				);
				$i++;
				$ms++;
			}

			$colInfo[$i] = array(
				"Name" => $rowch["test"],
				"DataColumn" => $col,
				"StatName" => "$stat",
				"GroupIndex" => $gr,
				"IdColumn" => $ms,
						"bg" => $bg
			);
			$i++;
			$ms++;
		}

		$colInfo[$i] = array(
			"Name" => "$test",
			"DataColumn" => - 1,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
						"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;
		$col++;
		$colInfo[$i] = array(
			"Name" => "Genes",
			"DataColumn" => $col,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
					"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;

		$colInfo[$i] = array(
			"Name" => "$test",
			"DataColumn" => - 1,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
						"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;
		$col++;
	
/*			$colInfo[$i] = array(
			"Name" => "Effect",
			"DataColumn" => $col,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
					"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;

*/
		$colInfo["NumColumns"] = $i;
		return $colInfo;
	}

	public function ReadRowCount($module, $chr)
	{	


    	return $this->db->fetchColumn("SELECT chrlen FROM ".$this->module( $module ).".chrsupp WHERE chr = $chr");
 	 
		 

	}

	public function ReadRowData($module, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, $returnHeight, $heightScale)
	{

		 
	
		$sql = "SELECT * FROM ".$this->module( $module ).".`col` ORDER BY col;";
 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();

		while ($md  = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
		if($md['islog']){
			$n = $md['col'];
			$colslog[$n] = $md['islog'];
		
			}
	
		}
	
		$currentRowHeader = 0;
		$trackBlock = array();
		$rowDataEnd = $rowDataStart + $rowDataCount;
		$dataBlock["NumColumns"] = $numDataColumns;
		$dataBlock["NumRows"] = $rowDataCount;
						
		$q="
		SELECT 
			c.chr, c.chrname, nrow, a.alias, p.pos, 1 as maf, 1 AS marker, 
				(SELECT  
					g.`gene`  
				FROM   
					GWATCH.genes g 
				WHERE 
					g.posstart < p.pos 
				AND 
					g.posend > p.pos 
				AND 
					g.chr = $chr 
				LIMIT 1
				) as gene, e.dbn AS effect 
		 FROM 
		 	".$this->module( $module ).".ind r 
		 JOIN 
		 	".$this->module( $module ).".alias a USING ( ind ) 
		 JOIN 
		 	".$this->module( $module ).".chr c USING ( chr ) 
		 JOIN 
		 	".$this->module( $module ).".pos p 
		 USING ( ind ) 
		 LEFT JOIN 
		 	GWATCH.medtable e 
		 ON 
		 	p.pos = e.pos 
		 AND 
		 	c.chr = e.chr  
		 WHERE 
		 	r.chr = $chr 
		 AND 
		 	r.nrow >=$rowDataStart 
		 AND 
		 	r.nrow <=$rowDataEnd 
		 AND 
		 	r.nrow >0 
		 ORDER BY 
		 	r.nrow;";
 		$stmt = $this->db->prepare($q);
 		$stmt->execute();
		@$i = 0;
		while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
		
			$dataBlock["Rows"][$i] = array(
				"marker" => $rowch["marker"],
				"Name" => $rowch["alias"],
				"Coords" => $rowch["pos"],
				"MAF" => round($rowch["maf"], 5),
				"Gene" => $rowch["gene"] ,
				"Effect" => $rowch["effect"] 
			);
		
			$trackBlock[$i] = $rowch["gene"];
			$effectBlock[$i] = $rowch["effect"];
	 
		
			$i++;
		}
		$pv = '';
		if ($returnHeight) 
			$pv = " AND pval <= 4 AND pval > 0 "; 
			$q = "
			SELECT 
				nrow, col,  pval,ratio , v.v_ind
			FROM 
				".$this->module( $module ).".ind i
			JOIN 
				".$this->module( $module ).".v_ind v using(ind)
			JOIN  
				".$this->module( $module ).".pval using(v_ind)
			JOIN  
				".$this->module( $module ).".ratio using(v_ind)
			WHERE 
				i.chr = $chr 
			AND 
				nrow >= $rowDataStart 
			AND 
				nrow <= $rowDataEnd  
				$pv
			ORDER BY 
				v.v_ind";
		
	
		@$j = 0;
		$cols = $dataBlock["NumColumns"] + 1;
		
		$stmt = $this->db->prepare($q);
 		$stmt->execute();
		$i = 0;
		$colslog = false;
		while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
			@$row = $rowch['nrow'] - $rowDataStart;
		
			@$col = $rowch['col'];
			if ($returnHeight)
			{
			
				$height = (round(log10($rowch["pval"]) , 3) + 2) * -$heightScale;
			 
				if($colslog){
					if($colslog[$col] == 1)
					$height = (round($rowch["pval"], 3) + 2) * -$heightScale;
			
				}
			 
				if ($height < 0.0) continue;
				$dataBlock[$row][$col] = array(
					$height,
					$rowch["ratio"]
				);
			}
			else
			{
				if ($rowch["pval"] > 0) $dataBlock[$row][$col] = array(
			
					-log($rowch["pval"]) ,
					$rowch["ratio"]
				);
				else $dataBlock[$row][$col] = array(-1, -1
				);
			}
	 
			//print_r($dataBlock);
		
		
			@$j++;
		
		}
		 
			//add track - genes
			$prevvalue = false;
			$color = -1;
			foreach($trackBlock as $key => $value){
	
				if($value != ''){	
			
				if($value != $prevvalue)
					$color = -$color;
			
					$dataBlock[$key][$cols] = array(0, $color);		
		
				 $j++;
				$prevvalue = $value;
				}
		
			}
		
			//add track - effects 
			/*
			$color = -1;
			$newcols = $cols + 1;
			foreach($effectBlock as $key => $va){
	
				if($va != null){	
			
				if($va != $prevva)
					$color = -$color;
			
					$dataBlock[$key][$newcols] = array(10, $color);		
		
				 $j++;
				$prevva = $va;
				}
		
			}
		
		 */


		// read the row data.
		// Skip any rows that aren't relevant to what we're looking for.
		// we still need to read them but we don't need to
		// store them

		$dataBlock["NumDataPoints"] = $j+$i;

		$level = 1; //treshhold for search replication

		foreach($dataBlock as $k => $r){
	
			if(is_numeric($k)){
			
				foreach($r as $key => $value){
		 
					if(isset( $columns[$key])){

						$master = $columns[$key];
						$slave = $key;

						if((!$dataBlock[$k][$master][0]) || ($dataBlock[$k][$master][0] < $level)){
				 
							unset( $dataBlock[$k][$slave]);

							$dataBlock["NumDataPoints"]--;
					
			
						}
					
					}
	
				}
			
			}
		
		}
 
		return $dataBlock;
	
	}
	public function getEyePos( $eyeposParam )
	{
		 
		if ( isset( $eyeposParam ) )
		{
			$eyeposParam = str_replace( ' ', '', $eyeposParam );
			// check it matches the right format
	
			if ( preg_match( "/^\[[\d\.\-]+\,[\d\.\-]+\,[\d\.\-]+\]$/", $eyeposParam ) )
			{
				// echo the parsed eye pos
				return $eyeposParam;
				 
			}

		}

		// use the default
		return "[0, 40, 70]";
	}

	public function getDistance( $distanceParam )
	{
		 
		if ( isset( $distanceParam ) )
		{
			$distance = floatval( $distanceParam );
			if ( $distance < 5 )
			{
				$distance = 5;
			}
	
			return $distance;
	
			 
		}

		return "80";
	}

	public function getAngleX( $anglexParam )
	{
		 
		if ( isset( $anglexParam ) )
		{
			$anglex = floatval( $anglexParam );
			$halfPi = pi() * 0.5;
			if ( $anglex < - $halfPi )
			{
				$anglex = -$halfPi;
			}

			if ( $anglex > $halfPi )
			{
				$anglex  = $halfPi;
			}
	
			return $anglex;
	
			 
		}
 
		return "0.0";
	}

	public function getOffsetX( $offsetxParam )
	{
		 
		if ( isset( $offsetxParam ) )
		{
			$offsetx = floatval( $offsetxParam );
			return $offsetx;
	
			 
		}
		return "0.0";
	}
		
	
	public function getCurrentRow($urlRow)
	{
		 
		if ( isset( $urlRow ) )
		{
			return $urlRow;
		}
		else
		{
			return "0";
		}

	}

	public function getCurrentURL()
	{
		return (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
	}

	public function ReturnError($error)
	{
		$errorMsg["Error"] = $error;
		echo json_encode($errorMsg);
		exit(0);
	}
		
		
	public function getTreshhold( $threshold ){


		$ts = array(1,2,3,4);
		if(in_array($threshold, $ts)) 
			return exp(-($_GET['threshold'] + 0) * log(10));
		else 
			return exp(-(2 + 0) * log(10)); 


	}
		
	public function GetBuildAndPlatform($module){
		if($module)
		{
			$q = "
				SELECT 
					build, 
					platform 
				FROM 
					". $this->dbmanagemant .".module 
				WHERE 
					name = '$module';";
		
		
			$stmt = $this->db->prepare($q);
			$stmt->execute();
	 
			$rowch = $stmt->fetch(\PDO::FETCH_ASSOC);
			$md['build'] = $rowch['build'];
			$md['platform'] = $rowch['platform'];
			return $md;
		}
		else
			return false;

	}
	
		public function GetSearchType($SearchTerm){
	
		if(is_numeric($SearchTerm) && $SearchTerm > 0){
		
			if($SearchTerm <= 20){
			
				 
				return 3;  // log(p)
		
			}
			elseif(preg_match('/^\+?\d+$/', $SearchTerm) && $SearchTerm > 20){
			
				 
				return 1;  // coordinates
		
			}
			else {
			
			 
				return -1;
			}
		}
		else{
			if(preg_match('/^rs\d+$/', $SearchTerm) || preg_match('/^kgp\d+$/', $SearchTerm) || $SearchTerm == -1)
		 
				return 0; //SNP
			
			else{
				 
				return 2; // GENE
			
			}
	
	
		}
		}
		public function addSpace($var, $space, $count){
	
			$adprobel = '';
	
			$commas = substr_count($var, ',');
	
			for($i=0;$i<$commas;$i++){
	
				$adprobel = "$adprobel"."&nbsp;";
	
			}
	
			$ad = ($count - strlen($var)) * strlen($space) + strlen($var);
	
			return str_pad($var,$ad,$space)."$adprobel";

		}
		public function getChrDescription($module){
			
			$q = "SELECT * FROM ".$this->module( $module ).".chrsupp;";
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			@$i = 0;
			while ($chrres = $stmt->fetch(\PDO::FETCH_ASSOC)){
				$nchr = $chrres['chr'];
				$chrsupp[$nchr] = $chrres['chroff'];
			}
			return $chrsupp;

		}
		public function search($module, $chr, $searchTerm, $searchType, $type){
		
		$found = 0;

		$results = "";
		$j=0;

	 
		
		if ( $searchType == 0) //SNP
		{	
			if($type == 0)$chrselect = " i.chr = $chr AND ";
			else $chrselect = " ";
 	
			$q = "
			SELECT 
				* 
			FROM 
				".$this->module( $module ).".alias 
			JOIN 
				".$this->module( $module ).".ind i using(ind) 
			JOIN 
				".$this->module( $module ).".pos USING(ind) 
			WHERE 
				$chrselect alias LIKE '%$searchTerm%' 
			AND 
				i.nrow > 0 
			ORDER BY 
				cast(chr as UNSIGNED), nrow;";
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			
			if($stmt->rowCount() == 0)
			{
				return FALSE;				
			}
			while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
			{
	 
				$curchr=$rowch['chr'] + 0;
				$result[$j][ 0 ] = $this->addSpace($rowch['alias'] , '&nbsp;&nbsp;', 13).$this->addSpace($rowch['chr'], '&nbsp;&nbsp;', 6).$this->addCommas($rowch['pos']);
				$result[$j][ 1 ] = $rowch["ind"] - $chrsupp[$curchr];
				$result[$j][ 2 ] = $rowch["chr"];

				$j++;
			} 
		}
		else if ( ( $searchType == 1 ) && ( is_numeric( $searchTerm ) ) ) //position
		{
	
 			$q = "
 			SELECT 
 				* 
 			FROM 
 				".$this->module( $module ).".alias 
 			JOIN 
 				".$this->module( $module ).".ind i using(ind) 
 			JOIN 
 				".$this->module( $module ).".pos p using(ind) 
 			WHERE  
 				i.chr = $chr 
 			AND 
 				p.pos = $searchTerm 
 			AND 
 				i.nrow > 0 
 			ORDER BY  pos;"; 
			
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			if($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
			{
		
		
				$result[0][ 0 ] = $this->addSpace($rowch['alias'], '&nbsp;&nbsp;', 13).$this->addCommas($rowch['pos']);
				$result[0][ 1 ] = $rowch["nrow"];

	 
			} 
			else 
			{
 				$q = "
 				SELECT 
 					* 
 				FROM 
 					".$this->module( $module ).".alias 
 				JOIN 
	 				".$this->module( $module ).".ind i using(ind) 
	 			JOIN 
	 				".$this->module( $module ).".pos using(ind) 
	 			WHERE  
	 				i.chr = $chr 
	 			AND 
	 				pos > $searchTerm 
	 			AND 
	 				i.nrow > 0 
	 			ORDER BY 
	 				pos 
	 			LIMIT 5;"; 
				$stmt = $this->db->prepare($q);
				$stmt->execute();
				while($rowch1 = $stmt->fetch(\PDO::FETCH_ASSOC))
				{
			 
					$result[$j][ 0 ] = $this->addSpace($rowch1['alias'], '&nbsp;&nbsp;', 13).$this->addCommas($rowch1['pos']);
					$result[$j][ 1 ] = $rowch1["nrow"];

					$j++;
				} 
				$q = "
				SELECT 
					* 
				FROM 
					".$this->module( $module ).".alias 
				JOIN 
					".$this->module( $module ).".ind i using(ind) 
				JOIN 
					pos using(ind) 
				WHERE  
					i.chr = $chr 
				AND 
					pos < $searchTerm 
				AND 
					i.nrow > 0 
				ORDER BY  
					pos DESC 
				LIMIT 5;"; 
				$stmt = $this->db->prepare($q);
				$stmt->execute();
				while($rowch2 = $stmt->fetch(\PDO::FETCH_ASSOC))
				{
			 
					$result[$j][ 0 ] = $this->addSpace($rowch2['alias'], '&nbsp;&nbsp;', 13).$this->addCommas($rowch2['pos']);
					$result[$j][ 1 ] = $rowch2["nrow"];

					$j++;
				} 
			}
			 

		}
		else if ( $searchType == 2 ) //GENES
		{	

			if($type == 0)$chrselect = "AND   chr = '$chr' ";
				else $chrselect = " ";

 
 

			$q = "
			SELECT 
				gene, chr, posstart,  posend 
			FROM 
				GWATCH.genes
			WHERE 
				gene LIKE '%$searchTerm%' $chrselect
			ORDER BY 
				cast(chr as UNSIGNED), posstart;"; 
			
			
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			@$i = 0;
			
			while ($genes =  $stmt->fetch(\PDO::FETCH_ASSOC))
			{
				if($type != 0){
		
 					$chrselect = "AND   chr = $genes[chr] "; 
		 
				}

				$q = "
				SELECT 
					chr, nrow, alias , pos
				FROM 
					".$this->module( $module ).".ind 
				JOIN 
					".$this->module( $module ).".alias USING(ind) 
				JOIN 
					".$this->module( $module ).".pos USING(ind) 
				WHERE  
					pos >= $genes[posstart] 
				AND 
					pos <= $genes[posend]   
				$chrselect 
				AND  
					nrow > 0 
				ORDER BY 
					cast(chr as UNSIGNED), nrow;"; 
				$stmt = $this->db->prepare($q);
				$stmt->execute();
				while ($rowgen = $stmt->fetch(\PDO::FETCH_ASSOC))
				{
		
					$result[$j][ 0 ] = $this->addSpace($rowgen['alias'], '&nbsp;&nbsp;', 13).$this->addSpace($rowgen['chr'], '&nbsp;&nbsp;', 6).$this->addSpace($this->addCommas($rowgen[pos]), '&nbsp;&nbsp;', 13)."$genes[gene]";
					$result[$j][ 1 ] = $rowgen["nrow"];
					$result[$j][ 2 ] = $rowgen["chr"];

					$j++;
				}
			} 
		}

		else if ( $searchType == 3 )//log(p)
		{	
		 
			$searchTerm=exp(-($searchTerm + 0) * log(10));
	
			if($type == 0)$chrselect = " chr = $chr AND ";
			else $chrselect = " ";

	
	
		
			$q = "
			SELECT 
				pval, v_ind, chr, alias, pos , ind
			FROM 
				".$this->module( $module ).".pval
			JOIN 
				".$this->module( $module ).".v_ind USING ( v_ind )
			JOIN 
				".$this->module( $module ).".ind USING ( ind )
			JOIN 
				".$this->module( $module ).".alias USING(ind)
			JOIN 
				".$this->module( $module ).".pos USING(ind)
			WHERE 
				$chrselect pval < $searchTerm
			AND 
				pval > 0
			ORDER BY 
				pval ASC
			LIMIT 100
			"; 
			$stmt = $this->db->prepare($q);
			$stmt->execute();
	
	
			if($stmt->rowCount() == 0)
			{
				return FALSE;
			}
 
	 
			while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
			{
	 
			   $curchr=$rowch['chr'] + 0;
	   
 
				$result[$j][ 0 ] = $this->addSpace($rowch['alias'], '&nbsp;&nbsp;', 13).$this->addSpace($rowch['chr'], '&nbsp;&nbsp;', 4).$this->addSpace($this->addCommas($rowch['pos']), '&nbsp;&nbsp;', 13)."  -log(P)=". -round(log10($rowch['pval']),5);
				$result[$j][ 1 ] = $rowch["ind"] - $chrsupp[$curchr];
				$result[$j][ 2 ] = $rowch['chr'];

			$j++;
			}  
			

			
		}

	
			return $result;
		
		}
		
		
		
		private function addCommas($nStr)
		{
		
			$i=strlen($nStr);
			while(isset($i) && $i != -1)
			{
		
				$new = "$nStr[$i]"."$new";
				if((strlen($nStr)-$i)%3 == 0 && strlen($nStr) != $i && $i != 0)$new = ','.$new;
				$i--;
			}
			
		return $new;
		}
		
		
	private function addRect($dom, $parent, $left, $color)
	{
		$box = $dom->createElement("use", null);
		$box->setAttribute("xlink:href", "#".$color);
		$box->setAttribute("x", $left);
		$parent->appendChild($box);
	}

	private function addDefBox($dom, $parent, $id, $color)
	{
		$box = $dom->createElement( 'rect', null);
		
		$box->setAttribute('id', $id);
		$box->setAttribute('width', 10);
		$box->setAttribute('height', 10);
		$box->setAttribute('fill', $color);
		$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');

		$parent->appendChild($box);
	}

	private function getColorPValue($logP)
	{

		// if ( !isset( $logP ) ) // missing data

		if ($logP == - 1) // missing data
		{
			return 'w';
		}

		if ($logP == 0)
		{
			return 'g';
		}

		if ($logP < - 5)
		{
			return 'bl';
		}

		if ($logP < - 4)
		{
			return 'b';
		}

		if ($logP < - 3)
		{
			return 'r';
		}

		if ($logP < - 2)
		{
			return 'o';
		}

		if ($logP < - 1.3)
		{
			return 'y';
		}

		return 'g';
	}

	public function generateGWASArray($request)
	{
		$reportRow = $request->query->get('row');
		if (!isset($reportRow))
		{
			$this->ReturnError("No row specified");
		}
	
	
	 
		$chr = $request->query->get('chr');
		if (!isset($chr))
		{
			$this->ReturnError("No chromosome specified");
		}

		$fileName = trim($request->query->get('module'));
		if (!$fileName)
		{
			$this->ReturnError("No module specified");
		}

		$file = $fileName;

		if (!$file)
		{
			$this->ReturnError("Could not open table.");
		}
		
		$stageMask = $request->query->get('stageMask');
 

		$BuildAndPlatform = $this->GetBuildAndPlatform($fileName);
		$colInfo = $this->ReadColumnInfo($file, $stageMask);
	//print_r($colInfo);
		$numColumns = $colInfo["NumColumns"];
		$numDataColumns = $colInfo["NumDataColumns"];
		$rowCount = $this->ReadRowCount($file,$chr);
		$rowDataStart = $reportRow - 50;
		if ($rowDataStart < 0) $rowDataStart = 0;
		$rowDataCount = 100;

		// false means return the data as logP

		$dataBlock = $this->ReadRowData($file, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, false, 1.0);

		// echo json_encode($dataBlock);

		$topOfBlocks = 200;
		$leftOfBlocks = 250;
		
		$dom = new \DOMDocument('1.0', 'utf-8');

		$dom->formatOutput = true; // TODO, remove this to save bandwidth later

		$svg = $dom->createElement( 'svg',  null);
		
		$svg->setAttribute('width', '29.7cm');
		$svg->setAttribute('height', '21.0cm');
		$svg->setAttribute('preserveAspectRatio', 'xMinYMin meet');
		$svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
		$svg->setAttribute('xmlns:xlink', "http://www.w3.org/1999/xlink");
		$svg->setAttribute('version', '1.1');
		
		
		$defs = $dom->createElement('defs');
		$this->addDefBox($dom, $defs, 'r', 'red');
		$this->addDefBox($dom, $defs, 'w', 'white');
		$this->addDefBox($dom, $defs, 'g', 'lightgrey');
		$this->addDefBox($dom, $defs, 'y', '#f7f5ca');
		$this->addDefBox($dom, $defs, 'o', 'orange');
		$this->addDefBox($dom, $defs, 'b', 'blue');
		$this->addDefBox($dom, $defs, 'bl', 'black');
		$svg->appendChild($defs);
		$style = $dom->createElement('style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
		$svg->appendChild($style);
	
		$rows = $dataBlock["Rows"];
		$numRows = count($rows);
	
		$relativeRow = $reportRow - $rowDataStart;
		for ($i = 0; $i < $numRows; ++$i)
		{
			$rowName = $rows[$numRows - $i - 1]["Name"];
			$maf = sprintf("%01.5f", $rows[$numRows - $i - 1]["MAF"]);
			$text = $dom->createElement( 'text', $rowName);
			
			$text->setAttribute("x", 5);
			$text->setAttribute("y", $topOfBlocks + 8 + $i * 10);
			
			if ($i == $relativeRow)
			{
				$reportRowName = $rowName;
			}

			$svg->appendChild($text);
			$text = $dom->createElement('text', $maf );
			$text->setAttribute("x", $leftOfBlocks / 2 + 20);
			$text->setAttribute("y", $topOfBlocks + 8 + $i * 10);
			$text->setAttribute('text-anchor', 'end');
			
			
			$svg->appendChild($text);
			$text = $dom->createElement( 'text', number_format($rows[$numRows - $i - 1]["Coords"]) );
			$text->setAttribute("x", $leftOfBlocks - 20);
			$text->setAttribute("y", $topOfBlocks + 8 + $i * 10);
			$text->setAttribute('text-anchor', 'end');

			$svg->appendChild($text);
		}

		$title = $dom->createElement( 'title', "Report for Module $fileName - SNP $reportRowName");
			$title->setAttribute('x', 10);
			$title->setAttribute('y', 18);
			$title->setAttribute('style', 'font-size:30pt');
		
		$svg->appendChild($title);
		
		$text = $dom->createElement( "text", "2D Snapshot for SNP $reportRowName");
			$text->setAttribute('x', 10);
			$text->setAttribute('y', 18);
			$text->setAttribute('style', 'font-size:11pt');
		$svg->appendChild($text);



		$cornerBox = $dom->createElement( "text", null );
		$cornerBox->setAttribute('x', 10);
		$cornerBox->setAttribute('y', 25);
		$cornerBox->setAttribute('style', 'font-size:10pt');
		$svg->appendChild($text);		
		
		
		
		$text = $dom->createElement( "tspan", "Date:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		if($BuildAndPlatform['build'] !=''){
			$text = $dom->createElement("tspan", "Build:");
			$text->setAttribute('x', 10);
			$text->setAttribute('dy', '1.2em');
			$cornerBox->appendChild($text);
		 
		}
		if(trim($BuildAndPlatform['platform']) !=''){
			$text = $dom->createElement("tspan", "Platform:");
			
			$cornerBox->appendChild($text);
			$text->setAttribute('x', 10);
			$text->setAttribute('dy', '1.2em');
		}
		$text = $dom->createElement("tspan", "Chr:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text );
		$text = $dom->createElement("tspan", "From:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		$dom->createElement("tspan", "To:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$svg->appendChild($cornerBox);
		
		$cornerBox = $dom->createElement("text", null);
		$cornerBox->setAttribute('x', '85');
		$cornerBox->setAttribute('y', '25');
		$cornerBox->setAttribute('style', 'font-size:10pt');
		$text = $dom->createElement("tspan", date("Y-m-d"));
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", "$BuildAndPlatform[build]");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", "$BuildAndPlatform[platform]");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", "$chr");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", number_format($rows[0]["Coords"]));
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$svg->appendChild($cornerBox);
		$colIndex = 0;
		$gap = false;
		$colHeadingPos = $topOfBlocks - 10;
		

		for ($i = 0; $i < $numColumns; ++$i)
 
		{
			$dataColumn = $colInfo[$i]["DataColumn"];
			if ($dataColumn == - 1)
			{
				if ($gap == false)
				{
					$gap = true;
					$dataColumns[$colIndex] = $dataColumn;
					++$colIndex;
				}

				continue;
			}

			$dataColumns[$colIndex] = $dataColumn;
			$offset = $leftOfBlocks + $colIndex * 10 + 10;
			$gap = false;
 
			$text = $dom->createElement( 'text', $colInfo[$i]["Name"]);
			$text->setAttribute("transform", "translate( $offset, $colHeadingPos ) rotate( -80 ) ");
			$text->setAttribute('style', 'font-size:7pt');

			$svg->appendChild($text);
			++$colIndex;
		} 
		
		for ($row = 0; $row < $numRows; ++$row)
		{
			$top = $topOfBlocks + $row * 10;
			$group = $dom->createElement( 'g', null );
			$group->setAttribute('transform', "translate(0,$top)");
			// if($rowDataStart == 0) $ro = $row + 1;
			// else $ro = $row;

			$currentRowData = $dataBlock[$numRows - $row - 1];
		 
			for ($i = 0; $i < $colIndex; ++$i)
		 
			{
				$dc = $dataColumns[$i];
				if ($dc == - 1)
				{
					continue;
				}

				$logP = (int)$currentRowData[$dc][0];
				$this->addRect($dom, $group, $leftOfBlocks + $i * 10, $this->getColorPValue($logP));
			}

			$svg->appendChild($group);
		}

	$maxX = $leftOfBlocks + $numColumns * 10 + 30;
	$maxY = $topOfBlocks + $numRows * 10 + 80;
	$top = $topOfBlocks + $numRows * 10 + 20;
	$group = $dom->createElement('g', null);
		$group->setAttribute('transform', "translate(0,$top)");
 
	$labels = array(
		'' => 'P-value',
		'g' => '>0.05',
		'y' => '0.01 - 0.05',
		'o' => '0.001 - 0.01',
		'r' => '0.0001 - 0.001',
		'b' => '0.00001 - 0.0001',
		'bl' => '<0.00001',
		'w' => 'Missing'
	);
	$labelIdx = 0;
	foreach($labels as $c => $d)
	{
		if ($c != '')
		{
			$this->addRect($dom, $group, $leftOfBlocks + $labelIdx * 180, $c);
		}

		$xPos = $leftOfBlocks + 15 + $labelIdx * 180;
		$text = $dom->createElement('text', $d); 
			$text->setAttribute('x', $xPos);
			$text->setAttribute('style', 'font-size:12pt');
			$text->setAttribute('y', 10);
		
		$maxX = max($maxX, $xPos + 180);
		$group->appendChild($text);
		++$labelIdx;
	}

	$svg->appendChild($group);
	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', 0);
		$rect->setAttribute('y', 0);
		$rect->setAttribute('width', $leftOfBlocks);
		$rect->setAttribute('height', $topOfBlocks - 5);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);
	
	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', $leftOfBlocks);
		$rect->setAttribute('y', 0);
		$rect->setAttribute('width', $maxX - $leftOfBlocks);
		$rect->setAttribute('height', $topOfBlocks - 5);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', 0);
		$rect->setAttribute('y', $topOfBlocks - 5);
		$rect->setAttribute('width', $leftOfBlocks);
		$rect->setAttribute('height', $numRows * 10 + 10);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', $leftOfBlocks);
		$rect->setAttribute('y', $topOfBlocks - 5);
		$rect->setAttribute('width', $maxX - $leftOfBlocks);
		$rect->setAttribute('height', $numRows * 10 + 10);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', 0);
		$rect->setAttribute('y', $topOfBlocks + $numRows * 10 + 5);
		$rect->setAttribute('width', $maxX);
		$rect->setAttribute('height', 35);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);
	 
	$svg->setAttribute('viewBox', "0 0 $maxX $maxY");
	$dom->appendChild($svg);
	//fclose($file);
	return $dom->saveXML();
	
	}

private function getColorOddsRatio($oddsRatio)
{
	if ($oddsRatio == - 1.0)
	{
		return "rgb(0,0,255)";;
	}

	if ($oddsRatio < 0.5)
	{
		return "rgb(0,255,0)";
	}
	else
	if ($oddsRatio < 0.9)
	{
		return "rgb(127,255,127)";
	}
	else
	if ($oddsRatio < 1.1)
	{
		return "rgb(255,255,255)";
	}
	else
	if ($oddsRatio < 2.0)
	{
		return "rgb(255, 127, 127)";
	}
	else

	// > 2.0

	{
		return "rgb(255,0,0)";
	}
}

private function getColorOddsRatioLighter($oddsRatio)
{
	if ($oddsRatio == - 1.0)
	{
		return "rgb(50,50,255)";;
	}

	if ($oddsRatio < 0.5)
	{
		return "rgb(50,255,50)";
	}
	else
	if ($oddsRatio < 0.9)
	{
		return "rgb(177,255,177)";
	}
	else
	if ($oddsRatio < 1.1)
	{
		return "rgb(255,255,255)";
	}
	else
	if ($oddsRatio < 2.0)
	{
		return "rgb(255, 177, 177)";
	}
	else
	if ($oddsRatio == 2.0)
	{
		return "rgb(0,0,0)";
	}
	else

	// > 2.0

	{
		return "rgb(255,50,50)";
	}
}

private function getColorOddsRatioDarker($oddsRatio)
{
	
	if ($oddsRatio == - 1.0)
	{
		return "rgb(0,0,205)";;
	}

	if ($oddsRatio < 0.5)
	{
		return "rgb(0,205,0)";
	}
	else
	if ($oddsRatio < 0.9)
	{
		return "rgb(77,205,77)";
	}
	else
	if ($oddsRatio < 1.1)
	{
		return "rgb(205,205,205)";
	}
	else
	if ($oddsRatio < 2.0)
	{
		return "rgb(205, 77, 77)";
	}
	else

	// > 2.0

	{
		return "rgb(205,0,0)";
	}
}


// 3D snapshot

public function generateGWASSnapshot($request, $polarized)
{


		$reportRow = $request->query->get('row');
		if (!isset($reportRow))
		{
			$this->ReturnError("No row specified");
		}
	
	
	 
		$chr = $request->query->get('chr');
		if (!isset($chr))
		{
			$this->ReturnError("No chromosome specified");
		}

		$fileName = trim($request->query->get('module'));
		if (!$fileName)
		{
			$this->ReturnError("No module specified");
		}

		$file = $fileName;

		
		$stageMask = $request->query->get('stageMask');
	
		if (!isset($stageMask))
		{
			$stageMask = 0xffffffff;
		}

 	if ($polarized)
	{
	 
		//$meta = $this->parseMeta();
		//$polFile = $this->getPolarization($file);
		//$polData = $this->getPolarizationData($file, $chr, $reportRow);
		//ReturnError($polData);
	}
 
	// ReadVersionAndMagic( $file );
	$BuildAndPlatform = $this->GetBuildAndPlatform($fileName);
	$colInfo = $this->ReadColumnInfo($file);
	$numColumns = $colInfo["NumColumns"];
	$numDataColumns = $colInfo["NumDataColumns"];
	$rowCount = $this->ReadRowCount($file, $chr);
	$rowDataStart = $reportRow - 40;
	if ($rowDataStart < 0) $rowDataStart = 0;
	$rowDataCount = 80;
	$heightScale = 20;

	// false means return the data as height

	$dataBlock = $this->ReadRowData($file, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, 1, $heightScale * 1.5);
	$topOfBlocks = 40;
	$leftOfBlocks = 400;
	$dom = new \DOMDocument('1.0', 'utf-8');

//	$dom->formatOutput = true; // TODO, remove this to save bandwidth later
	$svg = $dom->createElement( 'svg',  null);
		
	$svg->setAttribute('width', '29.7cm');
	$svg->setAttribute('height', '21.0cm');
	$svg->setAttribute('preserveAspectRatio', 'xMinYMin meet');
	$svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
	$svg->setAttribute('xmlns:xlink', "http://www.w3.org/1999/xlink");
	$svg->setAttribute('version', '1.1');

	$defs = $dom->createElement( 'defs');
	$this->addDefBox($dom, $defs, 'w', 'white');
	$this->addDefBox($dom, $defs, 'g', 'lightgrey');
	$svg->appendChild($defs);
	$style = $dom->createElement( 'style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
	$svg->appendChild($style);
	$rows = $dataBlock["Rows"];
	$numRows = count($rows);
	$relativeRow = $reportRow - $rowDataStart;
	$repTitle = "3D Snapshot ";
	if ($polarized)
	{
		$repTitle.= " - Polarized";
	}
	
	$text = $dom->createElement("text", $repTitle);
	  
	$text->setAttribute('x', 10);
	$text->setAttribute('y', 25);
	$text->setAttribute('style', 'font-size:16pt');
	$svg->appendChild($text);
	
	$cornerBox = $dom->createElement( "text", null);
	$cornerBox->setAttribute('x', 10);
	$cornerBox->setAttribute('y', 45);
	$cornerBox->setAttribute('style', 'font-size:14pt');
	 
	$text = $dom->createElement( "tspan", "Date:");
	$text->setAttribute('x', 10);
	$text->setAttribute('dy', '1.1em');
	$cornerBox->appendChild($text);
 
	if($BuildAndPlatform['build'] !=''){
		$text = $dom->createElement(  "tspan", "Build:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);
 	}
	if($BuildAndPlatform['platform'] !=''){
 		$text = $dom->createElement(  "tspan", "Platform:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);
	}
 
 		$text = $dom->createElement(  "tspan", "Module:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 		$text = $dom->createElement(  "tspan", "Chr:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 
 		$text = $dom->createElement(  "tspan", "From:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


 		$text = $dom->createElement(  "tspan", "To:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


 		$text = $dom->createElement(  "tspan", "Gene:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


	$svg->appendChild($cornerBox);



	$cornerBox = $dom->createElement("text", null);
	$cornerBox->setAttribute('x', 110);
	$cornerBox->setAttribute('y', 45);
	$cornerBox->setAttribute('style', 'font-size:14pt');
 
 	$text = $dom->createElement("tspan", date("Y-m-d"));
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan", "$BuildAndPlatform[build]");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan",  "$BuildAndPlatform[platform]");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan", "$fileName");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
 
 	$text = $dom->createElement("tspan", "$chr");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	

 
	if ($polarized)
	{
		$text = $dom->createElement( "tspan", $rows[$reportRow - $rowDataStart]["Name"]);
			$text->setAttribute('x', 110);
			$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);
	}


 	$text = $dom->createElement("tspan", number_format($rows[0]["Coords"]));
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan",  number_format($rows[$numRows - 1]["Coords"]));
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan", $rows[$reportRow - $rowDataStart]["Gene"]);
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	



 
	$svg->appendChild($cornerBox);
	$maxX = $leftOfBlocks + $numColumns * 10 + 200;
	$maxY = $topOfBlocks + $numRows * 10 + 180;
	
	
	$rect = $dom->createElement( "rect", null );
	$rect->setAttribute('x', 2);
	$rect->setAttribute('y', 2);
	$rect->setAttribute('width', $maxX - 10);
	$rect->setAttribute('height', $maxY - 10);
	$rect->setAttribute('fill', 'none');
	$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$colIndex = 0;
	$gap = false;
	$colHeadingPos = $topOfBlocks - 10 + ($numRows * 10) + 20;
	$skewValue = 20;
	$tanOfSkew = tan(deg2rad($skewValue));
	$rowSkewOffset = 10 * $tanOfSkew;
	for ($i = 0; $i < $numColumns; ++$i)
	{
		$dataColumn = $colInfo[$i]["DataColumn"];
		if ($dataColumn == - 1)
		{
			if ($gap == false)
			{
				$gap = true;
				$dataColumns[$colIndex] = $dataColumn;
				++$colIndex;
			}

			continue;
		}

		$dataColumns[$colIndex] = $dataColumn;
		$offset = $leftOfBlocks + $colIndex * 10 - ($rowSkewOffset * $numRows) - $tanOfSkew * $topOfBlocks + 8;
		$gap = false;
		$rotate = 90 - $skewValue;
		$text = $dom->createElement( 'text', $colInfo[$i]["Name"]);
		$text->setAttribute("transform", "translate( $offset, $colHeadingPos ) rotate( -$rotate ) ");
		$text->setAttribute('text-anchor', 'end');
		$text->setAttribute('style', 'font-size:7pt');
		 
		$svg->appendChild($text);
		++$colIndex;
	}

	// add row things on the end

	$startRowName = $leftOfBlocks + ($colIndex * 10) - 10;
	$rowIndex = 0;
	for ($i = $numRows - 1; $i >= 0; --$i)
	{
		$rowName = $rows[$i]["Name"];
		$text = $dom->createElement( 'text', $rowName);
			$text->setAttribute("x", $startRowName);
			$text->setAttribute("y", $topOfBlocks + 8 + $rowIndex * 10);
	 
		if ($i == $relativeRow)
		{
			$reportRowName = $rowName;
		}

		$svg->appendChild($text);
		$rowName = $rows[$i]["Name"];
		$text = $dom->createElement( 'text', number_format($rows[$i]["Coords"]));
			$text->setAttribute("x", $startRowName + 90);
			$text->setAttribute("y", $topOfBlocks + 8 + $rowIndex * 10);
	 
		if ($i == $relativeRow)
		{
			$reportRowName = $rowName;
		}

		$svg->appendChild($text);
		$rowName = $rows[$i]["Name"];
		if ($rows[$i]["MAF"] == 0 || $rows[$i]["MAF"] == - 1) $rows[$i]["MAF"] = '';
		$text = $dom->createElement( 'text', $rows[$i]["MAF"]);
			$text->setAttribute("x", $startRowName + 170);
			$text->setAttribute("y", $topOfBlocks + 8 + $rowIndex++ * 10);
	 
		if ($i == $relativeRow)
		{
			$reportRowName = $rowName;
		}

		$svg->appendChild($text);
		$startRowName-= $rowSkewOffset;
	}

	$titleString = "Report for Module $fileName - SNP $reportRowName";
	if ($polarized)
	{
		$titleString.= " - Polarized";
	}

	$title = $dom->createElement( 'title', $titleString);
	$svg->appendChild($title);
	$outerGroup = $dom->createElement( 'g', null);
	$outerGroup->setAttribute('transform', "skewX(-$skewValue)");
	 
	$rowIndex = 0;
	for ($row = $numRows - 1; $row >= 0; --$row)
	{
		$top = $topOfBlocks + $rowIndex++ * 10;
		$group = $dom->createElement( 'g', null);
			$group->setAttribute('transform', "translate(0,$top)");
	 
		$rowMod = ($row % 5) == 0;
		$currentRowData = $dataBlock[$row];
		for ($i = 0; $i < $colIndex; ++$i)
		{
			$dc = $dataColumns[$i];
			if ($dc == - 1)
			{
				continue;
			}

			$this->addRect($dom, $group, $leftOfBlocks + $i * 10, (($i % 5) == 0 || $rowMod) ? "w" : "g");
		}

		$outerGroup->appendChild($group);
	}

	$svg->appendChild($outerGroup);
	$rowIndex = 0;
	for ($row = $numRows - 1; $row >= 0; --$row)
	{
		$top = $topOfBlocks + $rowIndex++ * 10;
		$rowMod = ($row % 5) == 0;
		$currentRowData = $dataBlock[$row];
		$fipOddsRatio = false;
		$grey = false;
		$white = false;
		if ($polarized)
		{
			$numPolSamples = $polData["NumPolSamples"];
			$rowRelativeToPolData = ($rowDataStart + $row) - $polData["Row"] + (($numPolSamples - 1) / 2);
			if (($rowRelativeToPolData < 0) || ($rowRelativeToPolData >= $numPolSamples))
			{

				// hide row, outside polarisation region

				continue;
			}

			$polDataForRow = $polData[$rowRelativeToPolData];
			if ($polDataForRow == - 3) // not in LD with anything
			{
				$grey = true;
			}

			if ($polDataForRow == - 2) // not in LD with anything
			{
				$white = true;
			}

			$fipOddsRatio = ($polDataForRow == - 1);
		}

		for ($i = 0; $i < $colIndex; ++$i)
		{
			$dc = $dataColumns[$i];
			if ($dc == - 1)
			{
				continue;
			}

			$dataPoint = $currentRowData[$dc];
			if (isset($dataPoint))
			{
				$height = $dataPoint[0];
				$odds = $dataPoint[1];
				if ($fipOddsRatio)
				{
					$odds = 1.0 / $odds;
				}

				$leftBar = $leftOfBlocks + $i * 10 - ($rowSkewOffset * $rowIndex) - $topOfBlocks * $tanOfSkew;
				$topBar = $top - $height + 10;
				$group = $dom->createElement( "g", null);
					$group->setAttribute("transform", "translate( $leftBar, $topBar )");
			 
				if (!$grey)
				{
					$color = $this->getColorOddsRatio($odds);
				}
				else
				{
					$color = "rgb(178,178,178)";
				}

				if ($white)
				{
					$color = "rgb(0,0,0)";
				}

				$bar = $dom->createElement("rect", null);
					$bar->setAttribute("width", 10);
					$bar->setAttribute("height", $height);
					$bar->setAttribute('fill', $color);
					$bar->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
			 
				$group->appendChild($bar);
				if (!$grey)
				{
					$color = $this->getColorOddsRatioLighter($odds);
				}
				else
				{
					$color = "rgb(228,228,228)";
				}

				if ($white)
				{
					$color = "rgb(0,0,0)";
				}

				$barTop = $dom->createElement( "rect", null);
					$barTop->setAttribute("width", 10);
					$barTop->setAttribute("height", 10);
					$barTop->setAttribute("y", - 10);
					$barTop->setAttribute("x", 0);
					$barTop->setAttribute('fill', $color);
					$barTop->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
					$barTop->setAttribute('transform', "skewX(-$skewValue)");
			 
				$group->appendChild($barTop);
				if (!$grey)
				{
					$color = $this->getColorOddsRatioDarker($odds);
				}
				else
				{
					$color = "rgb(128,128,128)";
				}

				if ($white)
				{
					$color = "rgb(0,0,0)";
				}

				$rs = $rows[$row]["Name"];
				if (preg_match('/-1/', $rs))
				{
					$color = 'rgb(1,1,1)';
				}

				$tanOfSkewPlus10 = 10 + $tanOfSkew * 10;
				$heightMinusTen = $height - 10;
				$barSide = $dom->createElement( "polygon", null);
					$barSide->setAttribute("points", "10,0  $tanOfSkewPlus10,-10  $tanOfSkewPlus10, $heightMinusTen  10, $height");
					$barSide->setAttribute('fill', $color);
					$barSide->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
				 
				$group->appendChild($barSide);
				$svg->appendChild($group);
			}
		}
	}

	$text = $dom->createElement( 'text', 'QAS');
		$text->setAttribute("x", 10);
		$text->setAttribute("y", 240);
		$text->setAttribute('style', 'font-size:15pt');
	$svg->appendChild($text);
	$labels = array(
		"0.1" => '<0.5',
		"0.7" => '0.5 - 0.9',
		"1.0" => '0.9 - 1.1',
		"1.5" => '1.1 - 2.0',
		"3.0" => '>2.0',
		"-1.0" => 'Undefined',
	);
	$labelIdx = 0;
	foreach($labels as $cm => $d)
	{
		 
		$top = $labelIdx * 30 + 250;
		$group = $dom->createElement( 'g', null);
			$group->setAttribute('transform', "translate(20,$top), scale(1.1)");
	 
		$box = $dom->createElement( "rect", null);
			$box->setAttribute("width", 30);
			$box->setAttribute("height", 10);
			$box->setAttribute("fill", $this->getColorOddsRatio($cm) );
			$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
		 
		$group->appendChild($box);
		$text = $dom->createElement('text', $d);
			$text->setAttribute('x', 45);
			$text->setAttribute('style', 'font-size:12pt');
			$text->setAttribute('y', 10);
		
		$group->appendChild($text);
		++$labelIdx;
		$svg->appendChild($group);
	}

	$text = $dom->createElement('text', "P-value");
		$text->setAttribute("x", 10);
		$text->setAttribute("y", 450);
		$text->setAttribute('style', 'font-size:15pt');
	$svg->appendChild($text);
	$labels = array(
		"0.00001" => 3,
		"0.0001" => 3,
		"0.001" => 3,
		"0.01" => 1,
		"" => 4,
		">0.01" => 1,
	);
	$labelIdx = 0;
	foreach($labels as $c => $d)
	{
		$top = $labelIdx * $heightScale + 470;
		$group = $dom->createElement( 'g', null);
			$group->setAttribute('transform', "translate(20,$top)");
		 
		$text = $dom->createElement('text', $c);
			$text->setAttribute('x', 65);
			$text->setAttribute('style', 'font-size:12pt');
			$text->setAttribute('text-anchor', 'end');
			$text->setAttribute('y', 10);
		 
		$group->appendChild($text);
		if ($d & 1)
		{
			$text = $dom->createElement('line', null);
				$text->setAttribute('x1', 70);
				$text->setAttribute('y1', 5);
				$text->setAttribute('x2', 80);
				$text->setAttribute('y2', 5);
				$text->setAttribute('style', 'stroke:rgb(0,0,0);stroke-width:2');
			 $group->appendChild($text);
		}

		if ($d & 2)
		{
			$text = $dom->createElement('line', null);
				$text->setAttribute('x1', 80);
				$text->setAttribute('y1', 5);
				$text->setAttribute('x2', 80);
				$text->setAttribute('y2', 5 + $heightScale);
				$text->setAttribute('style', 'stroke:rgb(0,0,0);stroke-width:2');
			 $group->appendChild($text);
		}

		if ($d & 4)
		{
			$text = $dom->createElement('line', null);
				$text->setAttribute('x1', 80);
				$text->setAttribute('y1', 5 - $heightScale);
				$text->setAttribute('x2', 80);
				$text->setAttribute('y2', 5 + $heightScale);
				$text->setAttribute('style', 'stroke:rgb(0,0,0);stroke-width:2;stroke-dasharray: 5, 5');
			 $group->appendChild($text);
		}

		++$labelIdx;
		$svg->appendChild($group);
	}

	$svg->setAttribute('viewBox', "0 0 $maxX $maxY");
	$dom->appendChild($svg);
	fclose($file);
	return $dom->saveXML();
}



	
}  
