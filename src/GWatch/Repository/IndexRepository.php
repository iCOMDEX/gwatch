<?php
namespace GWatch\Repository;

use Doctrine\DBAL\Connection;
use GWatch\Entity\Module;
/**
 * Index repository
 */
class IndexRepository
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
  
	protected function getChr($module){
	
		$chr = array();
		$chrnum = false;
		$sql = "
 		SELECT 
 			* 
 		FROM 
 			".$this->module( $module ).".`chr` 
 		ORDER BY 
 			chr;";

 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 		
		while ($md  = $stmt->fetch(\PDO::FETCH_ASSOC)){
		
			$chrnum = $md['chr'];
			$chr[$chrnum] = $md['chrname'];
		
		}

		return $chr;
		
	}
	protected function getTests($module){
	
		$col = array();
		$colnum = false;
		$sql = "
 		SELECT 
 			* 
 		FROM 
 			".$this->module( $module ).".`col` 
 		ORDER BY 
 			col;";

 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 		
		while ($md  = $stmt->fetch(\PDO::FETCH_ASSOC)){
		
			$colnum = $md['col'];
			$col[$colnum] = $md['test'];
		
		}

		return $col;
		
	}
  
	public function findModules(){
		
		$module = array();
		$modules = array();
		$name = false;
		$sql = "
 		SELECT 
 			* 
 		FROM 
 			".$this->dbmanagemant.".`module` 
 		ORDER BY 
 			id;";
 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 		
		while ($md  = $stmt->fetch(\PDO::FETCH_ASSOC)){
		
		
			$module['id'] = $md['id'];
			$module['description'] = $md['description'];
			$module['name'] = $md['name'];
			$module['chr'] = $this->getChr($md['name']);
			$module['test'] = $this->getTests($md['name']);
			$name = $md['name'];
			$modules[$name] = $this->buildModule($module);
		
		
		}	
		
		return $modules;
		
		
	}
	private function buildModule($module){
		
		$Module = new Module();
	
		$Module->setId($module['id']);
		$Module->setName($module['name']);
		$Module->setDescription($module['description']);
		$Module->setChr($module['chr']);
		$Module->setTest($module['test']);
		 
		return $Module;
	
	}

	
}  
