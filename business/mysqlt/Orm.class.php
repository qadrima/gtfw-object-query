<?php
/**
 * @author
 */
 
class Orm extends Database
{
	protected $_table_name;
	protected $_where;
	protected $_select = "*";
	protected $_join;
	protected $_orderBy;

    public function __construct($connectionNumber = 0)
    {
        parent::__construct($connectionNumber);
        $this->LoadSql('module/rfp.request/business/mysqlt/orm.sql.php');
        $this->SetDebugOn();
    }

    public function table($table)
    {
    	$this->_table_name = $table;
    	return $this;
    }

    public function insert($arrColumn, $arrData)
    {
    	$query = $this->mSqlQueries['insert'];

    	$query = str_replace(
    		'--table--', 
    		$this->_table_name, 
    		$query
    	);

    	$query = str_replace(
    		'--column_list--', 
    		'('. implode(',', $arrColumn) .')', 
    		$query
    	);

    	$value_list = '';
    	foreach ($arrData as $key => $val) 
    	{
    		if($value_list != ''){
    			$value_list .= ',';
    		}

    		$arr = array();
    		foreach ($val as $v) 
    		{
    			$v = $this->isStringFunction($v) ? $v : "'".$v."'";
    			array_push($arr, $v);
    		}

    		$value_list .= '('. implode(',', $arr) .')';
    	}

    	$query = str_replace(
    		'--value_list--', 
    		$value_list, 
    		$query
    	);

    	return $this->Execute($query, array());
    }

    public function where($column, $op, $val = false)
    {
    	if(!$val){
    		$val = $op;
    		$op = '=';
    	}

    	if(!$this->_where){
    		$this->_where = ' WHERE ';
    	} else {
    		$this->_where .= ' AND ';
    	}

    	$this->_where .= $column ." ". $op ." '".$val."'";

    	return $this;
    }

    public function orWhere($column, $op, $val = false)
    {
    	if(!$val){
    		$val = $op;
    		$op = '=';
    	}

    	if(!$this->_where){
    		$this->_where = ' WHERE ';
    	} else {
    		$this->_where .= ' OR ';
    	}

    	$this->_where .= $column ." ". $op ." '".$val."'";

    	return $this;
    }

    public function select()
    {
    	$this->_select = implode(',', func_get_args());
    	return $this;
    }

    public function get()
    {
    	$query = $this->query();
    	
    	return $this->Open($query, array());
    }

    public function first()
    {
    	$query = $this->query();
    	$query .= ' LIMIT 1';
    	$result = $this->Open($query, array());

    	return $result[0];
    }

    public function delete()
    {
    	$query = $this->mSqlQueries['delete'];
    	$query = str_replace('--table--', $this->_table_name, $query);
    	$query = str_replace('--where--', $this->_where, $query);

		return $this->Execute($query, array());
    }

    public function update($dataArr)
    {
    	$query = $this->mSqlQueries['update'];
    	$query = str_replace('--table--', $this->_table_name, $query);
    	$query = str_replace('--where--', $this->_where, $query);

    	$setArr = array();

    	foreach ($dataArr as $key => $val) 
    	{
    		array_push($setArr, $key . "='" . $val . "'");
    	}

    	$query = str_replace('--value_list--', implode(',', $setArr), $query);
    	
		return $this->Execute($query, array());
    }

    # JOIN function
    public function leftJoin($table, $column1, $condition, $column2)
    {
    	$this->_join .= ' LEFT JOIN '.$table.' ON '.$column1 . $condition . $column2;

    	return $this;
    }

    public function orderBy($column, $order = " ASC ")
    {
    	$this->_orderBy = " ORDER BY ".$column." ".$order;
    	return $this;
    }

    public function query()
    {
    	$query = $this->mSqlQueries['get'];
    	$query = str_replace('--table--', $this->_table_name, $query);
    	$query = str_replace('--select_list--', $this->_select, $query);
    	$query = str_replace('--join--', $this->_join, $query);
    	$query = str_replace('--where--', $this->_where, $query);
    	$query = str_replace('--order_by--', $this->_orderBy, $query);
    	return $query;
    }

    private function isStringFunction($val)
    {
    	return in_array($val, array(
    		'NOW()'
    	));
    }
}