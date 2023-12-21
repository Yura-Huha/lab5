<?php
	ini_set('display_errors', 1); 
	ini_set('display_startup_errors', 1); 
	error_reporting(E_ALL);
	abstract class BaseList{
		protected $dataArray;
		protected $index;
		public function __construct(){
			$this->dataArray=[];
			$this->index=0;
		}
		public function convertToJSON(){
			header("Content-type: application/json");
			$jsonArray=[];
			for ($i=0; $i<count($this->dataArray);$i++){
				array_push($jsonArray,$this->dataArray[$i]->getAsJSONObject());
			}
			return json_encode($jsonArray,JSON_UNESCAPED_UNICODE);
		}
		public function getTable(){
			$tableContent='';
			for ($i=0; $i<count($this->dataArray);$i++){
				$tableContent.=$this->dataArray[$i]->getDataAsTableRow();
			}
			return $tableContent;
		}
		public function showAll(){
			for ($i=0; $i<count($this->dataArray);$i++){
				echo $this->dataArray[$i]->displayInfo();
			}
		}
		public abstract function importFromFile($fileName);
		public function delete($id){
			for ($i=0; $i<count($this->dataArray);$i++){
				if ($this->dataArray[$i]->getId()==$id){
					array_splice($this->dataArray,$i,1);
					break;
				}
			}
		}
		public function exportToFile($fileName){
			if (($handle = fopen($fileName, "w")) !== FALSE) {
				for ($i=0; $i<count($this->dataArray);$i++){
					fwrite($handle,$this->dataArray[$i]->getDataAsCSVRow());
				}
				fclose($handle);
			}
			
		}
	}
	class CategoryList extends BaseList{
		public function importFromFile($fileName){
			$row = 1;
			if (($handle = fopen($fileName, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$this->add($data[0]);
				$row++;	
			}
			fclose($handle);
			}
		}
		public function getDataAsXML(){
			header("Content-type: text/xml");
			$result='<?xml version="1.0" encoding="UTF-8"?>
			<categories>';
			for ($i=0; $i<count($this->dataArray);$i++){
				$result.=$this->dataArray[$i]->getDataAsXML();
			}
			$result.='</categories>';
			return $result;
		}
		public function getDataAsSelect(){
			$result='<select name="category">';
			for ($i=0; $i<count($this->dataArray);$i++){
				$result.=$this->dataArray[$i]->getDataAsOption();
			}
			$result.='</select>';
			return $result;
		}
		public function add($name){
			$id=++$this->index;
			$nc=new Category($id,$name);
			array_push($this->dataArray,$nc);
			return $id;
		}
		public function edit($id,$name){
			for ($i=0; $i<count($this->dataArray);$i++){
				if ($this->dataArray[$i]->getId()==$id){
					$this->dataArray[$i]->edit($name);
					break;
				}
			}
		}
	}
	class Category{
		private $id;
		private $name;
		public function __construct($id, $name){
			$this->id=$id;
			$this->name=$name;		
		}
		public function getId(){
			return $this->id;
		}
		public function edit($name){
			$this->name=$name;
		}
		public function getDataAsXML(){
			return "
				<category>
					<id>".$this->id."</id>
					<name>".$this->name."</name>
				</category>
			";
		}
		public function getDataAsOption(){
			return "<option value='".$this->name."'>".$this->name."</option>";
		}
		public function getDataAsTableRow(){
			return "
				<tr>
					<td>".$this->id."</td>
					<td>".$this->name."</td>
				</tr>
			";
		}
		public function displayInfo(){
			return $this->id.". ".$this->name."</br>";
		}
		public function getDataAsCSVRow(){
			return '"'.addslashes($this->name).'"'."\n";
		}
		public function __destruct(){
			echo "";	
		}
		public function getAsJSONObject(){
			return get_object_vars($this);
		}
	}
	/*$cl=new CategoryList();
	$cl->importFromFile('categories.csv');
	echo $cl->getDataAsXML();*/
	/*$cl->showAll();
	$cl->add('Велосипедні рюкзаки');
	$cl->exportToFile('categories.csv');
	$cl->add('Міські рюкзаки');
	$cl->add('Похідні рюкзаки');
	$cl->edit(1,'Рюкзаки для альпіністів');
	$cl->add('Велосипедні рюкзаки');
	$cl->delete(2);
	$cl->showAll();*/
	class EbookList extends BaseList{
		public function add($brand, $model, $category, $price, $properties){
			$id=++$this->index;
			$nb=new Ebook($id,$brand, $model, $category, $price, $properties);
			array_push($this->dataArray,$nb);
			return $id;
		}
		public function getDataAsXML(){
			header("Content-type: text/xml");
			$result='<?xml version="1.0" encoding="UTF-8"?>
			<ebooks>';
			for ($i=0; $i<count($this->dataArray);$i++){
				$result.=$this->dataArray[$i]->getDataAsXML();
			}
			$result.='</ebooks>';
			return $result;
		}
		public function importFromFile($fileName){
			$row = 1;
			if (($handle = fopen($fileName, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				eval('$propsArray='.$data[4].';');
				$this->add($data[0],$data[1],$data[2],$data[3],$propsArray);
				$row++;	
			}
			fclose($handle);
			}
		}
		public function edit($id,$brand, $model, $category, $price, $properties){
			for ($i=0; $i<count($this->dataArray);$i++){
				if ($this->dataArray[$i]->getId()==$id){
					$this->dataArray[$i]->edit($brand, $model, $category, $price, $properties);
					break;
				}
			}
		}
	}
	class Ebook{
		private $id;
		private $model;
		private $brand;
		private $category;
		private $price;
		private $properties;
		public function __construct($id, $brand, $model, $category, $price, $properties){
			$this->id=$id;
			$this->brand=$brand;	
			$this->model=$model;
			$this->category=$category;
			$this->price=$price;
			$this->properties=$properties;	
		}
		public function getId(){
			return $this->id;
		}
		public function getDataAsCSVRow(){
			return '"'.addslashes($this->brand).'","'.addslashes($this->model).'","'.addslashes($this->category).'","'.addslashes($this->price).'","'.$this->getPropertiesForCSV().'"'."\n";
		}
		public function getDataAsXML(){
			return "
				<ebook>
					<id>".$this->id."</id>
					<brand>".$this->brand."</brand>
					<model>".$this->model."</model>
					<category>".$this->category."</category>
					<price>".$this->price."</price>
					<properties>".$this->getPropertiesAsXML()."</properties>
				</ebook>
			";
		}
		public function getDataAsTableRow(){
			return "
				<tr>
					<td>".$this->id."</td>
					<td>".$this->brand."</td>
					<td>".$this->model."</td>
					<td>".$this->category."</td>
					<td>".$this->price."</td>
					<td>".$this->displayProperties()."</td>
				</tr>
			";
		}
		public function edit($brand, $model, $category, $price, $properties){
			$this->brand=$brand;	
			$this->model=$model;
			$this->category=$category;
			$this->price=$price;
			$this->properties=$properties;	
		}
		public function getAsJSONObject(){
			return get_object_vars($this);
		}
		private function getPropertiesForCSV(){
			$result="[";
			foreach($this->properties as $key => $value) {
				$result.=  "'".addslashes($key) . "' => '" . addslashes($value)."'";
				$result.=",";
			}
			$result=substr_replace($result ,"", -1);
			$result.="]";
			return $result;
		}
		private function displayProperties(){
			$result='<i>Характеристики:</i></br>';
			foreach($this->properties as $key => $value) {
				$result.=  $key . ": " . $value;
			  	$result.=  "<br>";
			}
			return $result;
		}
		private function getPropertiesAsXML(){
			$result='';
			foreach($this->properties as $key => $value) {
			  	$result.="<property><key>".$key."</key><value>".$value."</value></property>";
			}
			return $result;
		}
		public function displayInfo(){
			return $this->id.". <b>".$this->brand." ".$this->model."</b></br>
			Ціна: ".$this->price."<br>
			Категорія: ".$this->category."<br>". $this->displayProperties();
		}
		public function __destruct(){
			echo "";	
		}
	}
	/*$bl=new BackpackList();
	$bl->importFromFile('backpacks.csv');
	echo $bl->convertToJSON();*/
	/*$bl->add('Daylite Plus', 'Osprey','Міські рюкзаки', '699',['Вага'=>'1.0','Об\'єм'=>'18']);
	$bl->exportToFile('backpacks.csv');
	$bl->showAll();*/
	/*$bl->add('Flare', 'Osprey','Міські рюкзаки', '999',['Вага'=>'1.1','Об\'єм'=>'20']);
	$bl->add('Kestrel', 'Osprey','Міські рюкзаки', '1999',['Вага'=>'1.6','Об\'єм'=>'65']);
	$bl->edit(2,'Kestrel', 'Osprey','Похідні рюкзаки', '1999',['Вага'=>'1.6','Об\'єм'=>'65']);
	$bl->add('Daylite Plus', 'Osprey','Міські рюкзаки', '699',['Вага'=>'1.0','Об\'єм'=>'18']);
	$bl->delete(1);
	$bl->showAll();*/
	//$nb1= new Backpack(1, 'Flare', 'Osprey','Міські рюкзаки', '999',['Вага'=>'1.1','Об\'єм'=>'20']);
	//echo $nb1->displayInfo();
	
	//$nc1= new Category(1, 'Міські рюкзаки');
	//echo $nc1->displayInfo();
	class PropertyList extends BaseList{
		public function add($name,$units){
			$id=++$this->index;
			$np=new Property($id,$name,$units);
			array_push($this->dataArray,$np);
			return $id;
		}
		public function getDataAsXML(){
			header("Content-type: text/xml");
			$result='<?xml version="1.0" encoding="UTF-8"?>
			<properties>';
			for ($i=0; $i<count($this->dataArray);$i++){
				$result.=$this->dataArray[$i]->getDataAsXML();
			}
			$result.='</properties>';
			return $result;
		}
		public function importFromFile($fileName){
			$row = 1;
			if (($handle = fopen($fileName, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$this->add($data[0],$data[1]);
				$row++;	
			}
			fclose($handle);
			}
		}
		public function edit($id,$name,$units){
			for ($i=0; $i<count($this->dataArray);$i++){
				if ($this->dataArray[$i]->getId()==$id){
					$this->dataArray[$i]->edit($name,$units);
					break;
				}
			}
		}
	}
	class Property{
		private $id;
		private $name;
		private $units;
		public function __construct($id, $name,$units){
			$this->id=$id;
			$this->name=$name;
			$this->units=$units;		
		}
		public function getId(){
			return $this->id;
		}
		public function getAsJSONObject(){
			return get_object_vars($this);
		}
		public function edit($name,$units){
			$this->name=$name;
			$this->units=$units;	
		}
		public function getDataAsXML(){
			return "
				<property>
					<id>".$this->id."</id>
					<name>".$this->name."</name>
					<units>".$this->units."</units>
				</property>
			";
		}
		public function getDataAsTableRow(){
			return "
				<tr>
					<td>".$this->id."</td>
					<td>".$this->name."</td>
					<td>".$this->units."</td>
				</tr>
			";
		}
		public function displayInfo(){
			return $this->id.". ".$this->name." <i>(".$this->units.")</i></br>";
		}
		public function getDataAsCSVRow(){
			return '"'.addslashes($this->name).'","'.addslashes($this->units).'"'."\n";
		}
		public function __destruct(){
			echo "";	
		}
	}

	/*$pl=new PropertyList();
	$pl->importFromFile('properties.csv');
	echo $pl->getDataAsXML();*/
	/*$pl->add('Довжина спинки','см');
	$pl->exportToFile('properties.csv');
	$pl->showAll();
	$pl->add('Вага','кг');
	$pl->add('Довжина спинки','см');
	$pl->edit(1,'Маса','кг');
	$pl->add('Об\'єм','л');
	$pl->delete(2);
	$pl->showAll();*/
	//$np1= new Property(1, 'Вага', 'кг');
	//echo $np1->displayInfo();
?>
